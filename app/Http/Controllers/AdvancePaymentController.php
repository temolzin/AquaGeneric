<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Payment; 
use App\Models\Debt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdvancePaymentController extends Controller
{
    public function index()
    {
        return view('advancePayments.index');
    }

    public function create()
    {
    
    }

    public function store(Request $request)
    {
    
    }

    public function show($id)
    {
    
    }

    public function edit($id)
    {
    
    }

    public function update(Request $request, $id)
    {
    
    }

    public function destroy($id)
    {
    
    }
    
    public function getCustomersWithAdvancePayments(Request $request)
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
    
        $paymentsQuery = Payment::with(['customer', 'debt.waterConnection'])
            ->whereHas('debt', function($debtQuery) use ($currentMonthStart) {
                $debtQuery->where('status', Debt::STATUS_PAID)
                    ->where('end_date', '>', $currentMonthStart);
            });

        $customerId = $request->input('customerId');

        if (!$customerId) {
            $customers = $paymentsQuery->get()
                ->pluck('customer')
                ->unique('id')
                ->values();
            return response()->json(['customers' => $customers]);
        }

        $connections = $paymentsQuery->whereHas('customer', function($customerQuery) use ($request) {
            $customerQuery->where('id', $request->customerId);
        })
            ->get()
            ->pluck('debt.waterConnection')
            ->unique('id')
            ->values();

        return response()->json(['waterConnections' => $connections]);
    }

    public function getAdvanceDebtDates(Request $request)
    {
        $connectionId = $request->input('waterConnectionId');

        Log::info('getAdvanceDebtDates', [
            'waterConnectionId' => $connectionId
        ]);

        $now = Carbon::now()->startOfMonth();

        $debt = DB::table('debts')
            ->join('payments', 'debts.id', '=', 'payments.debt_id')
            ->where('debts.status', 'paid')
            ->where('debts.water_connection_id', $connectionId) 
            ->where('debts.end_date', '>', $now)
            ->orderBy('debts.start_date', 'asc')
            ->select('debts.start_date', 'debts.end_date')
            ->first();

        return $debt 
            ? response()->json(['start_date' => $debt->start_date,'end_date' => $debt->end_date,]) : response();
    }
}
