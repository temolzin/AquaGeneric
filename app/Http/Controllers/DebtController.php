<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\Payment;
use App\Models\WaterConnection;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $authUser = auth()->user();
        $localityId = $authUser->locality_id;

        $customers = Customer::where('customers.locality_id', $localityId)
                    ->select('customers.id', 'customers.name', 'customers.last_name', 'customers.locality_id')
                    ->where(function ($query) use ($search) {
                        $query->where('customers.id', 'like', "%{$search}%")
                            ->orWhere('customers.name', 'like', "%{$search}%")
                            ->orWhere('customers.last_name', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(customers.name, ' ', customers.last_name) LIKE ?", ["%{$search}%"]);
                    })
                    ->groupBy('customers.id', 'customers.name', 'customers.last_name', 'customers.locality_id')
                    ->get();

        $waterConnections = WaterConnection::with('customer')
        ->where('locality_id', $localityId)
        ->get();

        $debts = Debt::with('waterConnection.customer', 'creator')
            ->whereHas('waterConnection', function ($query) use ($search, $localityId) {
                $query->where('locality_id', $localityId)
                    ->whereHas('customer', function ($query) use ($search) {
                        $query->where(function ($query) use ($search) {
                            $query->where('id', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                        });
                    });
            })
            ->selectRaw('water_connection_id, debts.created_at, SUM(amount) as total_amount')
            ->where('status', '!=', 'paid')
            ->groupBy('water_connection_id', 'debts.created_at')
            ->orderByDesc('debts.created_at')
            ->paginate(10);

        $totalDebts = [];
        foreach ($debts as $debt) {
            $customerId = $debt->waterConnection->customer_id;
            if (!isset($totalDebts[$customerId])) {
                $totalDebts[$customerId] = 0;
            }

            $totalDebtAmount = Debt::where('water_connection_id', $debt->water_connection_id)->sum('amount');
            $totalDebtPaid = Debt::where('water_connection_id', $debt->water_connection_id)->sum('debt_current');
            $totalDebts[$customerId] = $totalDebtAmount - $totalDebtPaid;
        }

        return view('debts.index', compact('debts', 'customers', 'waterConnections', 'totalDebts'));
    }

    public function getWaterConnections(Request $request)
    {
        $authUser = auth()->user();
        $customerId = $request->input('customer_id');
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

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $waterConnection = WaterConnection::findOrFail($request->water_connection_id);

        $startMonth = $request->input('start_date');
        $endMonth = $request->input('end_date');

        $startDate = new \DateTime($startMonth . '-01');
        $endDate = (new \DateTime($endMonth . '-01'))->modify('last day of this month');

        $existingDebt = Debt::where('water_connection_id', $request->input('water_connection_id'))
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate->format('Y-m-d'))
                      ->where('end_date', '>=', $startDate->format('Y-m-d'));
            })
            ->exists();

        if ($existingDebt) {
            return response()->json(['error' => 'El cliente ya tiene una deuda en este rango de fechas.'], 400);
        }

        Debt::create([
            'locality_id' => $authUser->locality_id,
            'created_by' => $authUser->id,
            'water_connection_id' => $request->water_connection_id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'amount' => $request->input('amount'),
            'note' => $request->input('note'),
        ]);

        return response()->json(['success' => 'Deuda creada exitosamente.'], 400);
    }

    public function assignAll(Request $request)
    {
        $authUser = auth()->user();
        $customers = Customer::with('waterConnections')
            ->where('locality_id', $authUser->locality_id)
            ->get();

        $startMonth = $request->input('start_date');
        $endMonth = $request->input('end_date');

        $startDate = new \DateTime($startMonth . '-01');
        $endDate = (new \DateTime($endMonth . '-01'))->modify('first day of next month')->modify('-1 day');

        $note = $request->note ?? 'Deuda asignada manualmente';

        $allHaveDebt = true;

        foreach ($customers as $customer) {
            foreach ($customer->waterConnections as $waterConnection) {
                $cost = $waterConnection->cost;

                if (!$cost || !$cost->price) {
                    continue;
                }

                $existingDebt = Debt::where('water_connection_id', $waterConnection->id)
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $endDate->format('Y-m-d'))
                            ->where('end_date', '>=', $startDate->format('Y-m-d'));
                    })
                    ->exists();

                if ($existingDebt) {
                    continue;
                }

                $allHaveDebt = false;
                Debt::create([
                    'locality_id' => $authUser->locality_id,
                    'created_by' => $authUser->id,
                    'water_connection_id' => $waterConnection->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'amount' => $cost->price,
                    'note' => $note,
                ]);
            }
        }

        if ($allHaveDebt) {
            return redirect()->back()->with('error', 'Todas las tomas de agua de los clientes ya tienen deudas asignadas para el período especificado.');
        }

        return redirect()->back()->with('success', 'Deudas asignadas exitosamente a todas las tomas de agua de los clientes.');
    }

    public function destroy($id, Request $request)
    {
        $debt = Debt::find($id);

        if (!$debt) {
            return redirect()->back()->with('error', 'Deuda no encontrada.');
        }

        $debt->delete();

        return redirect()->back()->with('success', 'Deuda eliminada con éxito.')
            ->with('modal_id', $request->input('modal_id'));
    }
}
