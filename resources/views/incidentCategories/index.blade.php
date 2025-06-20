@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Categorías de Incidencia')

@section('content')
<section class="content">
    <div class="right_col" cost="main">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Categorías de Incidencia</h2>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#create">
                                <i class="fa fa-plus"></i> Registrar Categoría
                            </button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="incidentCategories" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
                                            <th>DESCRIPCIÓN</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($categories) <= 0)
                                            <tr>
                                                <td colspan="4">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach($categories as $category)
                                                <tr>
                                                    <td>{{ $category->id }}</td>
                                                    <td>{{ $category->name }}</td>
                                                    <td>{{ $category->description }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $category->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @can('editIncidentCategories')
                                                                <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Registro" data-target="#edit{{ $category->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @endcan
                                                            @can('deleteIncidentCategories')
                                                                <button
                                                                    type="button"
                                                                    class="btn {{ $category->hasDependencies() ? 'btn-secondary' : 'btn-danger' }} mr-2"
                                                                    title="{{ $category->hasDependencies() ? 'Eliminación no permitida: Existen incidencias asociadas a esta categoría.' : 'Eliminar Registro' }}"
                                                                    {{ $category->hasDependencies() ? 'disabled' : 'data-toggle=modal data-target=#delete' . $category->id }}
                                                                >
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('incidentCategories.edit')
                                                @include('incidentCategories.delete')
                                                @include('incidentCategories.show')
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('incidentCategories.create')
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
        $('#incidentCategories').DataTable({
            responsive: true,
            buttons: ['excel', 'pdf', 'print'],
            dom: 'Bfrtip',
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
</script>
@endsection
