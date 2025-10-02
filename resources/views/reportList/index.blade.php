@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Lista de Reportes')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Lista de Reportes</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                                <form method="GET" action="{{ route('reportList.index') }}" class="flex-grow-1 mt-2" style="min-width: 328px; max-width: 40%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por Sección o Reporte" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Reporte">
                                                <i class="fas fa-search d-lg-none"></i>
                                                <span class="d-none d-lg-inline">Buscar</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="reportsTable" class="table table-striped display responsive nowrap" style="width:100%; max-width: 100%; margin: 0 auto; margin-top: 30px;">
                                    <thead>
                                        <tr>
                                            <th>Secciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($sections->isEmpty())
                                            <tr>
                                                <td colspan="1" class="text-center">No hay secciones disponibles</td>
                                            </tr>
                                        @else
                                            @foreach($sections as $section)
                                                <tr class="clickable-row" data-toggle="collapse" data-target="#collapse-{{ str_replace(' ', '-', strtolower($section['text'])) }}">
                                                    <td>
                                                        <span class="text-dark">{{ $section['text'] }}</span> <i class="fas fa-chevron-down rotate-icon ml-2"></i>
                                                        @if (!empty($section['reports']))
                                                            <div id="collapse-{{ str_replace(' ', '-', strtolower($section['text'])) }}" class="collapse">
                                                                <div class="button-group-uniform">
                                                                    @foreach ($section['reports'] as $report)
                                                                        @if (isset($report['type']) && $report['type'] === 'pdf')
                                                                            <a type="button" class="btn btn-secondary report-btn" target="_blank" title="{{ $report['text'] }}" href="{{ $report['url'] }}">
                                                                                <i class="fas fa-file-pdf"></i> {{ $report['text'] }}
                                                                            </a>
                                                                        @elseif (isset($report['type']) && $report['type'] === 'button')
                                                                            <button type="button" class="{{ $report['button_class'] }} report-btn" data-toggle="modal" data-target="{{ $report['modal'] ?? '' }}" title="{{ $report['title'] }}" {{ isset($report['url']) ? 'href="' . $report['url'] . '"' : '' }} {{ isset($report['target']) ? 'target="' . $report['target'] . '"' : '' }}>
                                                                                {!! $report['icon'] ?? '' !!}
                                                                                <span class="{{ $report['label']['d-none d-md-inline'] ?? '' }}"> {{ $report['label']['d-none d-md-inline'] ?? $report['text'] }}</span>
                                                                                <span class="{{ $report['label']['d-inline d-md-none'] ?? '' }}"> {{ $report['label']['d-inline d-md-none'] ?? $report['text'] }}</span>
                                                                            </button>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="clickable-row" data-toggle="collapse" data-target="#collapse-panel">
                                                <td>
                                                    <span class="text-dark">Panel</span> <i class="fas fa-chevron-down rotate-icon ml-2"></i>
                                                    <div id="collapse-panel" class="collapse">
                                                        <div class="button-group-uniform">
                                                            <button type="button" class="btn btn-info report-btn" data-toggle="modal" data-target="#annualEarnings" title="Ingresos Anuales">
                                                                <i class="fa fa-dollar-sign"></i> <span class="d-none d-md-inline">Ingresos Anuales</span><span class="d-inline d-md-none">Ingresos Anuales</span>
                                                            </button>
                                                            <button type="button" class="btn bg-olive report-btn" data-toggle="modal" data-target="#weeklyEarnings" title="Ingresos Semanales">
                                                                <i class="fa fa-dollar-sign"></i> <span class="d-none d-md-inline">Ingresos Semanales</span><span class="d-inline d-md-none">Ingresos Semanales</span>
                                                            </button>
                                                            <button type="button" class="btn btn-info report-btn" data-toggle="modal" data-target="#annualExpenses" title="Egresos Anuales">
                                                                <i class="fa fa-dollar-sign"></i> <span class="d-none d-md-inline">Egresos Anuales</span><span class="d-inline d-md-none">Egresos Anuales</span>
                                                            </button>
                                                            <button type="button" class="btn bg-olive report-btn" data-toggle="modal" data-target="#weeklyExpenses" title="Egresos Semanales">
                                                                <i class="fa fa-dollar-sign"></i> <span class="d-none d-md-inline">Egresos Semanales</span><span class="d-inline d-md-none">Egresos Semanales</span>
                                                            </button>
                                                            <button type="button" class="btn btn-info report-btn" data-toggle="modal" data-target="#annualGains" title="Ganancias Anuales">
                                                                <i class="fa fa-dollar-sign"></i> <span class="d-none d-md-inline">Ganancias Anuales</span><span class="d-inline d-md-none">Ganancias Anuales</span>
                                                            </button>
                                                            <button type="button" class="btn bg-olive report-btn" data-toggle="modal" data-target="#weeklyGains" title="Ganancias Semanales">
                                                                <i class="fa fa-dollar-sign"></i> <span class="d-none d-md-inline">Ganancias Semanales</span><span class="d-inline d-md-none">Ganancias Semanales</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center">
                                    {!! $sections->links('pagination::bootstrap-4') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('payments.create')
        @include('payments.clientPayments')
        @include('payments.waterConnectionPayments')
        @include('advancePayments.advancePaymentsReportForm')
        @include('advancePayments.paymentHistoryModal')
        @include('payments.annualEarnings')
        @include('payments.weeklyEarnings')
        @include('generalExpenses.annualExpenses')
        @include('generalExpenses.weeklyExpenses')
        @include('generalExpenses.annualGains')
        @include('generalExpenses.weeklyGains')
    </section>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .rotate-icon {
        transition: transform 0.3s ease;
        transform-origin: center;
    }
    .collapse.show .rotate-icon {
        transform: rotate(180deg);
    }
    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: #f5f5f5;
    }
    .button-group-uniform {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
        flex-wrap: nowrap;
    }
    .button-group-uniform .report-btn {
        min-width: 140px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        box-sizing: border-box;
        margin: 0;
    }
    @media (max-width: 768px) {
        .button-group-uniform {
            flex-wrap: wrap;
        }
        .button-group-uniform .report-btn {
            min-width: 110px;
            height: 36px;
        }
    }
    .btn.bg-maroon { background-color: #d81b60; color: #fff !important; }
    .btn.bg-purple { background-color: #6f42c1; color: #fff !important; }
    .btn.bg-teal { background-color: #20c997; color: #fff !important; }
    .btn.btn-success { background-color: #28a745; color: #fff !important; }
    .btn.bg-olive { background-color: #3d9970; color: #fff !important; }
    table.dataTable thead th {
        position: relative;
        cursor: pointer;
    }
    table.dataTable thead th:after {
        content: '';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid #333;
    }
    table.dataTable thead th.sorting_asc:after,
    table.dataTable thead th.sorting_desc:after {
        border-top: none;
        border-bottom: 4px solid #333;
    }
    table.dataTable thead th.sorting:after {
        border-top: 4px solid #333;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#reportsTable').DataTable({
            responsive: true,
            buttons: ['csv', 'excel', 'print'],
            dom: 'Bfrtip',
            paging: true,
            pageLength: 10,
            deferRender: true,
            info: true,
            searching: true,
            order: [[0, 'asc']],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            columnDefs: [
                { targets: 0, orderable: true }
            ]
        });

        $('#reportsTable').on('click', '.clickable-row', function(e) {
            if (!$(e.target).is('.report-btn') && !$(e.target).closest('.report-btn').length &&
                !$(e.target).is('a') && !$(e.target).closest('a').length) {
                var target = $(this).data('target');
                var $target = $(target);
                var icon = $(this).find('.rotate-icon');
                if ($target.length) {
                    $target.collapse('toggle');
                    if ($target.hasClass('show')) {
                        icon.addClass('rotate-down');
                    } else {
                        icon.removeClass('rotate-down');
                    }
                }
                e.stopPropagation();
            } else {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
</script>
@endsection
