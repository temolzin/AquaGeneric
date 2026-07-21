<?php

namespace App\Http\Controllers;

use App\Models\DiscountHistory;
use App\Models\Locality;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DiscountHistoryController extends Controller
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
        $dbModule = $this->normalizeModuleForStorage($module);
        $moduleNames = [
            'pagos' => 'Pagos',
            'deudas' => 'Deudas',
            'payment' => 'Pagos',
            'debt' => 'Deudas',
        ];

        if (!$locality) {
            return Pdf::loadView('reports.pdfDiscountHistory', [
                'error' => 'No se encontró la localidad.',
                'authUserLocality' => null,
                'reportType' => 'single-module',
                'reportTitles' => ['single-module' => 'HISTORIAL DE DESCUENTOS'],
                'groupedByDay' => [],
                'groupedByModule' => [],
                'moduleNames' => [],
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])->setPaper('a4', 'portrait')->stream('error.pdf');
        }

        $query = DiscountHistory::query()
            ->with(['discount', 'customer', 'creator'])
            ->where('locality_id', $localityId)
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('created_at', '>=', Carbon::parse($startDate)->toDateString());
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('created_at', '<=', Carbon::parse($endDate)->toDateString());
            })
            ->when(!$includeAllModules && $module, function ($q) use ($dbModule) {
                $q->where('module', $dbModule);
            })
            ->orderByDesc('created_at');

        $histories = $query->get();

        if ($histories->isEmpty()) {
            return Pdf::loadView('reports.pdfDiscountHistory', [
                'error' => 'No se encontraron registros para los filtros seleccionados.',
                'authUserLocality' => $locality,
                'reportType' => $includeAllModules && $showModuleColumn ? 'all-modules-grouped' : 'single-module',
                'reportTitles' => ['single-module' => 'HISTORIAL DE DESCUENTOS'],
                'groupedByDay' => [],
                'groupedByModule' => [],
                'moduleNames' => $moduleNames,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])->setPaper('a4', 'portrait')->stream('historial_descuentos_vacio.pdf');
        }

        $groupedByDay = [];
        $groupedByModule = [];

        foreach ($histories as $history) {
            $day = Carbon::parse($history->created_at)->translatedFormat('l, d/m/Y');
            $groupedByDay[$day][] = $history;

            if ($includeAllModules) {
                $moduleKey = $history->module;
                $moduleLabel = $this->getModuleLabel($moduleKey);
                $groupedByModule[$moduleLabel][$day][] = $history;
            }
        }

        $reportType = 'single-module';
        $reportTitles = [
            'single-module' => 'HISTORIAL DE DESCUENTOS',
            'all-modules-grouped' => 'HISTORIAL DE DESCUENTOS POR MÓDULO',
        ];

        if ($includeAllModules) {
            $reportType = $showModuleColumn ? 'all-modules-grouped' : 'single-module';
        }

        return Pdf::loadView('reports.pdfDiscountHistory', [
            'authUserLocality' => $locality,
            'reportType' => $reportType,
            'reportTitles' => $reportTitles,
            'groupedByDay' => $groupedByDay,
            'groupedByModule' => $groupedByModule,
            'moduleNames' => $moduleNames,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->setPaper('a4', 'portrait')->stream('historial_descuentos.pdf');
    }

    private function normalizeModuleForStorage(string $module): string
    {
        return match ($module) {
            'pagos' => 'payment',
            'deudas' => 'debt',
            default => $module,
        };
    }

    private function getModuleLabel(string $module): string
    {
        return match ($module) {
            'pagos', 'payment' => 'Pagos',
            'deudas', 'debt' => 'Deudas',
            default => ucfirst($module),
        };
    }
}
