@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Panel de Descuentos')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Panel de Descuentos</h2>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-lg-12 mb-4">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Uso general de descuentos
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="discountChart" height="90"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card card-success card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Descuentos aplicados en pagos
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="paymentChart" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card card-danger card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Descuentos aplicados en deudas
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="debtChart" height="140"></canvas>
                                </div>
                            </div>
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
    const colors = [
        '#3498db',
        '#2ecc71',
        '#f39c12',
        '#e74c3c',
        '#9b59b6',
        '#1abc9c',
        '#34495e',
        '#16a085',
        '#2980b9',
        '#8e44ad'
    ];

    new Chart(document.getElementById('discountChart'), {

        type:'bar',

        data:{
            labels:@json($discountLabels),
            datasets:[{
                label:'Cantidad',
                data:@json($discountData),
                backgroundColor:colors
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false,
            scales:{
                y:{
                    beginAtZero:true
                }
            }
        }

    });

    new Chart(document.getElementById('paymentChart'), {

        type:'bar',

        data:{
            labels:@json($paymentLabels),
            datasets:[{
                label:'Pagos',
                data:@json($paymentData),
                backgroundColor:'#2ecc71'
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false,
            scales:{
                y:{
                    beginAtZero:true
                }
            }
        }

    });

    new Chart(document.getElementById('debtChart'), {

        type:'bar',

        data:{
            labels:@json($debtLabels),
            datasets:[{
                label:'Deudas',
                data:@json($debtData),
                backgroundColor:'#e74c3c'
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false,
            scales:{
                y:{
                    beginAtZero:true
                }
            }
        }
    });
</script>
@endsection
