@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Secciones')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title mb-3">
                    <h2>Secciones</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-lg-flex justify-content-between align-items-center flex-wrap">
                                <form method="GET" action="{{ route('sections.index') }}" class="mb-3 mb-lg-0" style="min-width: 300px;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Buscar por ID o Nombre" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Sección">Buscar</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="btn-group d-none d-md-flex" role="group" aria-label="Acciones de Sección">
                                    <button class="btn btn-success mr-2"
                                            data-toggle='modal' data-target="#createSection"
                                            title="Registrar Sección">
                                        <i class="fa fa-plus"></i> Registrar Sección
                                    </button>
                                    <button class="btn btn-secondary" data-toggle="modal" data-target="#pdfSectionModal">
                                        <i class="fas fa-file-pdf"></i> Generar Lista De Secciones
                                    </button>
                                </div>
                                <div class="d-md-none w-100">
                                    <div class="row g-2">
                                        <div class="col-6 pe-1">
                                            <button class="btn btn-success w-100 py-2"
                                                    data-toggle='modal'
                                                    data-target="#createSection"
                                                    title="Registrar Sección">
                                                <i class="fa fa-plus"></i> Registrar
                                            </button>
                                        </div>
                                        <div class="col-6 ps-1">
                                            <button class="btn btn-secondary w-100 py-2"
                                                    data-toggle="modal" data-target="#pdfSectionModal"
                                                    title="Generar Lista">
                                                <i class="fas fa-file-pdf"></i> Lista
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
<a type="button" class="btn btn-secondary me-2" title="Generar Reporte por Sección" data-toggle="modal" data-target="#pdfSectionModal">
    <i class="fas fa-file-pdf"></i> Generar Reporte
</a>
<div class="modal fade" id="pdfSectionModal" tabindex="-1" role="dialog" aria-labelledby="pdfSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="GET" action="{{ route('reports.pdfSections', ['search' => request('search')]) }}" target="_blank">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfSectionModalLabel">Seleccione la Sección</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select name="section_id" class="form-control" required>
                        <option value="" disabled selected>-- Seleccione una sección --</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> Generar PDF
                    </button>
                </div>
            </div>
        </form>
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
