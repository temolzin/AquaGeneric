<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaterConnection;
use App\Models\Customer;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class AdvancePaymentController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = Payment::with(['debt.customer', 'debt.waterConnection'])
            ->whereHas('debt', function ($q) {
                $q->where('end_date', '>', now())
                    ->where('status', '!=', 'pending');
            })
            ->where('locality_id', $authUser->locality_id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('name')) {
            $query->whereHas('debt.customer', function ($q) use ($request) {
                $q->whereRaw("CONCAT(customers.name, ' ', customers.last_name) LIKE ?", ['%' . $request->name . '%'])
                    ->orWhereRaw("CONCAT(customers.last_name, ' ', customers.name) LIKE ?", ['%' . $request->name . '%']);
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
        $customers = Customer::where('locality_id', $authUser->locality_id)->get();

        return view('advancePayments.index', compact('payments', 'customers'));
    }

    public function create() {}

    public function store(Request $request) {}

    public function show($id) {}

    public function edit($id) {}

    public function update(Request $request, $id) {}

    public function destroy($id) {}

    public function report(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'water_connection_id' => 'required|exists:water_connections,id'
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $authUser = auth()->user();

        $waterConnection = WaterConnection::where('id', $request->water_connection_id)
            ->where('customer_id', $request->customer_id)
            ->firstOrFail();

        $payments = Payment::with(['debt', 'customer'])
            ->whereHas('debt', function ($query) {
                $query->where('end_date', '>', now())
                    ->where('status', '!=', 'pending');
            })
            ->where('customer_id', $customer->id)
            ->whereHas('debt', function ($query) use ($waterConnection) {
                $query->where('water_connection_id', $waterConnection->id);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('debt_id');

        $totalPayments = $payments->flatten()->sum('amount');

        $pdf = PDF::loadView('reports.advancedPayments', [
            'customer' => $customer,
            'waterConnection' => $waterConnection,
            'payments' => $payments,
            'totalPayments' => $totalPayments,
            'authUser' => $authUser
        ])->setPaper('a4', 'portrait');

        $fileName = 'pagos_adelantados_' . $customer->id . '_' . $waterConnection->id . '.pdf';

        return $pdf->stream($fileName);
    }
}
