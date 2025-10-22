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
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por Id, Dirección" value="{{ request('search') }}">
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
                                        @if(!$customer)
                                            <tr class="text-center py-5">
                                                <td colspan="7">
                                                    <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                                                    <h5>No se encontró información de cliente</h5>
                                                    <p class="text-muted">No hay datos de cliente asociados a tu usuario.</p>
                                                </td>
                                            </tr>
                                        @elseif($payments->count() == 0)
                                            <tr class="text-center py-5">
                                                <td colspan="7">
                                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                                    <h5>No hay pagos registrados</h5>
                                                    <p class="text-muted">Aún no has realizado ningún pago.</p>
                                                </td>
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
                                    </tbody>
                                </table>
                                @endif
                                
                                @if($payments->total() > $payments->perPage())
                                <div class="d-flex justify-content-center mt-3">
                                    {!! $payments->links('pagination::bootstrap-4') !!}
                                </div>
                                @endif
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
        @if($payments->count() > 0)
        $('#myPayments').DataTable({
            responsive: true,
            buttons: ['csv', 'excel', 'print'],
            dom: 'Bfrtip',
            paging: false,
            info: false,
            searching: false,
            order: [[1, 'desc']] 
        });
        @endif

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
