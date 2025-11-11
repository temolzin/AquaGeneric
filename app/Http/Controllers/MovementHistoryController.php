<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Debt;
use App\Models\Cost;
use App\Models\GeneralExpense;
use App\Models\Locality;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MovementHistoryController extends Controller
{
    public function generatePDF(Request $request)
    {
        $localityId = $request->input('locality_id');
        $module = $request->input('module');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $showModuleColumn = $request->boolean('show_module_column');

        $authUser = auth()->user();

        $locality = Locality::find($localityId);
        if (!$locality) {
            return redirect()->back()->with('error', 'No se encontró la localidad.');
        }

        $moduleKey = strtolower($module ?? '');
        $includeAllModules = ($moduleKey === 'todos');

        $data = $this->getFilteredMovements($localityId, $module, $startDate, $endDate, $includeAllModules);
        if (!$data) {
            return redirect()->back()->with('error', 'No se encontraron movimientos o módulo inválido.');
        }

        $groupedByDay = $data['groupedByDay'];
        $modulesRequested = $data['modulesRequested'];
        $moduleNames = $data['moduleNames'];
        $weekDays = $data['weekDays'];

        if ($includeAllModules) {
            if ($showModuleColumn) {
                $groupedByModule = [];
                foreach ($modulesRequested as $moduleKeyIter => $movements) {
                    $moduleName = $moduleNames[$moduleKeyIter] ?? $moduleKeyIter;
                    foreach ($movements as $movement) {
                        $date = Carbon::parse($movement->updated_at)->startOfDay();
                        $dayEs = $weekDays[$date->format('l')] ?? $date->format('l');
                        $formattedDay = $dayEs . ', ' . $date->format('d/m/Y');
                        $groupedByModule[$moduleName][$formattedDay][] = [
                            'movement' => $movement,
                            'module' => $moduleName,
                        ];
                    }
                }

                $pdf = Pdf::loadView('reports.pdfMovementsHistoryGroupedByModule', [
                    'groupedByModule'  => $groupedByModule,
                    'authUserLocality' => $locality,
                    'startDate'        => $startDate,
                    'endDate'          => $endDate,
                ])->setPaper('a4', 'portrait');

                return $pdf->stream('Historial_Movimientos_PorModulo_Fechas_' . $locality->name . '_' . Carbon::now()->format('d_m_Y') . '.pdf');
            } else {
                $pdf = Pdf::loadView('reports.pdfMovementsHistory', [
                    'groupedByDay'     => $groupedByDay,
                    'authUserLocality' => $locality,
                    'startDate'        => $startDate,
                    'endDate'          => $endDate,
                ])->setPaper('a4', 'portrait');

                return $pdf->stream('Historial_Movimientos_TodosModulos_' . $locality->name . '_' . Carbon::now()->format('d_m_Y') . '.pdf');
            }
        }

        if ($showModuleColumn) {
            return redirect()->back()->with('error', 'Cuando seleccionas un módulo, no debes marcar "Mostrar módulo como columna".');
        }

        if (empty($module)) {
            return redirect()->back()->with('error', 'Selecciona un módulo o elige "todos".');
        }

        $moduleLabelMap = [
            'pagos' => 'Pagos',
            'payments' => 'Pagos',
            'deudas' => 'Deudas',
            'debts' => 'Deudas',
            'costos' => 'Costos',
            'costs' => 'Costos',
            'gastos' => 'Gastos',
            'generalexpenses' => 'GastosGenerales',
            'general_expenses' => 'GastosGenerales',
        ];
        $moduleLabelForFile = $moduleLabelMap[$moduleKey] ?? ucfirst($moduleKey);

        $pdf = Pdf::loadView('reports.pdfMovementsHistoryModuleTitle', [
            'groupedByDay'      => $groupedByDay,
            'authUserLocality'  => $locality,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'selectedModuleName'   => $moduleNames[ucfirst($module)] ?? ucfirst($module),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Historial_Movimientos_' . $moduleLabelForFile . '_' . $locality->name . '_' . Carbon::now()->format('d_m_Y') . '.pdf');
    }

    private function getFilteredMovements($localityId, $module, $startDate, $endDate, $includeAllModules = false)
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        $applyFilters = function ($query) use ($localityId, $start, $end) {
            return $query->with(['creator.locality'])
                ->withTrashed()
                ->where('locality_id', $localityId)
                ->where(function ($q) use ($localityId) {
                    $q->whereHas('creator', function ($userQuery) use ($localityId) {
                        $userQuery->where('locality_id', $localityId);
                    })
                    ->where(function ($inner) {
                        $inner->whereNotNull('updated_at')
                              ->orWhereNotNull('deleted_at');
                    });
                })
                ->when($start && $end, fn($q) => $q->whereBetween('updated_at', [$start, $end]))
                ->when($start && !$end, fn($q) => $q->where('updated_at', '>=', $start))
                ->when(!$start && $end, fn($q) => $q->where('updated_at', '<=', $end))
                ->orderByDesc('updated_at')
                ->orderByDesc('deleted_at')
                ->get();
    };
    
        $modulesRequested = [];
        $moduleKey = strtolower($module ?? '');

        if ($includeAllModules) {
            $modulesRequested['Payments'] = $applyFilters(Payment::query());
            $modulesRequested['Debts'] = $applyFilters(Debt::query());
            $modulesRequested['Costs'] = $applyFilters(Cost::query());
            $modulesRequested['GeneralExpenses'] = $applyFilters(GeneralExpense::query());
        } else {
            if (in_array($moduleKey, ['pagos', 'payments'])) {
                $modulesRequested['Payments'] = $applyFilters(Payment::query());
            } elseif (in_array($moduleKey, ['deudas', 'debts'])) {
                $modulesRequested['Debts'] = $applyFilters(Debt::query());
            } elseif (in_array($moduleKey, ['costos', 'costs'])) {
                $modulesRequested['Costs'] = $applyFilters(Cost::query());
            } elseif (in_array($moduleKey, ['gastos', 'generalexpenses', 'general_expenses'])) {
                $modulesRequested['GeneralExpenses'] = $applyFilters(GeneralExpense::query());
            } else {
                return null;
            }
        }

        $weekDays = [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado',
            'Sunday'    => 'Domingo',
        ];

        $moduleNames = [
            'Payments'        => 'Pagos',
            'Debts'           => 'Deudas',
            'Costs'           => 'Costos',
            'GeneralExpenses' => 'Gastos Generales',
        ];

        $groupedByDay = [];
        foreach ($modulesRequested as $moduleKey => $movements) {
            foreach ($movements as $movement) {
                $date = Carbon::parse($movement->updated_at)->startOfDay();
                $groupedByDay[$date->format('Y-m-d')][] = [
                    'movement' => $movement,
                    'module'   => $moduleNames[$moduleKey] ?? $moduleKey,
                ];
            }
        }

        krsort($groupedByDay);

        $formattedGroupedByDay = [];
        foreach ($groupedByDay as $dateKey => $entries) {
            $date = Carbon::createFromFormat('Y-m-d', $dateKey);
            $dayEs = $weekDays[$date->format('l')] ?? $date->format('l');
            $formattedGroupedByDay[$dayEs . ', ' . $date->format('d/m/Y')] = $entries;
        }

        return [
            'groupedByDay' => $formattedGroupedByDay,
            'modulesRequested' => $modulesRequested,
            'moduleNames' => $moduleNames,
            'weekDays' => $weekDays,
        ];
    }
}
