<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovementHistory;
use App\Models\Locality;
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
        $locality = Locality::find($localityId);
        $includeAllModules = $module === 'todos';

        $baseData = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'module' => $module,
            'showModuleColumn' => $showModuleColumn,
            'authUserLocality' => $locality,
        ];

        if (!$locality) {
            return $this->returnErrorPDF('No se encontró la localidad.', $baseData);
        }

        switch (true) {
            case (!$includeAllModules && $showModuleColumn):
                return $this->returnErrorPDF('Cuando seleccionas un módulo, no debes marcar "Agrupar por módulo".', $baseData);
            case (!$includeAllModules && empty($module)):
                return $this->returnErrorPDF('Selecciona un módulo o elige "todos".', $baseData);
        }

        $data = $this->getFilteredMovements($localityId, $module, $startDate, $endDate, $includeAllModules);
        
        if (!$data) {
            return $this->returnErrorPDF('No se encontraron movimientos para los filtros seleccionados.', $baseData);
        }

        $reportType = $this->getReportType($includeAllModules, $showModuleColumn);

        $pdfData = array_merge($baseData, [
            'reportType' => $reportType,
            'reportTitles' => $this->getReportTitles($reportType, $data, $module),
            'moduleNames' => $data['moduleNames'],
            'groupedByDay' => $data['groupedByDay'],
            'groupedByModule' => $data['groupedByModule'],
            'selectedModuleName' => $data['moduleNames'][$this->normalizeModuleName($module)] ?? ucfirst($module),
        ]);

        $fileName = $this->generateFileName($reportType, $locality, $module, $data);

        return Pdf::loadView('reports.pdfMovementsHistory', $pdfData)
                  ->setPaper('a4', 'portrait')
                  ->stream($fileName);
    }

    private function getReportType($includeAllModules, $showModuleColumn)
    {
        switch (true) {
            case ($includeAllModules && $showModuleColumn):
                return 'all-modules-grouped';
            case ($includeAllModules && !$showModuleColumn):
                return 'all-modules-ungrouped';
            default:
                return 'single-module';
        }
    }

    private function getReportTitles($reportType, $data, $module)
    {
        switch ($reportType) {
            case 'single-module':
                return ['single-module' => 'HISTORIAL DE MOVIMIENTOS ' . strtoupper($data['moduleNames'][$this->normalizeModuleName($module)] ?? ucfirst($module))];
            case 'all-modules-grouped':
                return ['all-modules-grouped' => 'HISTORIAL DE MOVIMIENTOS POR MÓDULO'];
            case 'all-modules-ungrouped':
                return ['all-modules-ungrouped' => 'HISTORIAL DE MOVIMIENTOS'];
            default:
                return ['single-module' => 'HISTORIAL DE MOVIMIENTOS'];
        }
    }

    private function generateFileName($reportType, $locality, $module, $data)
    {
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

        switch ($reportType) {
            case 'all-modules-grouped':
                return 'Historial_Movimientos_PorModulo_' . $locality->name . '_' . now()->format('d_m_Y') . '.pdf';
            case 'all-modules-ungrouped':
                return 'Historial_Movimientos_TodosModulos_' . $locality->name . '_' . now()->format('d_m_Y') . '.pdf';
            case 'single-module':
            default:
                $moduleLabelForFile = $moduleLabelMap[$module] ?? ucfirst($module);
                return 'Historial_Movimientos_' . $moduleLabelForFile . '_' . $locality->name . '_' . now()->format('d_m_Y') . '.pdf';
        }
    }

    private function returnErrorPDF($message, $baseData = [])
    {
        $pdfData = array_merge($baseData, [
            'error' => $message,
            'groupedByDay' => [],
            'groupedByModule' => [],
            'moduleNames' => [],
            'reportType' => 'single-module',
            'reportTitles' => $this->getReportTitles('single-module', [], ''),
        ]);

        return Pdf::loadView('reports.pdfMovementsHistory', $pdfData)
                  ->setPaper('a4', 'portrait')
                  ->stream('error.pdf');
    }

    private function getFilteredMovements($localityId, $module, $startDate, $endDate, $includeAllModules = false)
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        $query = MovementHistory::query()
            ->with(['user.locality'])
            ->whereHas('user', fn($q) => $q->where('locality_id', $localityId))
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($start && !$end, fn($q) => $q->where('created_at', '>=', $start))
            ->when(!$start && $end, fn($q) => $q->where('created_at', '<=', $end))
            ->orderByDesc('created_at');

        if (!$includeAllModules) {
            $moduleValue = $this->getModuleValueForDB($module);
            if (!$moduleValue) return null;
            $query->where('module', $moduleValue);
        }

        $movements = $query->get();
        if ($movements->isEmpty()) return null;

        return $this->processMovementsData($movements);
    }

    private function processMovementsData($movements)
    {
        $weekDays = [
            'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo',
        ];

        $moduleNames = [
            'pagos' => 'Pagos', 'deudas' => 'Deudas', 'costos' => 'Costos', 'general_expenses' => 'Gastos Generales',
        ];

        $processedMovements = $movements->map(fn($movement) => $this->processMovementData($movement, $moduleNames));
        $groupedByDay = $this->groupMovementsByDay($processedMovements, $weekDays);
        $groupedByModule = $this->groupMovementsByModule($processedMovements, $moduleNames, $weekDays);

        return [
            'movements' => $processedMovements,
            'groupedByDay' => $groupedByDay,
            'groupedByModule' => $groupedByModule,
            'moduleNames' => $moduleNames,
            'weekDays' => $weekDays,
        ];
    }

    private function groupMovementsByDay($movements, $weekDays)
    {
        $grouped = [];
        foreach ($movements as $movement) {
            $date = Carbon::parse($movement->created_at)->startOfDay();
            $dayEs = $weekDays[$date->format('l')] ?? $date->format('l');
            $formattedDay = $dayEs . ', ' . $date->format('d/m/Y');
            $grouped[$formattedDay][] = $movement;
        }
        krsort($grouped);
        return $grouped;
    }

    private function groupMovementsByModule($movements, $moduleNames, $weekDays)
    {
        $grouped = [];
        foreach ($movements as $movement) {
            $moduleName = $moduleNames[$movement->module] ?? $movement->module;
            $date = Carbon::parse($movement->created_at)->startOfDay();
            $dayEs = $weekDays[$date->format('l')] ?? $date->format('l');
            $formattedDay = $dayEs . ', ' . $date->format('d/m/Y');
            $grouped[$moduleName][$formattedDay][] = $movement;
        }
        return $grouped;
    }

    private function getModuleValueForDB($moduleKey)
    {
        switch (strtolower($moduleKey)) {
            case 'pagos': case 'payments': return 'pagos';
            case 'deudas': case 'debts': return 'deudas';
            case 'costos': case 'costs': return 'costos';
            case 'gastos': case 'generalexpenses': case 'general_expenses': return 'general_expenses';
            default: return null;
        }
    }

    private function normalizeModuleName($module)
    {
        return $this->getModuleValueForDB($module) ?: $module;
    }

    private function processMovementData($movement, $moduleNames)
    {
        $changedFields = $this->getChangedFields($movement->before_data, $movement->current_data, $movement->action);
        
        $movement->formatted_before = $this->formatChanges($changedFields, 'before');
        $movement->formatted_current = $this->formatChanges($changedFields, 'current');
        $movement->module_name = $moduleNames[$movement->module] ?? $movement->module;
        $movement->record_id = $this->extractRecordId($movement->before_data, $movement->current_data, $movement->action);
        
        return $movement;
    }

    private function extractRecordId($beforeData, $currentData, $action)
    {
        if (!empty($currentData)) {
            $currentArray = $this->objectToArray($currentData);
            if (isset($currentArray['id'])) return $currentArray['id'];
        }
        
        if (!empty($beforeData)) {
            $beforeArray = $this->objectToArray($beforeData);
            if (isset($beforeArray['id'])) return $beforeArray['id'];
        }
        
        return 'N/A';
    }

    private function getChangedFields($beforeData, $currentData, $action)
    {
        switch ($action) {
            case 'delete':
                return ['type' => 'delete'];
            default:
                return $this->compareData($beforeData, $currentData);
        }
    }

    private function compareData($beforeData, $currentData)
    {
        switch (true) {
            case is_null($beforeData) || empty((array)$beforeData):
                return ['type' => 'no_before_data'];
            case is_null($currentData) || empty((array)$currentData):
                return ['type' => 'no_current_data'];
            default:
                return $this->findChanges($beforeData, $currentData);
        }
    }

    private function findChanges($beforeData, $currentData)
    {
        $beforeValues = []; $currentValues = [];
        $beforeArray = $this->objectToArray($beforeData);
        $currentArray = $this->objectToArray($currentData);
        
        foreach ($beforeArray as $key => $beforeValue) {
            if ($this->shouldSkipField($key, $beforeValue, $currentArray[$key] ?? null)) continue;
            
            $beforeFormatted = $this->formatSimpleValue($beforeValue);
            $currentFormatted = $this->formatSimpleValue($currentArray[$key] ?? null);
            
            if ($beforeFormatted !== $currentFormatted) {
                if ($beforeFormatted !== '') $beforeValues[] = $beforeFormatted;
                if ($currentFormatted !== '') $currentValues[] = $currentFormatted;
            }
        }
        
        return ['type' => 'changes', 'before_values' => $beforeValues, 'current_values' => $currentValues];
    }

    private function shouldSkipField($key, $beforeValue, $currentValue)
    {
        return in_array($key, ['created_at', 'updated_at', 'deleted_at']) || 
               $this->isComplexValue($beforeValue) || 
               $this->isComplexValue($currentValue);
    }

    private function objectToArray($data)
    {
        if (is_array($data)) return $data;
        if (is_object($data)) return json_decode(json_encode($data), true);
        return [];
    }

    private function isComplexValue($value)
    {
        return is_array($value) || is_object($value);
    }

    private function formatSimpleValue($value)
    {
        if (is_null($value)) return '';
        if (is_bool($value)) return $value ? 'Sí' : 'No';
        
        $stringValue = (string)$value;
        return strlen($stringValue) > 100 ? substr($stringValue, 0, 100) . '...' : $stringValue;
    }

    private function formatChanges($changedFields, $type)
    {
        switch ($changedFields['type']) {
            case 'delete': return $type === 'current' ? 'REGISTRO ELIMINADO' : 'N/A';
            case 'no_before_data': return 'Sin datos anteriores';
            case 'no_current_data': return 'Sin datos actuales';
            case 'changes': 
                $values = $type === 'before' ? $changedFields['before_values'] : $changedFields['current_values'];
                return empty($values) ? 'Sin cambios' : implode("\n", $values);
            default: return 'Sin datos';
        }
    }
}
