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
                            <div class="col-lg-12 text-right">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createPayment">
                                    <i class="fa fa-plus"></i> Registrar Pago
                                </button>
                                <a type="button" class="btn btn-secondary" target="_blank" title="Customers" href="{{ route('report.current-customers') }}">
                                    <i class="fas fa-users"></i> Clientes al Día
                                </a>
                                </button>
                                <button type="button" class="btn bg-maroon" data-toggle="modal" data-target="#clientPayments">
                                    <i class="fas fa-money-bill-wave"></i> Pagos por Cliente
                                </button>
                                <button type="button" class="btn bg-purple" data-toggle="modal" data-target="#waterConnectionPayments">
                                    <i class="fas fa-fw fa-water"></i> Pagos por Toma de agua
                                </button>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-8">
                                <form method="GET" action="{{ route('payments.index') }}" class="my-3">
                                    <div class="input-group my-3">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" name="name" id="searchName" class="form-control" placeholder="Buscar por nombre de cliente" value="{{ request('name') }}">
                                        <input type="text" name="period" id="searchPeriod" class="form-control" placeholder="Buscar por Fecha ejemplo: enero / 2024" value="{{ request('period') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">Buscar</button>
                                        </div>
                                    </div>
                                </form>
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
                                                        <td>{{ $payment->debt->customer->name ?? 'Desconocido' }} {{ $payment->debt->customer->last_name ?? 'Desconocido' }}</td>
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($payment->debt->start_date)->locale('es')->isoFormat('MMMM [/] YYYY')}} - 
                                                            {{ \Carbon\Carbon::parse($payment->debt->end_date)->locale('es')->isoFormat('MMMM [/] YYYY') }}
                                                            | Deuda: ${{ number_format($payment->debt->amount, 2) }}
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
