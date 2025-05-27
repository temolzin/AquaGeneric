<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Payment; 
use App\Models\WaterConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AdvancePaymentController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        $now = Carbon::now()->startOfMonth();

        $customers = DB::table('customers')
            ->join('payments', 'customers.id', '=', 'payments.customer_id')
            ->join('debts', 'payments.debt_id', '=', 'debts.id')
            ->where('debts.status', 'paid')
            ->where('debts.end_date', '>', $now)
            ->select('customers.id', 'customers.name', 'customers.last_name')
            ->distinct()
            ->get();
 
        $payments = Payment::with('debt.customer')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('advancePayments.index', compact('payments', 'customers'));
       
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

    public function getAdvanceWaterConnections(Request $request)
    {
    $authUser = auth()->user();
    $customerId = $request->input('customer_id');

    $waterConnections = WaterConnection::where('customer_id', $customerId)
        ->where('locality_id', $authUser->locality_id)
        ->whereHas('debts', function ($query) {
            $query->where('status', 'paid')
                  ->whereDate('end_date', '>', now());
        })
        ->get()
        ->map(function ($connection) {
            return [
                'id' => $connection->id,
                'name' => $connection->name,
            ];
        });

    return response()->json(['waterConnections' => $waterConnections]);
    }


    public function getAdvanceDebtDates(Request $request)
    {
        $customerId = $request->input('customer_id');
        $connectionId = $request->input('water_connection_id');

        Log::info('getAdvanceDebtDates', [
            'customer_id' => $customerId,
            'water_connection_id' => $connectionId
        ]);

        $now = Carbon::now()->startOfMonth();

        $debt = DB::table('debts')
            ->join('payments', 'debts.id', '=', 'payments.debt_id')
            ->join('water_connections', 'debts.water_connection_id', '=', 'water_connections.id')
            ->where('debts.status', 'paid')
            ->where('water_connections.customer_id', $customerId)
            ->where('debts.water_connection_id', $connectionId)
            ->where('debts.end_date', '>', $now)
            ->orderBy('debts.start_date', 'asc')
            ->select('debts.start_date', 'debts.end_date')
            ->first();

        if ($debt) {
            return response()->json([
                'start_date' => $debt->start_date,
                'end_date' => $debt->end_date,
            ]);
        } 
    }
}
