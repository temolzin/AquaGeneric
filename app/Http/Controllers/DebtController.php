<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\WaterConnection;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $localityId = auth()->user()->locality_id;

        $customers = Customer::where('locality_id', $localityId)->get();

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
            ->where('status', '!=', 'paid')
            ->select('water_connection_id')
            ->groupBy('water_connection_id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->selectRaw('SUM(amount) as total_amount')
            ->paginate(10);

            $totalDebts = [];
            foreach ($debts as $debt) {
                $customerId = $debt->waterConnection->customer_id;
                if (!isset($totalDebts[$customerId])) {
                    $totalDebts[$customerId] = 0;
                }
                $totalDebts[$customerId] += $debt->total_amount;
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
            return redirect()->back()->with('error', 'El Usuario ya tiene una deuda en este rango de fechas.')->withInput();
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

        return redirect()->route('debts.index')->with('success', 'Deuda creada exitosamente.');
    }

    public function assignAll(Request $request)
    {
        $authUser = auth()->user();
        $customers = Customer::with('waterConnection', 'cost')
            ->where('locality_id', $authUser->locality_id)
            ->get();

        $startMonth = $request->input('start_date');
        $endMonth = $request->input('end_date');

        $startDate = new \DateTime($startMonth . '-01');
        $endDate = (new \DateTime($endMonth . '-01'))->modify('last day of this month');

        $note = $request->note ?? 'Deuda asignada manualmente';
        $allHaveDebt = true;

        foreach ($customers as $customer) {
            $waterConnection = $customer->waterConnection;

            if (!$waterConnection) {
                continue;
            }

            $cost = $customer->cost;

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

        if ($allHaveDebt) {
            return redirect()->back()->with('error', 'Ya todos los usuarios tienen la deuda del periodo.');
        }

        return redirect()->back()->with('success', 'Deudas asignadas a todos los Usuarios.');
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
