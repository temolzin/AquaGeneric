@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Secciones')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Secciones</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                                <form method="GET" action="{{ route('sections.index') }}" class="flex-grow-1 mt-2" style="min-width: 330px; max-width: 30%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por ID o Nombre" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Sección">
                                                <i class="fas fa-search"></i>
                                                <span class="d-none d-md-inline">Buscar</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <button class="btn btn-success flex-grow-1 flex-lg-grow-0 mt-2" data-toggle='modal' data-target="#createSection" title="Registrar Sección">
                                    <i class="fa fa-plus"></i>
                                    <span class="d-none d-md-inline">Registrar Sección</span>
                                    <span class="d-inline d-md-none">Registrar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="sections" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
                                            <th>CÓDIGO POSTAL</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sections as $section)
                                            <tr>
                                                <td>{{ $section->id }}</td>
                                                <td>{{ $section->name }}</td>
                                                <td>{{ $section->zip_code }}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Opciones">
                                                        @can('viewSections')
                                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $section->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @endcan
                                                        @can('editSections')
                                                        <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Sección" data-target="#edit{{ $section->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @endcan
                                                        @can('deleteSections')
                                                            <button
                                                                type="button"
                                                                class="btn {{ $section->hasDependencies() ? 'btn-secondary' : 'btn-danger' }} mr-2"
                                                                title="{{ $section->hasDependencies() ? 'Eliminación no permitida: esta sección tiene tomas asociadas.' : 'Eliminar Sección' }}"
                                                                {{ $section->hasDependencies() ? 'disabled' : 'data-toggle=modal data-target=#delete' . $section->id }}
                                                            >
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                    @include('sections.create')
                                                    @include('sections.show')
                                                    @include('sections.edit')
                                                    @include('sections.delete')
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No hay resultados</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
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
    $('#sections').DataTable({
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
        Swal.fire({ icon: 'success', title: 'Éxito', text: successMessage, confirmButtonText: 'Aceptar' });
    }

    if (errorMessage) {
        Swal.fire({ icon: 'error', title: 'Error', text: errorMessage, confirmButtonText: 'Aceptar' });
    }

    $('#createSection').on('shown.bs.modal', function() {
        $('.select2').select2({ dropdownParent: $('#createSection') });
    });
});
</script>
@endsection
