@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Pagos')

@section('content')
    <section class="content">
        <div class="right_col" payment="main">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Pagos</h2>
                        <div class="row">
                            @include('payments.create')
                            @include('payments.clientPayments')
                            @include('payments.waterConnectionPayments')
                            <div class="col-12 order-first">
                                <form id="formSearch" method="GET" action="{{ route('payments.index') }}" class="mb-2">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-4 mt-2">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                                <input type="text" name="name" id="searchName" class="form-control"
                                                    placeholder="Buscar por nombre de cliente"
                                                    value="{{ request('name') }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-5 mt-2">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                                <input type="text" name="period" id="searchPeriod" class="form-control"
                                                    placeholder="Buscar por Fecha ejemplo: enero / 2024"
                                                    value="{{ request('period') }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2 pe-1 mt-2">
                                            <button type="submit" class="btn btn-primary w-100" title="Buscar">
                                                <i class="fas fa-search me-1"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <button type="button" class="btn btn-success flex-grow-1 flex-md-grow-0 mt-2 mr-1" data-toggle="modal"
                                        data-target="#createPayment" title="Registrar Pago">
                                        <i class="fa fa-plus"></i>
                                        <span class="d-none d-md-inline">Registrar Pago</span>
                                        <span class="d-inline d-md-none">Registrar Pago</span>
                                    </button>
                                    <a type="button" class="btn btn-secondary flex-grow-1 flex-md-grow-0 mt-2 ml-1" target="_blank"
                                        href="{{ route('report.current-customers') }}" title="Clientes al Día">
                                        <i class="fas fa-file-pdf"></i>
                                        <span class="d-none d-md-inline">Clientes al Día</span>
                                        <span class="d-inline d-md-none">Clientes al Día</span>
                                    </a>
                                    <button type="button" class="btn bg-maroon flex-grow-1 flex-md-grow-0 ml-1 mt-2 mr-2" data-toggle="modal"
                                        data-target="#clientPayments" title="Pagos por Cliente">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span class="d-none d-md-inline">Pagos por Cliente</span>
                                        <span class="d-inline d-md-none">Por Cliente</span>
                                    </button>
                                    <button type="button" class="btn bg-purple flex-grow-1 flex-md-grow-0 mt-2 ml-1" data-toggle="modal"
                                        data-target="#waterConnectionPayments" title="Pagos por Toma">
                                        <i class="fas fa-fw fa-water"></i>
                                        <span class="d-none d-md-inline">Pagos por Toma</span>
                                        <span class="d-inline d-md-none">Por Toma</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="payments" class="table table-striped display responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID PAGO</th>
                                                <th>CLIENTE</th>
                                                <th>DEUDA</th>
                                                <th>FECHA DE PAGO</th>
                                                <th>MONTO</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($payments) <= 0)
                                                <tr>
                                                    <td colspan="5">No hay resultados</td>
                                                </tr>
                                            @else
                                                @foreach($payments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>{{ $payment->debt->customer->user->name ?? 'Desconocido' }} {{ $payment->debt->customer->user->last_name ?? 'Desconocido' }}</td>
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($payment->debt->start_date)->locale('es')->isoFormat('MMMM [/] YYYY')}} -
                                                            {{ \Carbon\Carbon::parse($payment->debt->end_date)->locale('es')->isoFormat('MMMM [/] YYYY') }}
                                                            | Deuda: ${{ number_format($payment->debt->amount, 2) }}
                                                        </td>
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($payment->created_at)->locale('es')->isoFormat('MMMM [/] YYYY')}}
                                                        </td>
                                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                                        <td>
                                                            <div class="btn-group" payment="group" aria-label="Opciones">
                                                                <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $payment->id }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @can('editPayment')
                                                                <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#editPayment{{$payment->id}}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                @endcan
                                                                @can('deletePayment')
                                                                <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $payment->id }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                                @endcan
                                                                <a type="button" class="btn btn-block bg-gradient-secondary mr-2" target="_blank" title="Generar Recibo"
                                                                    href="{{ route('reports.receiptPayment', Crypt::encrypt($payment->id)) }}">
                                                                    <i class="fas fa-file-invoice"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                        @include('payments.delete')
                                                        @include('payments.edit')
                                                        @include('payments.show')
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                       {!! $payments->links('pagination::bootstrap-4') !!}
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
<script>
$(document).ready(function() {
    $('#createPayment').on('shown.bs.modal', function() {
        $('.select2').select2({
            dropdownParent: $('#createPayment')
        });
    });

    $('#waterCustomerId').on('change', function() {
        const customerId = $(this).val();
        const waterConnectionSelect = $('#waterConnectionId');

        waterConnectionSelect.empty().append('<option value="">Selecciona una toma</option>');

        if (customerId) {
            $.ajax({
                url: '{{ route("getWaterConnectionsByCustomer") }}',
                method: 'GET',
                data: { waterCustomerId: customerId },
                success: function(response) {
                    $.each(response.waterConnections, function(index, connection) {
                        const connectionId = connection.id;
                        const connectionName = connection.name;

                        if (connectionId && connectionName) {
                            waterConnectionSelect.append(`<option value="${connectionId}">${connectionId} - ${connectionName}</option>`);
                        }
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error', textStatus, errorThrown);
                }
            });
        }
    });

    $('#water_connection_id').change(function() {
        var waterConnectionId = $(this).val();
        if (waterConnectionId) {
            $.ajax({
                url: '{{ route("getDebtsByWaterConnection") }}',
                type: 'GET',
                data: { water_connection_id: waterConnectionId },
                success: function(response) {
                    $('#debt_id').empty();
                    $('#debt_id').append('<option value="">Selecciona una deuda</option>');
                    $.each(response.debts, function(index, debt) {
                        $('#debt_id').append('<option value="' + debt.id + '" data-remaining-amount="' + debt.remaining_amount + '">' + debt.start_date + ' - ' + debt.end_date + ' | Monto: ' + debt.amount + '</option>');
                    });
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                }
            });
        } else {
            $('#debt_id').empty().append('<option value="">Selecciona una deuda</option>');
        }
    });

    $('#debt_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var remainingAmount = selectedOption.data('remaining-amount');

        if (remainingAmount !== undefined) {
            var roundedAmount = parseFloat(remainingAmount).toFixed(2);
            $('#suggested_amount').text('Saldo pendiente: $' + roundedAmount);
        } else {
            $('#suggested_amount').text('');
        }
    });

    $('#payments').DataTable({
        responsive: true,
        paging: false,
        info: false,
        searching: false
    });

    var successMessage = "{{ session('success') }}";
    var errorMessage = "{{ session('error') }}";

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: successMessage,
            confirmButtonText: 'Aceptar'
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            confirmButtonText: 'Aceptar'
        });
    }

    $('#formSearch').on('submit', function(e) {
        var period = $('#searchPeriod').val();
        var regex = /^(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre)\/\d{4}$/i;

        if (period && !regex.test(period)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Formato inválido',
                text: 'El formato debe ser "mes/año".'
            });
        }
    });
});

$('#clientPayments').on('shown.bs.modal', function(){
    $('.select2').select2({
        dropdownParent: $('#clientPayments')
    });
});

$('#waterConnectionPayments').on('shown.bs.modal', function(){
    $('.select2').select2({
        dropdownParent: $('#waterConnectionPayments')
    });
});

$('#customer_id').on('change', function() {
    var customerId = $(this).val();

    if (customerId) {
        $.ajax({
            url: "{{ route('getWaterConnections') }}",
            type: "GET",
            data: { customer_id: customerId },
            success: function(response) {
                var waterConnectionSelect = $('#water_connection_id');
                waterConnectionSelect.empty();
                waterConnectionSelect.append('<option value="">Selecciona una toma</option>');

                $.each(response.waterConnections, function(index, waterConnection) {
                    waterConnectionSelect.append(
                        '<option value="' + waterConnection.id + '">' + waterConnection.id + ' - ' + waterConnection.name + '</option>'
                    );
                });
                waterConnectionSelect.trigger('change');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar las tomas de agua para el cliente seleccionado.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    } else {
        $('#water_connection_id').empty().append('<option value="">Selecciona una toma</option>');
    }
});
</script>
@endsection
