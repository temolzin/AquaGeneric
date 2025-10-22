@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Mis Tomas de Agua')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title mb-3">
                    <h2>Mis Tomas de Agua</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-lg-flex justify-content-between align-items-center flex-wrap">
                                <form method="GET" action="{{ route('viewCustomerWaterConnections.index') }}" class="mb-3 mb-lg-0" style="min-width: 300px;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por ID, Nombre, Tipo" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Tomas">
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
                                <table id="waterConnections" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
                                            <th>COSTO</th>
                                            <th>TIPO</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($connections) <= 0)
                                        <tr class="text-center py-5">
                                            <td colspan="6">
                                                <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                                                <h1 style="font-size: 1.3em;">¡No hay tomas de agua registradas!</h1>
                                                <p class="text-muted">Aún no tienes tomas de agua asociadas a tu cuenta.</p>
                                            </td>
                                        </tr>
                                        @else
                                        @foreach($connections as $connection)
                                        <tr>
                                            <td scope="row">{{ $connection->id }}</td>
                                            <td>{{ $connection->name }}</td>
                                            <td>${{ number_format($connection->cost->price, 2) }}</td>
                                            <td>
                                                @if ($connection->type === 'residencial')
                                                    Residencial
                                                @elseif ($connection->type === 'commercial')
                                                    Comercial
                                                @else
                                                    {{ $connection->type }}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Opciones">
                                                    <button type="button" class="btn btn-info mr-2" data-toggle="modal" 
                                                            title="Ver Detalles" data-target="#view{{ $connection->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center mt-3">
                                    {!! $connections->links('pagination::bootstrap-4') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@foreach($connections as $connection)
    @include('viewCustomerWaterConnections.show', ['connection' => $connection])
@endforeach
@endsection

@section('css')
<style>
    .badge {
        font-size: 0.85em;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#waterConnections').DataTable({
            responsive: true,
            buttons: ['csv', 'excel', 'print'],
            dom: 'Bfrtip',
            paging: false,
            info: false,
            searching: false,
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
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
