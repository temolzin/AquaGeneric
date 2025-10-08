@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Mis Pagos')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title mb-3">
                    <h2>Mis Pagos</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-lg-flex justify-content-between align-items-center flex-wrap">
                                <form method="GET" action="{{ route('viewCustomerPayments.index') }}" class="mb-3 mb-lg-0" style="min-width: 300px;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Pagos">
                                                <i class="fa fa-search"></i> Buscar
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
                            <div class="card-box table-responsive">
                                <table id="myPayments" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>TOMA DE AGUA</th>
                                            <th>FECHA</th>
                                            <th>HORA</th>
                                            <th>MONTO</th>
                                            <th>MÉTODO</th>
                                            <th>RECIBO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($payments) <= 0)
                                        <tr>
                                            <td colspan="8" class="text-center">No hay pagos registrados</td>
                                        </tr>
                                        @else
                                        @foreach($payments as $payment)
                                        <tr>
                                            <td scope="row">{{ $payment->id }}</td>
                                            <td>
                                                @if($payment->debt && $payment->debt->waterConnection)
                                                    {{ $payment->debt->waterConnection->name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $payment->created_at->format('H:i a') }}</td>
                                            <td>${{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                @php
                                                    $methodLabels = [
                                                        'cash' => 'Efectivo',
                                                        'transfer' => 'Transferencia', 
                                                        'card' => 'Tarjeta'
                                                    ];
                                                @endphp
                                                <span>{{ $methodLabels[$payment->method] ?? $payment->method }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Opciones">
                                                    <a href="{{ route('viewCustomerPayments.receipt', Crypt::encrypt($payment->id)) }}" 
                                                       class="btn btn-info mr-2" 
                                                       target="_blank"
                                                       title="Descargar Comprobante">
                                                        <i class="fas fa-receipt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                
                                <div class="d-flex justify-content-center mt-3">
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
        $('#myPayments').DataTable({
            responsive: true,
            buttons: ['csv', 'excel', 'print'],
            dom: 'Bfrtip',
            paging: false,
            info: false,
            searching: false,
            order: [[1, 'desc']] 
        });

        $('[data-toggle="tooltip"]').tooltip();

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
</script>
@endsection
