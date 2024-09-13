@extends('adminlte::page')

@section('title', 'Pagos')

@section('content')
    <section class="content">
        <div class="right_col" payment="main">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Pagos</h2>
                        <div class="row">
                            @include('payments.create')
                            @include('payments.annualEarnings')
                            <div class="col-lg-12 text-right">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createPayment">
                                    <i class="fa fa-plus"></i> Registrar Pago
                                </button>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#annualEarnings">
                                    <i class="fa fa-dollar-sign"></i> Ganancias Anuales
                                </button>
                                <a type="button" class="btn btn-secondary" target="_blank" title="Customers" href="{{ route('report.current-customers') }}">
                                    <i class="fas fa-users"></i> Clientes al día
                                </a>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-8">
                                <form method="GET" action="{{ route('payments.index') }}" class="my-3">
                                    <div class="input-group my-3">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" name="name" id="searchName" class="form-control" placeholder="Buscar por nombre de usuario" value="{{ request('name') }}">
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
                                                <th>USUARIO</th>
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
                                                            | Monto: {{ $payment->debt->amount }}
                                                        </td>
                                                        <td>{{ $payment->amount }}</td>
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
                                                                <a type="button" class="btn btn-block bg-gradient-secondary mr-2" target="_blank" title="Generar Perfil"
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
    
    $('#customer_id').change(function() {
        var customerId = $(this).val();
        if (customerId) {
            $.ajax({
                url: '{{ route("getCustomerDebts") }}',
                type: 'GET',
                data: { customer_id: customerId },
                success: function(response) {
                    $('#debt_id').empty();
                    $('#debt_id').append('<option value="">Selecciona una deuda</option>');
                    $.each(response.debts, function(key, value) {
                        $('#debt_id').append('<option value="'+ value.id +'" data-remaining-amount="'+ value.remaining_amount +'">'+ value.start_date +' - '+ value.end_date +' | Monto: '+ value.amount +'</option>');
                    });
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                }
            });
        } else {
            $('#debt_id').empty();
            $('#debt_id').append('<option value="">Selecciona una deuda</option>');
        }
    });

    $('#debt_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var remainingAmount = selectedOption.data('remaining-amount');
        
        if (remainingAmount !== undefined) {
            $('#suggested_amount').text('Monto sugerido a pagar: $' + remainingAmount);
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
        }).then((result) => {
            window.location.href = "{{ route('payments.index') }}";
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            window.location.href = "{{ route('payments.index') }}";
        });
    }
});
</script>
@endsection
