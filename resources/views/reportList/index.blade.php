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
                            <div class="card-box text-center">
                                <div id="reportsList" class="expandable-list mt-5 mx-auto">
                                    @if($sections->isEmpty())
                                        <div class="text-center p-4">No hay secciones disponibles</div>
                                    @else
                                        @php
                                            $colors = ['blue', 'green', 'red', 'yellow', 'cyan', 'purple', 'teal', 'orange', 'indigo', 'pink', 'gray', 'dark-gray', 'dark-green', 'deep-pink', 'light-gray', 'orange-red', 'sea-green', 'steel-blue', 'amethyst', 'coral'];
                                            $colorIndex = 0;
                                        @endphp
                                        @foreach($sections as $index => $section)
                                            <div class="expandable-card" data-color="{{ $colors[$colorIndex++ % count($colors)] }}">
                                                <div class="card-header expandable-header">
                                                    <span class="text-white">{{ $section['text'] }}</span>
                                                    <i class="fas fa-minus icon-toggle rotate-icon ml-auto" data-toggle="collapse" data-target="#collapse-{{ $index }}-{{ str_replace(' ', '-', strtolower($section['text'])) }}"></i>
                                                </div>
                                                @if (!empty($section['reports']))
                                                    <div id="collapse-{{ $index }}-{{ str_replace(' ', '-', strtolower($section['text'])) }}" class="collapse show card-body">
                                                        <div class="button-group-uniform">
                                                            @foreach ($section['reports'] as $report)
                                                                @if (isset($report['type']) && $report['type'] === 'pdf')
                                                                    <a type="button" class="btn btn-secondary report-btn" target="_blank" title="{{ $report['text'] }}" href="{{ $report['url'] }}">
                                                                        <i class="fas fa-file-pdf"></i> {{ $report['text'] }}
                                                                    </a>
                                                                @elseif (isset($report['type']) && $report['type'] === 'button')
                                                                    <button type="button" class="btn btn-secondary report-btn" data-toggle="modal" data-target="{{ $report['modal'] ?? '' }}" title="{{ $report['title'] }}" {{ isset($report['url']) ? 'href="' . $report['url'] . '"' : '' }} {{ isset($report['target']) ? 'target="' . $report['target'] . '"' : '' }}>
                                                                        {!! $report['icon'] ?? '' !!}
                                                                        <span class="{{ $report['label']['d-none d-md-inline'] ?? '' }}"> {{ $report['label']['d-none d-md-inline'] ?? $report['text'] }}</span>
                                                                        <span class="{{ $report['label']['d-inline d-md-none'] ?? '' }}"> {{ $report['label']['d-inline d-md-none'] ?? $report['text'] }}</span>
                                                                    </button>
                                                                @elseif (isset($report['type']) && $report['type'] === 'link')
                                                                    <a href="{{ $report['url'] }}" 
                                                                    target="{{ $report['target'] ?? '_blank' }}"
                                                                    class="{{ $report['button_class'] ?? 'btn btn-secondary' }} report-btn"
                                                                    title="{{ $report['title'] ?? $report['text'] }}">
                                                                        {!! $report['icon'] ?? '' !!}
                                                                        <span>{{ $report['label'] ?? $report['text'] }}</span>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="d-flex justify-content-center mt-5">
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
<style>
    :root {
        --color-blue: #007bff;
        --color-green: #28a745;
        --color-red: #dc3545;
        --color-yellow: #ffc107;
        --color-cyan: #17a2b8;
        --color-purple: #6f42c1;
        --color-teal: #20c997;
        --color-orange: #fd7e14;
        --color-indigo: #6610f2;
        --color-pink: #e83e8c;
        --color-gray: #6c757d;
        --color-dark-gray: #343a40;
        --color-dark-green: #00796b;
        --color-deep-pink: #d81b60;
        --color-light-gray: #f8f9fa;
        --color-orange-red: #ff4500;
        --color-sea-green: #2e8b57;
        --color-steel-blue: #4682b4;
        --color-amethyst: #9b59b6;
        --color-coral: #e74c3c;
    }

    .expandable-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 30px;
    }

    .expandable-card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        background: white;
        border: 1px solid #e0e0e0;
    }

    .expandable-card[data-color="blue"] .expandable-header        { background-color: var(--color-blue); }
    .expandable-card[data-color="green"] .expandable-header       { background-color: var(--color-green); }
    .expandable-card[data-color="red"] .expandable-header          { background-color: var(--color-red); }
    .expandable-card[data-color="yellow"] .expandable-header      { background-color: var(--color-yellow); }
    .expandable-card[data-color="cyan"] .expandable-header         { background-color: var(--color-cyan); }
    .expandable-card[data-color="purple"] .expandable-header      { background-color: var(--color-purple); }
    .expandable-card[data-color="teal"] .expandable-header         { background-color: var(--color-teal); }
    .expandable-card[data-color="orange"] .expandable-header       { background-color: var(--color-orange); }
    .expandable-card[data-color="indigo"] .expandable-header      { background-color: var(--color-indigo); }
    .expandable-card[data-color="pink"] .expandable-header         { background-color: var(--color-pink); }
    .expandable-card[data-color="gray"] .expandable-header         { background-color: var(--color-gray); }
    .expandable-card[data-color="dark-gray"] .expandable-header    { background-color: var(--color-dark-gray); }
    .expandable-card[data-color="dark-green"] .expandable-header   { background-color: var(--color-dark-green); }
    .expandable-card[data-color="deep-pink"] .expandable-header    { background-color: var(--color-deep-pink); }
    .expandable-card[data-color="light-gray"] .expandable-header   { background-color: var(--color-light-gray); }
    .expandable-card[data-color="orange-red"] .expandable-header   { background-color: var(--color-orange-red); }
    .expandable-card[data-color="sea-green"] .expandable-header    { background-color: var(--color-sea-green); }
    .expandable-card[data-color="steel-blue"] .expandable-header   { background-color: var(--color-steel-blue); }
    .expandable-card[data-color="amethyst"] .expandable-header    { background-color: var(--color-amethyst); }
    .expandable-card[data-color="coral"] .expandable-header       { background-color: var(--color-coral); }

    .expandable-header {
        color: white;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 500;
    }

    .expandable-header:hover {
        opacity: 0.9;
    }

    .card-body {
        background: white;
        padding: 15px;
        border-top: 1px solid #e0e0e0;
    }

    .button-group-uniform {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 12px;
        padding: 10px;
        justify-items: center;
    }

    .report-btn {
        width: 100%;
        min-height: 58px;
        padding: 8px 6px;
        border-radius: 8px;
        background-color: #6c757d !important;
        color: white !important;
        font-size: 0.82rem;
        font-weight: 500;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.25 0.25s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        white-space: normal !important;
        line-height: 1.3;
    }

    .report-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.22) !important;
    }

    .report-btn i {
        font-size: 1.4em;
        margin: 0 !important;
    }

    .report-btn span {
        font-size: 0.78rem;
        word-wrap: break-word;
    }

    @media (max-width: 992px) {
        .button-group-uniform {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .expandable-list {
            grid-template-columns: 1fr;
        }

        .button-group-uniform {
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .report-btn {
            min-height: 62px;
        }
    }

    @media (max-width: 480px) {
        .button-group-uniform {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('.collapse').collapse({
            toggle: false
        });

        $('.icon-toggle').on('click', function(e) {
            e.stopPropagation();
            var target = $(this).data('target');
            var $target = $(target);
            var $icon = $(this);
            var $card = $target.closest('.expandable-card');

            if ($target.hasClass('show')) {
                $card.css({ transform: 'scale(1)', boxShadow: '0 4px 8px rgba(0,0,0,0.1)' })
                    .animate({ opacity: 0 }, 500, 'easeInOutCubic', function() {
                        $target.collapse('hide');
                        $card.css({ transform: 'scale(0.95)', boxShadow: '0 1px 2px rgba(0,0,0,0.05)' });
                    });
            } else {
                $target.closest('.expandable-list')
                    .find('.collapse.show')
                    .not($target)
                    .collapse('hide');
                $target.css({ display: 'block', opacity: 0, transform: 'translateY(-10px)' })
                    .animate({ opacity: 1, transform: 'translateY(0)' }, 500, 'easeInOutCubic', function() {
                        $target.collapse('show');
                        $card.css({ transform: 'scale(1)', boxShadow: '0 4px 8px rgba(0,0,0,0.1)' });
                    });
            }

            $target.off('shown.bs.collapse hidden.bs.collapse');
            $target.on('shown.bs.collapse', function() {
                $icon.removeClass('fa-plus').addClass('fa-minus');
            });
            $target.on('hidden.bs.collapse', function() {
                $icon.removeClass('fa-minus').addClass('fa-plus');
            });
        });

        $('.expandable-header').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });

        $('.modal').on('shown.bs.modal', function() {
            $(this).find('[autofocus]').focus();
        }).on('hidden.bs.modal', function() {
            $(document.body).addClass('modal-open');
            $(this).attr('aria-hidden', 'false').removeAttr('inert');
            $(document).off('focusin.modal');
        });
    });
</script>
@endsection
