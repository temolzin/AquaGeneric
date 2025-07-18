@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Pagos adelantados')

@section('content')
<section class="content">
    <h2>Pagos adelantados</h2>

    @include('advancePayments.advancePaymentsReportForm')
    @include('advancePayments.paymentHistoryModal')


    <div class="row">
        <div class="col-lg-12 text-right">
            <div class="btn-group" role="group" aria-label="Acciones de gráfica de pagos">
                <button class="btn btn-primary mr-2" id="btnGenerateReportGraph">
                    <i class="fa fa-chart-pie"></i> Generar PDF de Gráficos
                </button>
                <button class="btn btn-success mr-2" data-toggle="modal" data-target="#paymentHistoryModal" title="Historial de pagos">
                    <i class="fa fa-calendar"></i> Historial de Pagos
                </button>
                <button type="button" class="btn bg-teal mr-2" data-toggle="modal"
                    data-target="#generateAdvancePaymentsReportModal">
                    <i class="fas fa-file-pdf"></i> Reporte de Pagos Adelantados
                </button>
            </div>
        </div>
    </div>

    @php
        $chartHeight = '250px';
    @endphp

    <div class="row mt-5">
        @foreach ([
            ['id' => 'barChart', 'title' => 'Gráfica de Barras'],
            ['id' => 'lineChart', 'title' => 'Gráfica de Líneas'],
            ['id' => 'pieChart', 'title' => 'Gráfica de Pastel'],
            ['id' => 'doughnutChart', 'title' => 'Gráfica de Dona']
        ] as $chart)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <strong class="flex-grow-1">{{ $chart['title'] }}</strong>
                        <button class="btn btn-sm btn-outline-dark download-btn" data-canvas="{{ $chart['id'] }}">
                            <i class="fas fa-download"></i> Descargar
                        </button>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="height: {{ $chartHeight }};">
                        <canvas id="{{ $chart['id'] }}"></canvas>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection

<style>
    #btnGenerateReportGraph:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>

@push('js')
<script>

    const labels = @json($months);
    const data = @json($totals);

    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pagos',
                data: data,
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pagos',
                data: data,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40,167,69,0.2)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pagos',
                data: data,
                backgroundColor: 'rgba(255,99,132,0.2)',
                borderColor: 'rgba(255,99,132,1)',
                pointBackgroundColor: 'rgba(255,99,132,1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    document.querySelectorAll('.download-btn').forEach(button => {
        button.addEventListener('click', function () {
            const canvasId = this.dataset.canvas;
            const canvas = document.getElementById(canvasId);
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = `${canvasId}.png`;
            link.click();
        });
    });
</script>
@endpush

@push('js')
<script>
    document.getElementById('btnGenerateReportGraph')?.addEventListener('click', () => {
    const chartIds = ['barChart', 'lineChart', 'pieChart','doughnutChart'];
    const chartImages = chartIds.map(id => {
        const canvas = document.getElementById(id);
        return canvas.toDataURL('image/png');
    });

    const formGenerateReportGraphPayments = document.createElement('form');
    formGenerateReportGraphPayments.method = 'POST';
    formGenerateReportGraphPayments.action = '{{ route('report.advancePaymentGraphReport') }}';
    formGenerateReportGraphPayments.target = '_blank';

    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = '{{ csrf_token() }}';
    formGenerateReportGraphPayments.appendChild(tokenInput);

    const chartsInput = document.createElement('input');
    chartsInput.type = 'hidden';
    chartsInput.name = 'charts';
    chartsInput.value = JSON.stringify(chartImages);
    formGenerateReportGraphPayments.appendChild(chartsInput);

    document.body.appendChild(formGenerateReportGraphPayments);
    formGenerateReportGraphPayments.submit();
});
</script>
@endpush
