@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Panel')

@section('content')
    <section class="content">
        <div class="right_col" role="main">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="row">
                        <div class="container-fluid">
                            <div class="card-box head">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center">
                                        @if ($authUser->getFirstMediaUrl('userGallery'))
                                            <img src="{{ $authUser->getFirstMediaUrl('userGallery') }}"
                                                alt="Foto de {{ $authUser->name }}">
                                        @else
                                            <img src="{{ asset('img/userDefault.png') }}">
                                        @endif
                                    </div>

                                    <div class="col-md-8">
                                        <h4 class="font-weight-bold text-capitalize welcome">Bienvenid@</h4>
                                        <h1 class="font-weight-bold text-blue">{{ $authUser->name }} {{ $authUser->last_name }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @can('viewDashboardCards')
                        @if ($data['noDebtsForCurrentMonth'])
                            <div class="alert alert-warning" role="alert">
                                Ya ha iniciado un nuevo mes y no se han asignado deudas a los Usuarios para este periodo.
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-lg-4 col-xs-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $data['customersByLocality'] }}</h3>
                                        <p>Total de Clientes</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <a href="{{ route('customers.index') }}" class="small-box-footer">Más información <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xs-6">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3>{{ $data['customersWithoutDebts'] }}</h3>
                                        <p>Clientes al Día</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <a href="{{ route('report.current-customers') }}" class="small-box-footer" target="_blank">Más información <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xs-6">
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <h3>{{ $data['customersWithDebts'] }}</h3>
                                        <p>Clientes con Deudas</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <a href="{{ route('report.with-debts') }}" class="small-box-footer" target="_blank">Más información <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @include('payments.annualEarnings')
                            @include('payments.weeklyEarnings')
                            @include('generalExpenses.weeklyExpenses')
                            @include('generalExpenses.weeklyGains')
                            @include('generalExpenses.annualExpenses')
                            @include('generalExpenses.annualGains')
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Reportes</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="column">
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#annualEarnings">
                                                <i class="fa fa-dollar-sign"></i> Ingresos Anuales
                                            </button>
                                            <button type="button" class="btn bg-olive" data-toggle="modal" target="_blank"  data-target="#weeklyEarnings">
                                                <i class="fa fa-dollar-sign"></i> Ingresos Semanales
                                            </button>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#annualExpenses">
                                                <i class="fa fa-dollar-sign"></i> Egresos Anuales
                                            </button>
                                            <button type="button" class="btn bg-olive" data-toggle="modal" target="_blank"  data-target="#weeklyExpenses">
                                                <i class="fa fa-dollar-sign"></i> Egresos Semanales
                                            </button>
                                            <button type="button" class="btn btn-info" data-toggle="modal" target="_blank"  data-target="#annualGains">
                                                <i class="fa fa-dollar-sign"></i> Ganancias Anuales
                                            </button>
                                            <button type="button" class="btn bg-olive" data-toggle="modal" target="_blank"  data-target="#weeklyGains">
                                                <i class="fa fa-dollar-sign"></i> Ganancias Semanales
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan

                    @can('viewLocalityCharts')
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="locality_id" class="form-label">Seleccionar Localidad</label>
                                <select id="mySelect" class="form-control select2" name="locality_id" required>
                                    <option value="">Seleccione una localidad</option>
                                    @foreach($data['localities'] as $locality)
                                        <option value="{{ $locality->id }}" data-name="{{ $locality->name }}" data-municipality="{{ $locality->municipality }}" {{ old('locality_id') == $locality->id ? 'selected' : '' }}>
                                            {{ $locality->name }} , {{ $locality->municipality }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endcan

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Ingresos Mensuales<span id="localityInfoMonthly"></h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="earningsChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Ingresos Anuales por Mes<span id="localityInfoAnnual"></h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="annualEarningsChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="row w-100">
                                <div class="col-md-6 d-flex align-items-center">
                                    <h3 class="card-title m-0">Períodos Próximos a Vencer</h3>
                                </div>
                                <div class="col-md-6 d-flex justify-content-end">
                                    <form action="{{ route('dashboard.sendEmailsForDebtsExpiringSoon') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-envelope"></i> Enviar recordatorios
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Cliente</th>
                                        <th>Toma de Agua</th>
                                        <th>Fecha de Término</th>
                                        <th>Días Restantes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($data['paidDebtsExpiringSoon']) <= 0)
                                        <tr>
                                            <td colspan="5">No hay resultados</td>
                                        </tr>
                                    @else
                                        @foreach($data['paidDebtsExpiringSoon'] as $period)
                                            <tr>
                                                <td>
                                                    <img src="{{ $period['customerPhoto'] }}"
                                                        alt="Foto de cliente"
                                                        style="width: 50px; height: 50px; border-radius: 50%;">
                                                </td>
                                                <td>{{ $period['customerName'] }}</td>
                                                <td>{{ $period['waterConnectionName'] }}</td>
                                                <td>{{ $period['endDate'] }}</td>
                                                <td>{{ $period['daysRemaining'] }} días</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
    <style>
        .select2-container .select2-selection--single {
            height: 40px;
            display: flex;
            align-items: center;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function(){
            $('#mySelect').select2();

            $('#mySelect').on('change', function(){
                var localityId = $(this).val();
                var selectedOption = $(this).find('option:selected');
                var localityName = selectedOption.data('name');
                var municipality = selectedOption.data('municipality');
                if(localityId){
                    $.ajax({
                        url:"{{ route('locality.earnings') }}",
                        type: 'GET',
                        data: { locality_id: localityId},
                        success: function(response){
                            earningsChart.data.datasets[0].data = response.earningsPerMonth;
                            earningsChart.update();

                            annualEarningsChart.data.datasets[0].data = response.earningsPerMonth;
                            annualEarningsChart.update();
                        },
                        error: function(xhr) {
                            console.log('Error al obtener los datos de la localidad');
                        }
                    });
                    $('#localityInfoMonthly').text(' de ' + localityName + ', ' + municipality);
                    $('#localityInfoAnnual').text(' de ' + localityName + ', ' + municipality);
                } else {
                    earningsChart.data.datasets[0].data = Array(12).fill(0); 
                    earningsChart.update();

                    annualEarningsChart.data.datasets[0].data = Array(12).fill(0); 
                    annualEarningsChart.update();

                    $('#localityInfoMonthly').text('');
                    $('#localityInfoAnnual').text('');
                }
            });
            var successMessage = "{{ session('success') }}";
            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: successMessage,
                    confirmButtonText: 'Aceptar'
                });
            }
        });

        var ctx = document.getElementById('earningsChart').getContext('2d');
        var earningsChart = new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: @json($data['months']),
                datasets: [{
                    label: 'Ingresos en $',
                    data: @json($data['earningsPerMonth']),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        var ctxAnnual = document.getElementById('annualEarningsChart').getContext('2d');
        var annualEarningsChart = new Chart(ctxAnnual, {
            type: 'line',
            data: {
                labels: @json($data['months']),
                datasets: [{
                    label: 'Ingresos Anuales en $',
                    data: @json($data['earningsPerMonth']),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@stop
