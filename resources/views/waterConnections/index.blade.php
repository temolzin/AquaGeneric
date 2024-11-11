@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Tomas')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Tomas</h2>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button class="btn btn-success" data-toggle='modal' data-target="#createWaterConnections">
                                <i class="fa fa-plus"></i> Registrar Toma
                            </button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <form method="GET" action="{{ route('waterConnections.index') }}" class="my-3">
                    <div class="input-group w-50">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por ID, Nombre, Propietario" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="waterConnections" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
                                            <th>PROPIETARIO</th>
                                            <th>COSTO</th>
                                            <th>TIPO</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($connections) <= 0)
                                            <tr>
                                                <td colspan="8">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach($connections as $connection)
                                                <tr>
                                                    <td scope="row">{{ $connection->id }}</td>
                                                    <td>{{ $connection->name }}</td>
                                                    <td>{{ $connection->customer->name }} {{ $connection->customer->last_name }}</td>
                                                    <td>${{ $connection->cost->price}}</td>
                                                    @if ($connection->type === 'residencial')
                                                        <td>Residencial</td>
                                                    @elseif ($connection->type === 'commercial')
                                                        <td>Comercial</td>
                                                    @endif
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                            @can('viewWaterConnection')
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $connection->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @endcan
                                                            @can('editWaterConnection')
                                                            <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{ $connection->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @endcan
                                                            @can('deleteWaterConnection')
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $connection->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('waterConnections.show')
                                                @include('waterConnections.edit')
                                                @include('waterConnections.delete')
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('waterConnections.create')
                                <div class="d-flex justify-content-center">
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
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#waterConnections').DataTable({
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

    $('#createWaterConnections').on('shown.bs.modal', function() {
        $('.select2').select2({
                dropdownParent: $('#createWaterConnections')
        });
    });

    $('[id^="edit"]').on('shown.bs.modal', function() {
        $(this).find('.select2').select2({
            dropdownParent: $(this)
        });
    });
</script>
@endsection
