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
                                        <div class="d-flex flex-md-row flex-column ">
                                            <button type="button" class="btn btn-info w-100 w-md-auto m-1" data-toggle="modal"
                                            data-target="#annualEarnings" title="Ingresos Anuales">
                                                <i class="fa fa-dollar-sign"></i> Ingresos Anuales
                                            </button>
                                            <button type="button" class="btn bg-olive w-100 w-md-auto m-1" data-toggle="modal"
                                            data-target="#weeklyEarnings" title="Ingresos Semanales">
                                                <i class="fa fa-dollar-sign"></i> Ingresos Semanales
                                            </button>
                                            <button type="button" class="btn btn-info w-100 w-md-auto m-1" data-toggle="modal"
                                            data-target="#annualExpenses" title="Egresos Anuales">
                                                <i class="fa fa-dollar-sign"></i> Egresos Anuales
                                            </button>
                                            <button type="button" class="btn bg-olive w-100 w-md-auto m-1" data-toggle="modal"
                                            data-target="#weeklyExpenses" title="Egresos Semanales">
                                                <i class="fa fa-dollar-sign"></i> Egresos Semanales
                                            </button>
                                            <button type="button" class="btn btn-info w-100 w-md-auto m-1" data-toggle="modal"
                                            data-target="#annualGains" title="Ganancias Anuales">
                                                <i class="fa fa-dollar-sign"></i> Ganancias Anuales
                                            </button>
                                            <button type="button" class="btn bg-olive w-100 w-md-auto m-1" data-toggle="modal"
                                            data-target="#weeklyGains" title="Ganancias Semanales">
                                                <i class="fa fa-dollar-sign"></i> Ganancias Semanales
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('viewCustomerDebts')
                        <div class="row mb-1">
                            <div class="col-lg-6 col-xs-12">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>${{ number_format($totalOwed, 2) }}</h3>
                                        <p>Total Adeudado</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-faucet"></i>
                                    </div>
                                    <a href="{{ route('viewCustomerDebts.index') }}" class="small-box-footer">Más información <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xs-12">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $pendingDebts }}</h3>
                                        <p>Deudas Pendientes</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <a href="{{ route('viewCustomerDebts.index') }}" class="small-box-footer">Más información <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-lg-6 col-xs-12">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $waterConnections->count() }}</h3>
                                        <p>Tomas de agua</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <a href="{{ route('viewCustomerWaterConnections.index') }}" class="small-box-footer">Más información <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xs-12">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>{{ $totalDebts }}</h3>
                                        <p>Total Deudas</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <a href="{{ route('viewCustomerDebts.index') }}" class="small-box-footer">Más información <i class="fa fa-arrow-circle-right"></i></a>
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
                    @can('viewDashboardCards')
                    <div class="card">
                        <div class="card-header">
                            <div class="row w-100 align-items-center">
                                <div class="col-12 col-md-10">
                                    <h3 class="card-title m-0">Períodos Próximos a Vencer</h3>
                                </div>
                                <div class="col-12 col-md-auto mt-2 mt-md-0 ms-md-auto">
                                    <form action="{{ route('dashboard.sendEmailsForDebtsExpiringSoon') }}" method="POST" class="w-100">
                                        @csrf
                                        <button type="submit" class="btn {{ $hasMailConfig ? 'btn-primary' : 'btn-secondary disabled' }} btn-sm w-100 w-md-auto" title="{{ $hasMailConfig
                                                ? 'Enviar correos de recordatorio' : 'No hay configuración de correo válida para esta localidad' }}" {{ $hasMailConfig ? '' : 'disabled' }}>
                                            <i class="fas fa-envelope"></i> Enviar recordatorios
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-box table-responsive">
                            <table id="emails" class="table table-striped display responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Cliente</th>
                                        <th>Email</th>
                                        <th>Toma de Agua</th>
                                        <th>Fecha de Término</th>
                                        <th>Días Restantes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($data['paidDebtsExpiringSoon']) <= 0)
                                        <tr>
                                            <td colspan="6">No hay resultados</td>
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
                                                <td>{{ $period['customerEmail'] }}</td>
                                                <td>{{ $period['waterConnectionName'] }}</td>
                                                <td>{{ $period['endDate'] }}</td>
                                                <td>{{ $period['daysRemaining'] }} días</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $data['paidDebtsExpiringSoon']->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                    @endcan
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
            var errorMessage = "{{ session('error') }}";
            if (errorMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'Aceptar'
                });
            }
            var warningMessage = "{{ session('warning') }}";
            if (warningMessage) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: warningMessage,
                    confirmButtonText: 'Aceptar'
                });
            }   
            $('#emails').DataTable({
                responsive: true,
                paging: false,
                info: false,
                searching: false
            });
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
