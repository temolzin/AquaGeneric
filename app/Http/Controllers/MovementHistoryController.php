<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Payment, Debt, Cost, GeneralExpense, Locality};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MovementHistoryController extends Controller
{
    public function generatePDF(Request $request)
    {
        $localityId = $request->input('locality_id');
        $module = strtolower($request->input('module', ''));
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $showModuleColumn = $request->boolean('show_module_column');

        $authUser = auth()->user();
        $locality = Locality::find($localityId);

        if (!$locality) {
            return back()->with('error', 'No se encontró la localidad.');
        }

        $includeAllModules = $module === 'todos';

        $data = $this->getFilteredMovements($localityId, $module, $startDate, $endDate, $includeAllModules);
        if (!$data) {
            return back()->with('error', 'No se encontraron movimientos o módulo inválido.');
        }

        switch (true) {
            case $includeAllModules && $showModuleColumn:
                $groupedByModule = [];
                foreach ($data['modulesRequested'] as $moduleKey => $movements) {
                    $moduleName = $data['moduleNames'][$moduleKey] ?? $moduleKey;
                    foreach ($movements as $movement) {
                        $date = Carbon::parse($movement->updated_at)->startOfDay();
                        $dayEs = $data['weekDays'][$date->format('l')] ?? $date->format('l');
                        $formattedDay = $dayEs . ', ' . $date->format('d/m/Y');
                        $groupedByModule[$moduleName][$formattedDay][] = [
                            'movement' => $movement,
                            'module' => $moduleName,
                        ];
                    }
                }

                return Pdf::loadView('reports.pdfMovementsHistoryGroupedByModule', [
                    'groupedByModule'  => $groupedByModule,
                    'authUserLocality' => $locality,
                    'startDate'        => $startDate,
                    'endDate'          => $endDate,
                ])->setPaper('a4', 'portrait')
                  ->stream('Historial_Movimientos_PorModulo_Fechas_' . $locality->name . '_' . now()->format('d_m_Y') . '.pdf');

            case $includeAllModules && !$showModuleColumn:
                return Pdf::loadView('reports.pdfMovementsHistory', [
                    'groupedByDay'     => $data['groupedByDay'],
                    'authUserLocality' => $locality,
                    'startDate'        => $startDate,
                    'endDate'          => $endDate,
                ])->setPaper('a4', 'portrait')
                  ->stream('Historial_Movimientos_TodosModulos_' . $locality->name . '_' . now()->format('d_m_Y') . '.pdf');

            case !$includeAllModules && $showModuleColumn:
                return back()->with('error', 'Cuando seleccionas un módulo, no debes marcar "Mostrar módulo como columna".');

            case !$includeAllModules && empty($module):
                return back()->with('error', 'Selecciona un módulo o elige "todos".');

            default:
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

                $moduleLabelForFile = $moduleLabelMap[$module] ?? ucfirst($module);

                return Pdf::loadView('reports.pdfMovementsHistoryModuleTitle', [
                    'groupedByDay'       => $data['groupedByDay'],
                    'authUserLocality'   => $locality,
                    'startDate'          => $startDate,
                    'endDate'            => $endDate,
                    'selectedModuleName' => $data['moduleNames'][ucfirst($module)] ?? ucfirst($module),
                ])->setPaper('a4', 'portrait')
                  ->stream('Historial_Movimientos_' . $moduleLabelForFile . '_' . $locality->name . '_' . now()->format('d_m_Y') . '.pdf');
        }
    }

    private function getFilteredMovements($localityId, $module, $startDate, $endDate, $includeAllModules = false)
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        $applyFilters = function ($query) use ($localityId, $start, $end) {
            return $query->with(['creator.locality'])
                ->withTrashed()
                ->where('locality_id', $localityId)
                ->whereHas('creator', fn($userQuery) => $userQuery->where('locality_id', $localityId))
                ->when($start && $end, fn($q) => $q->whereBetween('updated_at', [$start, $end]))
                ->when($start && !$end, fn($q) => $q->where('updated_at', '>=', $start))
                ->when(!$start && $end, fn($q) => $q->where('updated_at', '<=', $end))
                ->orderByDesc('updated_at')
                ->orderByDesc('deleted_at')
                ->get();
        };

        $modulesRequested = [];
        $moduleKey = strtolower($module ?? '');

        switch (true) {
            case $includeAllModules:
                $modulesRequested = [
                    'Payments'        => $applyFilters(Payment::query()),
                    'Debts'           => $applyFilters(Debt::query()),
                    'Costs'           => $applyFilters(Cost::query()),
                    'GeneralExpenses' => $applyFilters(GeneralExpense::query()),
                ];
                break;

            case in_array($moduleKey, ['pagos', 'payments']):
                $modulesRequested = ['Payments' => $applyFilters(Payment::query())];
                break;

            case in_array($moduleKey, ['deudas', 'debts']):
                $modulesRequested = ['Debts' => $applyFilters(Debt::query())];
                break;

            case in_array($moduleKey, ['costos', 'costs']):
                $modulesRequested = ['Costs' => $applyFilters(Cost::query())];
                break;

            case in_array($moduleKey, ['gastos', 'generalexpenses', 'general_expenses']):
                $modulesRequested = ['GeneralExpenses' => $applyFilters(GeneralExpense::query())];
                break;

            default:
                return null;
        }

        $weekDays = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];

        $moduleNames = [
            'Payments' => 'Pagos',
            'Debts' => 'Deudas',
            'Costs' => 'Costos',
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
