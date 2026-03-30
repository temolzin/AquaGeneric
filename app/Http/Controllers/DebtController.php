<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $customers = Customer::where('locality_id', $authUser->locality_id)
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
            ->where('locality_id', $authUser->locality_id)
            ->get();
        $debts = Debt::with(['waterConnection.customer', 'creator'])
            ->whereHas('waterConnection', function ($query) use ($authUser) {
                $query->where('locality_id', $authUser->locality_id);
            })
            ->where('status', '!=', 'paid')
            ->orderByDesc('created_at')
            ->paginate(10);
        $debtCategories = DebtCategory::where('locality_id', $authUser->locality_id)->get();
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

    public function store(Request $request)
    {
        $authUser = auth()->user();
        $request->validate([
            'water_connection_id' => ['required', 'exists:water_connections,id'],
            'start_date' => ['required', 'date_format:Y-m'],
            'end_date' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:0'],
            'debt_category_id' => ['required', 'exists:debt_categories,id'],
        ]);
        try {
            $startDate = Carbon::createFromFormat('Y-m', $request->start_date)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $request->end_date)->endOfMonth();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Formato de fecha inválido'], 422);
        }
        if ($endDate->lt($startDate)) {
            return response()->json(['error' => 'La fecha final no puede ser menor a la inicial.'], 422);
        }
        $waterConnection = WaterConnection::find($request->water_connection_id);
        $localityId = $waterConnection?->locality_id;
        if (!$localityId) {
            return response()->json(['error' => 'No se pudo determinar la localidad.'], 422);
        }
        $category = DebtCategory::find($request->debt_category_id);
        if (!$category) {
            return response()->json(['error' => 'Categoría inválida.'], 422);
        }
        $validationError = $this->validateDebtConflict(
            $category,
            $waterConnection,
            $localityId,
            $startDate,
            $endDate
        );
        if ($validationError) {
            return response()->json(['error' => $validationError], 422);
        }
        Debt::create([
            'locality_id' => $localityId,
            'created_by' => $authUser->id,
            'water_connection_id' => $waterConnection->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $request->amount,
            'note' => $request->note,
            'debt_category_id' => $category->id,
            'period_month' => $startDate->month,
            'period_year' => $startDate->year,
        ]);
        return response()->json([
            'success' => 'Deuda creada correctamente'
        ], 201);
    }

    private function validateDebtConflict($category, $waterConnection, $localityId, $startDate, $endDate)
    {
        return $category->isService()
            ? $this->validateServiceDebt($waterConnection, $localityId, $startDate)
            : $this->validateGenericDebt($category, $waterConnection, $localityId, $startDate, $endDate);
    }

    private function validateServiceDebt($waterConnection, $localityId, $startDate)
    {
        $periodMonth = $startDate->month;
        $periodYear = $startDate->year;
        $ym = $startDate->format('Y-m');
        $serviceCategoryIds = DebtCategory::where('name', DebtCategory::SERVICE_NAME)
            ->where('locality_id', $localityId)
            ->pluck('id');
        $exists = Debt::where('water_connection_id', $waterConnection->id)
            ->where('locality_id', $localityId)
            ->whereIn('debt_category_id', $serviceCategoryIds)
            ->where(function ($q) use ($periodMonth, $periodYear, $ym) {
                $q->where(
                    fn($a) =>
                    $a->where('period_month', $periodMonth)
                        ->where('period_year', $periodYear)
                )->orWhere(
                    fn($b) =>
                    $b->whereNull('period_month')
                        ->whereRaw("DATE_FORMAT(start_date, '%Y-%m') = ?", [$ym])
                );
            })
            ->exists();
        return $exists
            ? 'Ya existe una deuda de Servicio de Agua para este periodo (por toma).'
            : null;
    }

    private function validateGenericDebt($category, $waterConnection, $localityId, $startDate, $endDate)
    {
        $exists = Debt::where('water_connection_id', $waterConnection->id)
            ->where('locality_id', $localityId)
            ->where('debt_category_id', $category->id)
            ->where('status', '!=', 'paid')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereDate('start_date', '<=', $endDate)
                    ->whereDate('end_date', '>=', $startDate);
            })
            ->exists();

        return $exists
            ? 'Ya existe una deuda activa en ese rango de fechas.'
            : null;
    }

    public function assignAll(Request $request)
    {
        $authUser = auth()->user();
        $startDate = Carbon::createFromFormat('Y-m', $request->start_date)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $request->end_date)->endOfMonth();
        $periodMonth = $startDate->month;
        $periodYear = $startDate->year;
        $category = DebtCategory::getDefaultService(
            $authUser->locality_id,
            $authUser->id
        );
        $customers = Customer::with('waterConnections.cost')
            ->where('locality_id', $authUser->locality_id)
            ->get();
        $created = false;
        foreach ($customers as $customer) {
            foreach ($customer->waterConnections as $wc) {
                if (!$wc->cost?->price) continue;
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

        return $created
            ? back()->with('success', 'Deudas asignadas correctamente.')
            : back()->with('error', 'Todas las deudas ya existen para ese periodo.');
    }

    private function debtExists($waterConnectionId, $categoryId, $month, $year, $startDate, $endDate)
    {
        $category = DebtCategory::find($categoryId);
        $waterConnection = WaterConnection::find($waterConnectionId);
        $localityId = $waterConnection?->locality_id;
        if (!$category || !$localityId) return false;
        return $category->isService()
            ? $this->validateServiceDebtExists($waterConnectionId, $localityId, $month, $year)
            : $this->validateGenericDebtExists($waterConnectionId, $categoryId, $localityId, $startDate, $endDate);
    }

    private function validateServiceDebtExists($waterConnectionId, $localityId, $month, $year)
    {
        $ym = sprintf('%04d-%02d', $year, $month);
        $serviceCategoryIds = DebtCategory::where('name', DebtCategory::SERVICE_NAME)
            ->where('locality_id', $localityId)
            ->pluck('id');
        return Debt::where('water_connection_id', $waterConnectionId)
            ->where('locality_id', $localityId)
            ->whereIn('debt_category_id', $serviceCategoryIds)
            ->where(function ($q) use ($month, $year, $ym) {
                $q->where(
                    fn($q2) =>
                    $q2->where('period_month', $month)
                        ->where('period_year', $year)
                )->orWhere(
                    fn($q3) =>
                    $q3->whereNull('period_month')
                        ->whereRaw("DATE_FORMAT(start_date, '%Y-%m') = ?", [$ym])
                );
            })
            ->exists();
    }

    private function validateGenericDebtExists($waterConnectionId, $categoryId, $localityId, $startDate, $endDate)
    {
        return Debt::where('water_connection_id', $waterConnectionId)
            ->where('locality_id', $localityId)
            ->where('debt_category_id', $categoryId)
            ->where('status', '!=', 'paid')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereDate('start_date', '<=', $endDate)
                    ->whereDate('end_date', '>=', $startDate);
            })
            ->exists();
    }

    public function destroy($id)
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
