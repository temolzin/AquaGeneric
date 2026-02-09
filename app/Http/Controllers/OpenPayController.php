<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Debt;
use App\Services\OpenPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OpenPayController extends Controller
{
    protected $openPayService;

    public function __construct(OpenPayService $openPayService)
    {
        $this->openPayService = $openPayService;
    }

    /**
     * Mostrar formulario de pago
     */
    public function showPaymentForm($debtId)
    {
        try {
            $debt = Debt::with(['customer', 'waterConnection', 'payments'])->findOrFail($debtId);
            
            // Calcular el monto pendiente real basado en los pagos existentes
            $remainingAmount = $debt->remaining_amount;
            
            // Verificar que la deuda no esté pagada
            if ($debt->isPaid() || $remainingAmount <= 0) {
                return redirect()->back()->with('error', 'Esta deuda ya está pagada');
            }

            return view('payments.openpay-form', [
                'debt' => $debt,
                'customer' => $debt->customer,
                'remainingAmount' => $remainingAmount,
                'totalPaid' => $debt->total_paid,
                'merchantId' => config('openpay.merchant_id'),
                'publicKey' => config('openpay.public_key'),
                'sandbox' => config('openpay.sandbox'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error mostrando formulario de pago: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario de pago');
        }
    }

    /**
     * Procesar pago con token
     */
    public function processPayment(Request $request)
    {
        Log::info('Procesando pago OpenPay', $request->all());

        $validator = Validator::make($request->all(), [
            'token_id' => 'required|string',
            'device_session_id' => 'required|string',
            'debt_id' => 'required|exists:debts,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            Log::error('Validación fallida', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $debt = Debt::with(['customer', 'payments', 'waterConnection'])->findOrFail($request->debt_id);
            
            // Calcular el monto pendiente real basado en los pagos existentes
            $remainingAmount = $debt->remaining_amount;
            
            // Verificar que el monto no exceda la deuda pendiente real
            if ($request->amount > $remainingAmount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => "El monto excede la deuda pendiente. Monto máximo: $" . number_format($remainingAmount, 2),
                ], 400);
            }

            // Generar ID de orden único
            $orderId = 'DEBT-' . $debt->id . '-' . time();

            Log::info('Llamando a OpenPayService->chargeWithToken');

            // Preparar datos del cliente para OpenPay
            $customerData = [
                'name' => $debt->customer->name ?? 'Cliente',
                'last_name' => $debt->customer->last_name ?? '',
                'email' => $debt->customer->email ?? 'cliente@aquacontrol.com',
                'phone_number' => $debt->customer->phone ?? null,
            ];

            // Procesar cargo
            $result = $this->openPayService->chargeWithToken(
                $request->token_id,
                $request->amount,
                "Pago de deuda #{$debt->id} - {$debt->customer->name} {$debt->customer->last_name}",
                $orderId,
                $customerData,
                $request->device_session_id
            );

            if (!$result['success']) {
                DB::rollBack();
                Log::error('Error en cargo OpenPay', $result);
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Error desconocido',
                    'error_code' => $result['error_code'] ?? null,
                ], 400);
            }

            // Obtener el customer_id desde la toma de agua
            $customerId = $debt->waterConnection->customer_id ?? $debt->customer->id ?? null;
            
            if (!$customerId) {
                DB::rollBack();
                Log::error('No se pudo obtener customer_id para el pago', ['debt_id' => $debt->id]);
                return response()->json([
                    'success' => false,
                    'error' => 'No se pudo identificar al cliente para registrar el pago',
                ], 400);
            }

            // Crear registro de pago
            $payment = Payment::create([
                'customer_id' => $customerId,
                'debt_id' => $debt->id,
                'locality_id' => $debt->locality_id,
                'created_by' => auth()->id() ?? $debt->created_by,
                'amount' => $request->amount,
                'method' => 'openpay',
                'openpay_transaction_id' => $result['transaction_id'],
                'openpay_order_id' => $orderId,
                'openpay_authorization' => $result['authorization'],
                'openpay_status' => 'completed',
                'openpay_card_data' => $result['card'],
                'openpay_processed_at' => now(),
                'note' => $request->note ?? "Pago procesado con OpenPay",
            ]);

            // Recalcular el monto pendiente después de registrar el pago
            $debt->refresh();
            $newRemainingAmount = $debt->remaining_amount;
            
            // Sincronizar debt_current con el monto real pendiente
            $debt->debt_current = $newRemainingAmount;
            
            // Actualizar estado de la deuda
            if ($newRemainingAmount <= 0) {
                $debt->status = 'paid';
                $debt->debt_current = 0;
            } elseif ($newRemainingAmount < $debt->amount) {
                $debt->status = 'partial';
            }
            $debt->save();

            DB::commit();

            Log::info('Pago procesado exitosamente', [
                'payment_id' => $payment->id,
                'transaction_id' => $result['transaction_id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado exitosamente',
                'payment_id' => $payment->id,
                'transaction_id' => $result['transaction_id'],
                'authorization' => $result['authorization'],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing OpenPay payment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Endpoint para VERIFICAR el webhook (código de verificación)
     */
    public function verifyWebhook(Request $request)
    {
        Log::info('===== VERIFICACIÓN DE WEBHOOK =====', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'params' => $request->all(),
            'body' => $request->getContent(),
            'ip' => $request->ip()
        ]);

        // OpenPay envía un código de verificación en el parámetro 'verification_code'
        $verificationCode = $request->input('verification_code');
        
        if ($verificationCode) {
            Log::info('✅ CÓDIGO DE VERIFICACIÓN RECIBIDO: ' . $verificationCode);
            
            // Devolver el código tal cual lo recibimos (esto verifica el webhook)
            return response()->json([
                'verification_code' => $verificationCode
            ], 200);
        }

        // Si no hay código de verificación, responder con éxito general
        Log::info('Sin código de verificación - webhook verificado');
        return response()->json([
            'status' => 'webhook endpoint active',
            'message' => 'Endpoint de webhook configurado correctamente',
            'timestamp' => now()->toIso8601String()
        ], 200);
    }

    /**
     * Webhook para recibir y PROCESAR notificaciones de OpenPay
     */
    public function webhook(Request $request)
    {
        Log::info('===== WEBHOOK OPENPAY RECIBIDO =====', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'raw_content' => $request->getContent()
        ]);

        // Verificar la firma del webhook
        if (!$this->openPayService->verifyWebhook($request)) {
            Log::warning('⚠️ Webhook OpenPay no autorizado - firma inválida');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->all();

        try {
            $type = $data['type'] ?? null;
            $transaction = $data['transaction'] ?? null;

            if (!$transaction || !isset($transaction['id'])) {
                Log::error('Webhook sin datos de transacción válidos');
                return response()->json(['error' => 'Invalid data'], 400);
            }

            $payment = Payment::where('openpay_transaction_id', $transaction['id'])->first();

            if (!$payment) {
                Log::warning('Pago no encontrado para transacción: ' . $transaction['id']);
                return response()->json(['status' => 'payment not found'], 404);
            }

            // Procesar diferentes tipos de eventos
            switch ($type) {
                case 'charge.succeeded':
                    $payment->openpay_status = 'completed';
                    $payment->openpay_processed_at = now();
                    Log::info("✅ Pago #{$payment->id} completado exitosamente");
                    break;

                case 'charge.failed':
                    $payment->openpay_status = 'failed';
                    $payment->openpay_error_message = $transaction['error_message'] ?? 'Error desconocido';
                    
                    // Revertir actualización de deuda
                    $debt = $payment->debt;
                    $debt->debt_current += $payment->amount;
                    if ($debt->debt_current >= $debt->amount) {
                        $debt->status = 'pending';
                    } else {
                        $debt->status = 'partial';
                    }
                    $debt->save();
                    
                    Log::warning("❌ Pago #{$payment->id} falló: " . $payment->openpay_error_message);
                    break;

                case 'charge.cancelled':
                    $payment->openpay_status = 'cancelled';
                    Log::info("🚫 Pago #{$payment->id} cancelado");
                    break;

                case 'charge.refunded':
                    $payment->openpay_status = 'refunded';
                    
                    // Revertir deuda
                    $debt = $payment->debt;
                    $debt->debt_current += $payment->amount;
                    if ($debt->debt_current >= $debt->amount) {
                        $debt->status = 'pending';
                    } else {
                        $debt->status = 'partial';
                    }
                    $debt->save();
                    
                    Log::info("💰 Pago #{$payment->id} reembolsado");
                    break;

                default:
                    Log::info("ℹ️ Evento webhook no manejado: {$type}");
            }

            $payment->save();

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Error procesando webhook OpenPay: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Realizar reembolso
     */
    public function refund(Request $request, $paymentId)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $payment = Payment::findOrFail($paymentId);

            if (!$payment->isOpenPayPayment()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Este pago no fue procesado con OpenPay',
                ], 400);
            }

            if (!$payment->isOpenPayCompleted()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo se pueden reembolsar pagos completados',
                ], 400);
            }

            $result = $this->openPayService->refund(
                $payment->openpay_transaction_id,
                $request->description ?? 'Reembolso de pago',
                $request->amount
            );

            if (!$result['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 400);
            }

            // Actualizar pago
            $payment->openpay_status = 'refunded';
            $payment->save();

            // Actualizar deuda
            $debt = $payment->debt;
            $refundAmount = $request->amount ?? $payment->amount;
            $debt->debt_current += $refundAmount;
            
            if ($debt->debt_current >= $debt->amount) {
                $debt->status = 'pending';
            } else {
                $debt->status = 'partial';
            }
            $debt->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reembolso procesado exitosamente',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing refund: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al procesar el reembolso: ' . $e->getMessage(),
            ], 500);
        }
    }
}
