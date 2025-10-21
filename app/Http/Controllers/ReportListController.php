<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\Customer;

class ReportListController extends Controller {
    
    public function index(Request $request) {
        
        $menuItems = config('adminlte.menu');
        $sections = [];

        foreach ($menuItems as $item) {
            if (isset($item['text']) && !isset($item['submenu'])) {
                $sections[] = [
                    'text' => $item['text'],
                    'url' => $item['url'] ?? '#',
                    'reports' => $this->getReportsForSection($item['text']),
                ];
            } elseif (isset($item['submenu'])) {
                $sections[] = [
                    'text' => $item['text'],
                    'url' => '#',
                    'reports' => $this->getReportsForSection($item['text'], $item['submenu']),
                ];
            }
        }

        $sectionsCollection = new Collection($sections);

        $sectionsCollection = $sectionsCollection->filter(function ($section) {
            return !empty($section['reports']);
        });

        $search = $request->input('search');
        if ($search) {
            $sectionsCollection = $sectionsCollection->filter(function ($section) use ($search) {
                $sectionMatch = stripos($section['text'], $search) !== false;
                $reportMatch = !empty($section['reports']) && collect($section['reports'])->filter(function ($report) use ($search) {
                    return stripos($report['text'], $search) !== false;
                })->isNotEmpty();
                return $sectionMatch || $reportMatch;
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $sectionsCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $sections = new LengthAwarePaginator($currentItems, $sectionsCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $customers = Customer::where('locality_id', auth()->user()->locality_id)->get();

        return view('reportList.index', compact('sections', 'customers'));
    }

    private function getReportsForSection($sectionText, $submenu = null) {
        
        $reports = [];

        switch ($sectionText) {
            case 'Clientes':
                $reports = [
                    ['text' => 'Lista de Clientes', 'url' => '/report/pdfCustomers', 'type' => 'pdf'],
                    ['text' => 'Resumen de Tomas de Agua', 'url' => '/report/pdfCustomersSummary', 'type' => 'pdf'],
                ];
                break;

            case 'Gestión de Pagos':
                $reports = [
                    ['text' => 'Clientes al Día', 'url' => '/report/current-customers', 'type' => 'pdf'],
                ];
                if ($submenu) {
                    foreach ($submenu as $subitem) {
                        if ($subitem['text'] === 'Pagos') {
                            $reports[] = [
                                'text' => 'Pagos por Cliente',
                                'type' => 'button',
                                'button_class' => 'btn bg-maroon report-btn',
                                'icon' => '<i class="fas fa-money-bill-wave"></i>',
                                'modal' => '#clientPayments',
                                'title' => 'Pagos por Cliente',
                                'label' => ['d-none d-md-inline' => 'Pagos por Cliente', 'd-inline d-md-none' => '']
                            ];
                            $reports[] = [
                                'text' => 'Pagos por Toma',
                                'type' => 'button',
                                'button_class' => 'btn bg-purple report-btn',
                                'icon' => '<i class="fas fa-fw fa-water"></i>',
                                'modal' => '#waterConnectionPayments',
                                'title' => 'Pagos por Toma',
                                'label' => ['d-none d-md-inline' => 'Pagos por Toma', 'd-inline d-md-none' => '']
                            ];
                            $reports[] = [
                                'text' => 'Reporte de Pagos Adelantados',
                                'type' => 'button',
                                'button_class' => 'btn bg-teal report-btn',
                                'icon' => '<i class="fas fa-file-pdf"></i>',
                                'modal' => '#generateAdvancePaymentsReportModal',
                                'title' => 'Reporte de Pagos Adelantados',
                                'label' => ['d-none d-md-inline' => 'Reporte de Pagos Adelantados', 'd-inline d-md-none' => '']
                            ];
                        }
                    }
                }
                break;

            case 'Deudas':
                $reports = [
                    ['text' => 'Clientes con Deudas', 'url' => '/customers-with-debts', 'type' => 'pdf'],
                ];
                break;

            case 'Costos':
                $reports = [
                    ['text' => 'Lista de Costos', 'url' => '/reports/generateCostListReport', 'type' => 'pdf'],
                ];
                break;

            case 'Localidades':
                $reports = [];
                break;

            case 'Tomas de Agua':
                $reports = [];
                break;

            case 'Gastos':
                $reports = [];
                break;

            case 'Gestión de Incidencias':
                $reports = [
                    ['text' => 'Lista de Incidencias', 'url' => '/reports/generateIncidentListReport', 'type' => 'pdf'],
                    ['text' => 'Lista de Categorías de Incidencias', 'url' => '/reports/generateIncidentCategoyListReport', 'type' => 'pdf'],
                    ['text' => 'Lista de Estatus de Incidencias', 'url' => '/reports/generateIncidentStatusListReport', 'type' => 'pdf'],
                ];
                break;

            case 'Empleados':
                $reports = [
                    ['text' => 'Lista de Empleados', 'url' => '/reports/generateEmployeeListReport', 'type' => 'pdf'],
                ];
                break;

            case 'Inventario':
                $reports = [];
                break;

            case 'Mis Pagos':
            case 'Mis Deudas':
            case 'Mis Tomas de Agua':
            case 'Avisos':
            case 'Reporte de fallas':
            case 'Falta de pago':
                $reports = [];
                break;
        }

        return $reports;
    }
}
