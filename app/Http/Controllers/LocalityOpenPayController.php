<?php

namespace App\Http\Controllers;

use App\Models\Locality;
use App\Models\OpenPayWebhookVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class LocalityOpenPayController extends Controller
{
    public function edit(Locality $locality)
    {
        return view('localities.openpayConfig', compact('locality'));
    }

    public function update(Request $request, Locality $locality)
    {
        $validator = Validator::make($request->all(), [
            'openpay_merchant_id' => 'nullable|string|max:255',
            'openpay_private_key' => 'nullable|string|max:500',
            'openpay_public_key' => 'nullable|string|max:255',
            'openpay_webhook_user' => 'nullable|string|max:255',
            'openpay_webhook_password' => 'nullable|string|max:255',
            'openpay_sandbox' => 'boolean',
            'openpay_enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'openpay_merchant_id' => $request->openpay_merchant_id,
                'openpay_public_key' => $request->openpay_public_key,
                'openpay_webhook_user' => $request->openpay_webhook_user,
                'openpay_webhook_password' => $request->openpay_webhook_password,
                'openpay_sandbox' => $request->boolean('openpay_sandbox'),
                'openpay_enabled' => $request->boolean('openpay_enabled'),
            ];

            if ($request->filled('openpay_private_key')) {
                $data['openpay_private_key'] = $request->openpay_private_key;
            }

            $locality->update($data);

            return redirect()->route('localities.index')
                ->with('success', 'Configuración de OpenPay actualizada correctamente para ' . $locality->name);

        } catch (\Exception $e) {
            Log::error('Error actualizando configuración OpenPay: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al guardar la configuración: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function testConnection(Locality $locality)
    {
        if (!$locality->hasOpenPayEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'La localidad no tiene credenciales de OpenPay configuradas.',
            ]);
        }

        try {
            $service = \App\Services\OpenPayService::forLocality($locality);
            
            $result = $service->testCredentials();
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conexión exitosa con OpenPay.',
                    'merchant_id' => $service->getMerchantId(),
                    'sandbox' => $service->isSandbox(),
                    'details' => $result['details'] ?? null,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Error desconocido al conectar con OpenPay.',
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al conectar con OpenPay: ' . $e->getMessage(),
            ]);
        }
    }

    public function getWebhookUrl(Locality $locality)
    {
        $webhookUrl = route('openpay.webhook');
        
        return response()->json([
            'webhook_url' => $webhookUrl,
            'verify_url' => route('openpay.webhook.verify'),
            'locality_id' => $locality->id,
            'instructions' => 'Configura esta URL en tu panel de OpenPay. El webhook es único para todas las localidades y el sistema identifica automáticamente la localidad por la transacción.',
        ]);
    }

    public function webhookVerifications()
    {
        $verifications = OpenPayWebhookVerification::orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('localities.webhookVerifications', compact('verifications'));
    }

    public function webhookVerificationsApi()
    {
        $verifications = OpenPayWebhookVerification::orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'verifications' => $verifications,
        ]);
    }

    public function deleteVerification($id)
    {
        $verification = OpenPayWebhookVerification::find($id);
        
        if ($verification) {
            $verification->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
    }

    public function clearVerifications()
    {
        OpenPayWebhookVerification::truncate();
        return response()->json(['success' => true, 'message' => 'Todos los códigos han sido eliminados']);
    }
}
