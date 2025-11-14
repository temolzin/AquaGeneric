<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Debt;
use App\Models\Customer;
use App\Models\GeneralExpense;
use App\Models\User;
use App\Models\WaterConnection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller {
    public function index(Request $request) {
        $authUser = auth()->user();
        $query = Payment::with(['debt.customer.user'])->orderBy('id', 'desc')
            ->where('locality_id', $authUser->locality_id);

        if ($request->filled('name')) {
            $query->whereHas('debt.customer.user', function ($q) use ($request) {
                $q->whereRaw("CONCAT(users.name, ' ', users.last_name) LIKE ?", ['%' . $request->name . '%'])
                  ->orWhereRaw("CONCAT(users.last_name, ' ', users.name) LIKE ?", ['%' . $request->name . '%']);
            });
        }

        if ($request->filled('period')) {
            $periodParts = explode('/', $request->period);
            $monthName = strtolower(trim($periodParts[0]));
            $year = trim($periodParts[1]);

            $months = [
                'enero' => 1,
                'febrero' => 2,
                'marzo' => 3,
                'abril' => 4,
                'mayo' => 5,
                'junio' => 6,
                'julio' => 7,
                'agosto' => 8,
                'septiembre' => 9,
                'octubre' => 10,
                'noviembre' => 11,
                'diciembre' => 12
            ];

            $monthNumber = $months[$monthName] ?? null;

            if ($monthNumber && $year) {
                $query->whereHas('debt', function ($q) use ($year, $monthNumber) {
                    $q->whereYear('start_date', $year)
                        ->whereMonth('start_date', $monthNumber)
                        ->orWhere(function ($q) use ($year, $monthNumber) {
                            $q->whereYear('end_date', $year)
                                ->whereMonth('end_date', $monthNumber);
                        });
                });
            }
        }

        $payments = $query->paginate(10);
        $customers = Customer::with('user')->where('locality_id', $authUser->locality_id)->get();

        return view('payments.index', compact('payments', 'customers'));
    }

    public function getWaterConnectionsByCustomer(Request $request)
    {
        $authUser = auth()->user();
        $customerId = $request->input('waterCustomerId');
        $waterConnections = WaterConnection::where('customer_id', $customerId)
                                            ->where('locality_id', $authUser->locality_id)
                                            ->get()
                                            ->map(function ($waterConnection) {
                                                return [
                                                    'id' => $waterConnection->id,
                                                    'name' => $waterConnection->name,
                                                ];
                                            });

        return response()->json(['waterConnections' => $waterConnections]);
    }

    public function getDebtsByWaterConnection(Request $request) {
        $waterConnectionId = $request->input('water_connection_id');
        $debts = Debt::where('water_connection_id', $waterConnectionId)
                 ->where('status', '!=', 'paid')
                 ->orderBy('start_date', 'asc')
                 ->get()
                 ->map(function ($debt) {
                     $remainingAmount = $debt->amount - $debt->debt_current;
                     return [
                         'id' => $debt->id,
                         'start_date' => $debt->start_date,
                         'end_date' => $debt->end_date,
                         'amount' => $debt->amount,
                         'remaining_amount' => $remainingAmount,
                     ];
                 });

        return response()->json(['debts' => $debts]);
    }

    public function store(Request $request) {
        \Log::info('Request data:', $request->all());

        $authUser = auth()->user();
        $debt = Debt::findOrFail($request->debt_id);

        $debtStart = Carbon::parse($debt->start_date);
        $current = Carbon::now();
        $isFutureDebt = $debtStart->year > $current->year || ($debtStart->year == $current->year && $debtStart->month > $current->month);

        \Log::info('Debt start date:', ['start_date' => $debt->start_date, 'is_future_debt' => $isFutureDebt]);

        $isFuturePayment = $isFutureDebt;

        if ($isFutureDebt && !$request->has('is_future_payment')) {
            return redirect()->route('payments.index')
                ->with('error', 'La deuda seleccionada es de un periodo futuro. Debe marcarse como pago anticipado.');
        }

        if ($request->has('is_future_payment') && !$isFutureDebt) {
            return redirect()->route('payments.index')
                ->with('error', 'La deuda seleccionada no corresponde a un periodo futuro.');
        }

        $remainingAmount = $debt->amount - $debt->debt_current;

        if ($request->amount > $remainingAmount) {
            return redirect()->route('payments.index')
                ->with('error', 'El monto del pago supera la cantidad restante de la deuda.');
        }

        $payment = Payment::create([
            'customer_id' => $request->customer_id,
            'locality_id' => $authUser->locality_id,
            'created_by' => $authUser->id,
            'debt_id' => $request->debt_id,
            'method' => $request->method,
            'amount' => $request->amount,
            'note' => $request->note,
            'is_future_payment' => $isFuturePayment,
        ]);

        \Log::info('Payment created:', ['id' => $payment->id, 'is_future_payment' => $payment->is_future_payment]);

        $debt->debt_current += $request->amount;

        if ($debt->debt_current >= $debt->amount) {
            $debt->status = 'paid';
        } elseif ($debt->debt_current > 0) {
            $debt->status = 'partial';
        } else {
            $debt->status = 'pending';
        }

        $debt->save();

        return redirect()->route('payments.index')->with('success', 'Pago creado exitosamente.');
    }

    public function update(Request $request, Payment $payment) {
        $debt = $payment->debt;

        $previousAmount = $payment->amount;
        $remainingAmount = $debt->amount - $debt->debt_current + $previousAmount;

        if ($request->amount > $remainingAmount) {
            return redirect()->route('payments.index')
                ->with('error', 'El monto del pago supera la cantidad restante de la deuda.');
        }

        $debt->debt_current -= $previousAmount;
        $payment->update([
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        $debt->debt_current += $request->amount;

        if ($debt->debt_current >= $debt->amount) {
            $debt->status = 'paid';
        } elseif ($debt->debt_current > 0) {
            $debt->status = 'partial';
        } else {
            $debt->status = 'pending';
        }

        $debt->save();

        return redirect()->route('payments.index')->with('success', 'Pago actualizado exitosamnete.');
    }

    public function destroy($id) {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        if (!$payment) {
            return redirect()->back()->with('error', 'Pago no encontrado.');
        }

        return redirect()->route('payments.index')->with('success', 'Pago eliminado exitosamnete.');
    }

    public function annualEarningsReport($year)
    {
        $authUser = auth()->user();
        $year = intval($year);

        $monthlyEarnings = [];
        $totalEarnings = 0;

        for ($month = 1; $month <= 12; $month++) {
            $earnings = Payment::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('locality_id', $authUser->locality_id)
            ->sum('amount');

            $monthlyEarnings[$month] = $earnings;
            $totalEarnings += $earnings;
        }

        $payments = Payment::with(['debt.customer.user'])
            ->whereYear('created_at', $year)
            ->where('locality_id', $authUser->locality_id)
            ->get();

        $pdf = PDF::loadView('reports.annualEarnings', compact('monthlyEarnings', 'totalEarnings', 'year', 'authUser', 'payments'))
        ->setPaper('A4', 'portrait');

        return $pdf->stream('annual_earnings_' . $year . '.pdf');
    }

    public function weeklyEarningsReport(Request $request) {
        $authUser = auth()->user();
        $startDate = Carbon::parse($request->input('weekStartDate'));
        $endDate = Carbon::parse($request->input('weekEndDate'));

        $weeks = [];
        $currentStart = $startDate->copy();
        $totalPeriodEarnings = 0;

        while ($currentStart->lte($endDate)) {
            $currentEnd = $currentStart->copy()->endOfWeek();
            if ($currentEnd->gt($endDate)) {
                $currentEnd = $endDate;
            }

            $dailyEarnings = [];

            $day = $currentStart->copy();
            while ($day->lte($currentEnd)) {
                if ($day->between($startDate, $endDate)) {
                    $earnings = Payment::where('locality_id', $authUser->locality_id)
                        ->whereDate('created_at', $day->toDateString())
                        ->sum('amount');
                } else {
                    $earnings = 'N/A';
                }

                $dailyEarnings[$day->format('l')] = $earnings;
                $day->addDay();
            }

            $totalPeriodEarnings += array_sum($dailyEarnings);

            $weeks[] = [
                'start' => $currentStart->toDateString(),
                'end' => $currentEnd->toDateString(),
                'dailyEarnings' => $dailyEarnings,
            ];

            $currentStart = $currentEnd->copy()->addDay();
        }

        $pdf = PDF::loadView('reports.weeklyEarnings', compact('authUser', 'weeks', 'totalPeriodEarnings'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('weekly_earnings_' . now()->format('Ymd') . '.pdf');
    }

    public function receiptPayment($paymentId) {
        $decryptedId = Crypt::decrypt($paymentId);
        $payment = Payment::findOrFail($decryptedId);
        $debt = $payment->debt;
        $client = $debt->customer;

        $startDate = Carbon::parse($debt->start_date);
        $endDate = Carbon::parse($debt->end_date);

        $months = [
            'January' => ['month' => 'Enero', 'year' => null, 'amount' => null, 'note' => null],
            'February' => ['month' => 'Febrero', 'year' => null, 'amount' => null, 'note' => null],
            'March' => ['month' => 'Marzo', 'year' => null, 'amount' => null, 'note' => null],
            'April' => ['month' => 'Abril', 'year' => null, 'amount' => null, 'note' => null],
            'May' => ['month' => 'Mayo', 'year' => null, 'amount' => null, 'note' => null],
            'June' => ['month' => 'Junio', 'year' => null, 'amount' => null, 'note' => null],
            'July' => ['month' => 'Julio', 'year' => null, 'amount' => null, 'note' => null],
            'August' => ['month' => 'Agosto', 'year' => null, 'amount' => null, 'note' => null],
            'September' => ['month' => 'Septiembre', 'year' => null, 'amount' => null, 'note' => null],
            'October' => ['month' => 'Octubre', 'year' => null, 'amount' => null, 'note' => null],
            'November' => ['month' => 'Noviembre', 'year' => null, 'amount' => null, 'note' => null],
            'December' => ['month' => 'Diciembre', 'year' => null, 'amount' => null, 'note' => null],
        ];

        $numberOfMonths = $startDate->diffInMonths($endDate) + 1;

        if ($numberOfMonths === 1) {
            $monthName = $startDate->format('F');
            $months[$monthName]['year'] = $startDate->year;
            $months[$monthName]['amount'] = $payment->amount;
            $months[$monthName]['note'] = $payment->note;
        }

        $note = $payment->note;
        $message = $numberOfMonths > 1
            ? "Monto total del pago $" . number_format($payment->amount, 2) .
            ". Nota: " . $note
            : null;

        $pdf = PDF::loadView('reports.receiptPayment', compact('payment', 'months', 'message'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('comprobante_de_pago.pdf');
    }

    public function clientPaymentReport(Request $request) {
        $customerId = $request->input('customerId');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $customer = Customer::with('user')->findOrFail($customerId);
        $authUser = auth()->user();

        $payments = Payment::whereHas('debt.waterConnection', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();

        $totalPayments = $payments->sum('amount');

        $pdf = PDF::loadView('reports.clientPayments', compact('customer', 'startDate', 'endDate', 'payments', 'authUser', 'totalPayments'))
        ->setPaper('A4', 'portrait');

        return $pdf->stream('reporte_pagos_cliente.pdf');
    }

    public function waterConnectionPaymentsReport(Request $request) {
        $customerId = $request->input('waterCustomerId');
        $waterConnectionId = $request->input('waterConnectionId');
        $startDate = $request->input('waterStartDate');
        $endDate = $request->input('waterEndDate');

        $customer = Customer::with('user')->findOrFail($customerId);
        $authUser = auth()->user();

        $waterConnection = WaterConnection::where('id', $waterConnectionId)
                                        ->where('customer_id', $customerId)
                                        ->firstOrFail();

        $payments = Payment::select('id', 'debt_id', 'amount', 'created_at')
            ->where('locality_id', $authUser->locality_id)
            ->whereHas('debt', function ($query) use ($waterConnectionId) {
                $query->where('water_connection_id', $waterConnectionId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('debt_id');

        $totalPayments = $payments->flatMap(function ($group) {
            return $group;
        })->sum('amount');

        $pdf = PDF::loadView('reports.waterConnectionPayments', compact('customer', 'waterConnection', 'startDate', 'endDate', 'payments', 'authUser', 'totalPayments'))
                ->setPaper('A4', 'portrait');

        return $pdf->stream('reporte_pagos_tomas.pdf');
    }

    public function cashClosurePaymentsReport() {
        $authUser = auth()->user();
        $today = now()->toDateString();

        $payments = Payment::where('locality_id', $authUser->locality_id)
            ->whereDate('created_at', $today)
            ->get();

        $expenses = GeneralExpense::where('locality_id', $authUser->locality_id)
            ->whereDate('expense_date', $today)
            ->get();

        $totalPayments = $payments->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $totalCash = $payments->where('method', 'cash')->sum('amount');
        $totalCard = $payments->where('method', 'card')->sum('amount');
        $totalTransfer = $payments->where('method', 'transfer')->sum('amount');
        $finalAmount = $totalPayments - $totalExpenses;

        $latestClosure = (object)[
            'opened_at'      => now()->startOfDay(),
            'closed_at'      => now()->endOfDay(),
            'initial_amount' => 0,
            'final_amount'   => $finalAmount,
            'total_sales'    => $totalPayments,
            'total_expenses' => $totalExpenses,
        ];

        $pdf = PDF::loadView('reports.pdfCashClosures', [
            'closures'       => collect([$latestClosure]),
            'payments'       => $payments,
            'expenses'       => $expenses,
            'authUser'       => $authUser,
            'totalPayments'  => $totalPayments,
            'totalExpenses'  => $totalExpenses,
            'totalCash'      => $totalCash,
            'totalCard'      => $totalCard,
            'totalTransfer'  => $totalTransfer,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('reporte_corte_caja.pdf');
    }

    public function showCustomerPayments(Request $request)
    {
        $authUser = auth()->user();
        $customer = Customer::where('user_id', $authUser->id)
                        ->where('locality_id', $authUser->locality_id)
                        ->first();

        if (!$customer) {
            $payments = Payment::where('id', 0)->paginate(10);
            return view('viewCustomerPayments.index', compact('payments', 'customer'));
        }

        $query = Payment::with(['debt.waterConnection'])
                    ->where('customer_id', $customer->id)
                    ->where('locality_id', $authUser->locality_id)
                    ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                ->orWhere('method', 'LIKE', "%{$search}%")
                ->orWhere(function($q) use ($search) {
                    $methodMappings = [
                        'efectivo' => 'cash',
                        'transferencia' => 'transfer',
                        'tarjeta' => 'card'
                    ];

                    foreach ($methodMappings as $spanish => $english) {
                        if (stripos($search, $spanish) !== false) {
                            $q->orWhere('method', $english);
                        }
                    }
                })
                ->orWhereHas('debt.waterConnection', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        $payments = $query->paginate(10);

        return view('viewCustomerPayments.index', compact('payments', 'customer'));
    }

    public function quarterlyReport(Request $request)
    {
        try {
            $customer = Customer::where('user_id', Auth::id())->first();

            if (!$customer) {
                return redirect()->back()->with('error', 'No se encontró información del cliente.');
            }

            $year = $request->input('year');
            $quarter = $request->input('quarter');

            if (!$year || !$quarter) {
                return redirect()->back()->with('error', 'Debe seleccionar año y trimestre.');
            }

            $dateRange = $this->getQuarterDateRange($year, $quarter);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];

            $waterConnections = WaterConnection::where('customer_id', $customer->id)->get();

            $paymentsByWaterConnection = [];
            $totalGeneral = 0;

            foreach ($waterConnections as $waterConnection) {
                $payments = Payment::whereHas('debt', function($query) use ($waterConnection) {
                        $query->where('water_connection_id', $waterConnection->id);
                    })
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->with(['debt.waterConnection'])
                    ->get()
                    ->groupBy('debt_id');

                $subtotalToma = $payments->flatten()->sum('amount');
                $totalGeneral += $subtotalToma;

                $paymentsByWaterConnection[$waterConnection->id] = [
                    'water_connection' => $waterConnection,
                    'payments' => $payments,
                    'subtotal' => $subtotalToma
                ];
            }

            $authUser = Auth::user();

            $data = [
                'paymentsByWaterConnection' => $paymentsByWaterConnection,
                'customer' => $customer,
                'authUser' => $authUser,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'year' => $year,
                'quarter' => $quarter,
                'totalGeneral' => $totalGeneral,
                'quarterName' => $this->getQuarterName($quarter)
            ];

            $pdf = PDF::loadView('reports.customerPaymentsReport', $data)
                    ->setPaper('a4', 'portrait');

            return $pdf->stream("reporte-trimestral-{$year}-T{$quarter}.pdf");

        } catch (\Exception $e) {
            \Log::error('Error en quarterlyReport: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function getQuarterDateRange($year, $quarter)
    {
        switch ($quarter) {
            case 1:
                $start = Carbon::create($year, 1, 1)->startOfDay();
                $end = Carbon::create($year, 3, 31)->endOfDay();
                break;
            case 2:
                $start = Carbon::create($year, 4, 1)->startOfDay();
                $end = Carbon::create($year, 6, 30)->endOfDay();
                break;
            case 3:
                $start = Carbon::create($year, 7, 1)->startOfDay();
                $end = Carbon::create($year, 9, 30)->endOfDay();
                break;
            case 4:
                $start = Carbon::create($year, 10, 1)->startOfDay();
                $end = Carbon::create($year, 12, 31)->endOfDay();
                break;
            default:
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
        }

        return [
            'start' => $start,
            'end' => $end
        ];
    }

    private function getQuarterName($quarter)
    {
        $names = [
            1 => 'Primer Trimestre (Enero - Marzo)',
            2 => 'Segundo Trimestre (Abril - Junio)',
            3 => 'Tercer Trimestre (Julio - Septiembre)',
            4 => 'Cuarto Trimestre (Octubre - Diciembre)'
        ];

        return $names[$quarter] ?? 'Trimestre Desconocido';
    }
}
