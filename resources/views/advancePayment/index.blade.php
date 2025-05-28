@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | adelantados')

@section('content')
    <section class="content">
        <div class="right_col" payment="main">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Pagos Anticipados</h2>
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button type="button" id="sendChart" class="btn btn-success">
                                    <i class="fa fa-plus"></i> Gráfica de Pagos
                                </button>
                                    <button type="button" class="btn btn-success">
                                    <i class="fa fa-plus"></i> Historial de pagos
                                </button>
                                    <button type="button" class="btn btn-success">
                                    <i class="fa fa-plus"></i> Comprobante de pagos
                                </button>
                            </div>
                            <div class="clearfix"></div>
                            <div class="container">
                                <table style="width: 100%;">
                                    <tr>
                                        <td><canvas id="paymentsChart" style="max-width: 400px; max-height: 200px;"></canvas></td>
                                        <td><canvas id="paymetsbar" style="max-width: 400px; max-height: 200px;"></canvas></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><canvas id="paymetspie" style="max-width: 400px; max-height: 200px;"></canvas></td>
                                    </tr>
                                </table>
                            </div>                     
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const lineGraph = document.getElementById('paymentsChart').getContext('2d');
        const paymentsChart = new Chart(lineGraph, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [{
                    label: '',
                    data: {!! json_encode($earningsPerMonth) !!},
                    borderColor: 'rgba(54, 162, 235, 1)', 
                    borderWidth: 2, 
                    backgroundColor: 'rgba(0, 0, 0, 0)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false } 
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const data2 = {
            labels: {!! json_encode($months) !!}, 
            datasets: [
                {
                    axis: 'y',
                    label: '',
                    data: {!! json_encode($advancePayments) !!}, 
                    backgroundColor: 'rgba(255, 159, 64, 0.6)', 
                    borderColor: 'rgba(255, 159, 64, 1)',       
                    borderWidth: 1                              
                }
            ]
        };
        const config2 = {
            type: 'bar',
            data: data2,
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                        position: 'top'
                    }
                },
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true } 
                }
            }
        };
        const barGraph = document.getElementById('paymetsbar').getContext('2d');
        const paymetsbar = new Chart(barGraph, config2);

        const data3 = {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: '',
                data: {!! json_encode($earningsPerMonth) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(255, 205, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(201, 203, 207, 0.7)',
                    'rgba(255, 99, 71, 0.7)',
                    'rgba(255, 165, 0, 0.7)',
                    'rgba(154, 205, 50, 0.7)',
                    'rgba(0, 191, 255, 0.7)',
                    'rgba(199, 21, 133, 0.7)'
                ]
            }]
        };
        const config3 = {
            type: 'pie',
            data: data3,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right', 
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                        }
                    }
                }
            }
        };
        const pieGraph  = document.getElementById('paymetspie').getContext('2d');
        const paymetspie = new Chart(pieGraph , config3);

        const saveChart = (canvasId) => {
            const canvas = document.getElementById(canvasId);
            return canvas.toDataURL('image/png');
        };
        document.getElementById('sendChart').addEventListener('click', function () {
            const chartsData = [
                saveChart('paymentsChart'),
                saveChart('paymetsbar'),
                saveChart('paymetspie'),
            ];

            fetch('{{ route("reports.saveCharts") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ charts: chartsData }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.images) {
                    window.open('{{ route("reports.paymentGraph") }}', '_blank');
                }
            })  
        });
    });
</script>
@endsection
