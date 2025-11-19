@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Categorías de Incidencia')

@section('content')
<section class="content">
    <div class="right_col" cost="main">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Categoría de Incidencias</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                <button type="button" class="btn btn-success flex-grow-1 flex-md-grow-0 mt-2 mr-1"
                                        data-toggle="modal" data-target="#create" title="Registrar Categoría">
                                    <i class="fa fa-plus"></i>
                                    <span class="d-none d-md-inline">Registrar Categoría</span>
                                    <span class="d-inline d-md-none">Registrar Categoría</span>
                                </button>
                                <a type="button" class="btn btn-secondary flex-grow-1 flex-md-grow-0 mt-2 ml-1"
                                target="_blank" title="Generar Lista"
                                href="{{ route('report.generateIncidentCategoyListReport') }}">
                                    <i class="fas fa-file-pdf"></i>
                                    <span class="d-none d-md-inline">Generar Lista</span>
                                    <span class="d-inline d-md-none">Generar Lista</span>
                                </a>
                            </div>
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
                                                    <td>
                                                        <span class="badge status-badge" style="background-color: {{ $category->color ?? '#6c757d' }}; color: white;">
                                                            {{ $category->name }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $category->description }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $category->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if (!is_null($category->locality_id))
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
                                                                        {{ $category->hasDependencies() ? 'disabled' : 'data-toggle=modal data-target=#delete' . $category->id }}>
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                @endcan
                                                            @endif
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
                                    <div class="d-flex justify-content-center">
                                        {!! $categories->links('pagination::bootstrap-4') !!}
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

@section('css')
<style>
    .status-badge {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .table-dark .status-badge {
        border: 1px solid rgba(255,255,255,0.1);
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#incidentCategories').DataTable({
            responsive: true,
            buttons: ['csv', 'excel', 'print'],
            dom: 'Bfrtip',
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

        $('#create').on('shown.bs.modal', function() {
            $('.select2').select2({
                dropdownParent: $('#create')
            });
        });

        $('[id^="edit"]').on('shown.bs.modal', function() {
            $('.select2').select2({
                dropdownParent: $(this)
            });
        });
    });
</script>
@endsection
