<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreDebtRequest;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\WaterConnection;
use App\Models\MovementHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\DebtCategory;
use Carbon\Carbon;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $authUser = auth()->user();
        $localityId = $authUser->locality_id;

        $customers = Customer::where('locality_id', $localityId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            })
            ->get();

        $waterConnections = WaterConnection::with('customer')
            ->where('locality_id', $localityId)
            ->get();

        $debts = Debt::with(['waterConnection.customer', 'creator'])
            ->whereHas('waterConnection', function ($query) use ($localityId) {
                $query->where('locality_id', $localityId);
            })
            ->where('status', '!=', 'paid')
            ->orderByDesc('created_at')
            ->paginate(10);

        $debtCategories = DebtCategory::all();

        return view('debts.index', compact(
            'debts',
            'customers',
            'waterConnections',
            'debtCategories'
        ));
    }

    public function getWaterConnections(Request $request)
    {
        $authUser = auth()->user();

        $waterConnections = WaterConnection::where('customer_id', $request->customer_id)
            ->where('locality_id', $authUser->locality_id)
            ->get();

        return response()->json([
            'waterConnections' => $waterConnections->map(fn($wc) => [
                'id' => $wc->id,
                'name' => $wc->name
            ])
        ]);
    }

    public function store(StoreDebtRequest $request)
    {
        $authUser = auth()->user();

        $startDate = Carbon::createFromFormat('Y-m', $request->start_date)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $request->end_date)->startOfMonth();

        $periodMonth = $startDate->month;
        $periodYear = $startDate->year;

        $category = DebtCategory::find($request->debt_category_id)
            ?? DebtCategory::getDefaultService();

        // Validación centralizada
        if ($this->debtExists(
            $request->water_connection_id,
            $category->id,
            $periodMonth,
            $periodYear,
            $startDate,
            $endDate
        )) {
            return response()->json([
                'error' => 'Ya existe una deuda para este periodo.'
            ], 400);
        }

        Debt::create([
            'locality_id' => $authUser->locality_id,
            'created_by' => $authUser->id,
            'water_connection_id' => $request->water_connection_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $request->amount,
            'note' => $request->note,
            'debt_category_id' => $category->id,
            'period_month' => $periodMonth,
            'period_year' => $periodYear,
        ]);

        return response()->json([
            'success' => 'Deuda creada correctamente'
        ], 201);
    }

    public function assignAll(Request $request)
    {
        $authUser = auth()->user();

        $startDate = Carbon::createFromFormat('Y-m', $request->start_date)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $request->end_date)->startOfMonth();

        $periodMonth = $startDate->month;
        $periodYear = $startDate->year;

        $category = DebtCategory::find($request->debt_category_id)
            ?? DebtCategory::getDefaultService();

        $customers = Customer::with('waterConnections.cost')
            ->where('locality_id', $authUser->locality_id)
            ->get();

        $created = false;

        foreach ($customers as $customer) {
            foreach ($customer->waterConnections as $wc) {

                if (!$wc->cost || !$wc->cost->price) continue;

                if ($this->debtExists(
                    $wc->id,
                    $category->id,
                    $periodMonth,
                    $periodYear,
                    $startDate,
                    $endDate
                )) continue;

                Debt::create([
                    'locality_id' => $authUser->locality_id,
                    'created_by' => $authUser->id,
                    'water_connection_id' => $wc->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'amount' => $wc->cost->price,
                    'note' => $request->note ?? 'Asignación masiva',
                    'debt_category_id' => $category->id,
                    'period_month' => $periodMonth,
                    'period_year' => $periodYear,
                ]);

                $created = true;
            }
        }

        if (!$created) {
            return back()->with('error', 'Todas las deudas ya existen para ese periodo.');
        }

        return back()->with('success', 'Deudas asignadas correctamente.');
    }

    private function debtExists($waterConnectionId, $categoryId, $month, $year, $startDate, $endDate)
    {
        $serviceId = DebtCategory::getDefaultService()->id;

        if ($categoryId == $serviceId) {
            return Debt::where('water_connection_id', $waterConnectionId)
                ->where('debt_category_id', $categoryId)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->exists();
        }

        return Debt::where('water_connection_id', $waterConnectionId)
            ->where('debt_category_id', $categoryId)
            ->where('status', '!=', 'paid')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('start_date', '<=', $endDate)
                  ->where('end_date', '>=', $startDate);
            })
            ->exists();
    }

    public function destroy($id, Request $request)
    {
        $debt = Debt::findOrFail($id);

        $before = $debt->toArray();
        $debt->delete();

        MovementHistory::create([
            'alter_by' => Auth::user()->id,
            'module' => 'deudas',
            'action' => 'delete',
            'record_id' => $id,
            'before_data' => $before,
            'current_data' => null,
        ]);

        return back()->with('success', 'Deuda eliminada correctamente.');
    }
}
