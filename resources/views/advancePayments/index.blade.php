@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Pagos adelantados')

@section('content')

<h2>Pagos adelantados</h2>

<div class="row mb-4">
    <div class="col-lg-12 text-right">
        <div class="btn-group" role="group" aria-label="Acciones de gráfica de pagos">
            <button class="btn btn-primary mr-2" id="btnGenerateReportGraph" data-toggle='modal' data-target="#paymentChart">
                <i class="fa fa-money-bill"></i> Gráfica de pagos
            </button>

            <a type="button" class="btn btn-success mr-2" target="_blank" title="Historial de pagos" href="#">
                <i class="fas fa-clipboard"></i> Historial de pagos
            </a>

            <button class="btn btn-secondary mr-2" data-toggle='modal' data-target="#paymentChart">
                <i class="fa fa-dollar-sign"></i> Comprobante de pagos
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
        ['id' => 'radarChart', 'title' => 'Gráfica de Radar']
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

@endsection

@section('js')
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

    new Chart(document.getElementById('radarChart'), {
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
            maintainAspectRatio: false,
            elements: {
                line: {
                    borderWidth: 3
                }
            }
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
@endsection

@section('js')
<script>
    document.getElementById('btnGenerateReportGraph').addEventListener('click', async () => {
        const chartIds = ['barChart', 'lineChart', 'pieChart'];
        const chartImages = chartIds.map(id => {
            const canvas = document.getElementById(id);
            return canvas.toDataURL('image/png');
        });

        const response = await fetch('{{ route('report.advancePaymentGraphReport') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ charts: chartImages })
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            window.open(url, '_blank');
        } 
    });
</script>
@endsection
