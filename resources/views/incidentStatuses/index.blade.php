@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Estatus de Incidencia')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Estatus de Incidencia</h2>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createIncidentStatusModal">
                                <i class="fa fa-plus"></i> Registrar Estatus
                            </button>
                            <a type="button" class="btn btn-secondary" target="_blank" title="IncidentStatus"
                                href="{{ route('report.generateIncidentStatusListReport') }}">
                                <i class="fas fa-list"></i> Generar Lista
                            </a>
                        </div>
                    </div>
                    <div class="clearfix mb-4"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="incidentStatuses" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>ESTATUS</th>
                                            <th>DESCRIPCIÓN</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($statuses as $status)
                                            <tr>
                                                <td>{{ $status->id }}</td>
                                                <td>{{ $status->status }}</td>
                                                <td>{{ $status->description }}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Opciones">
                                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#viewIncidentStatus{{ $status->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @can('editIncidentStatuses')
                                                        <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Registro" data-target="#editIncidentStatus{{ $status->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @endcan
                                                        @can('deleteIncidentStatuses')
                                                        <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#deleteIncidentStatus{{ $status->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>

                                            @include('incidentStatuses.show')
                                            @include('incidentStatuses.edit')
                                            @include('incidentStatuses.delete')
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay estatus registrados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @include('incidentStatuses.create')
                                <div class="d-flex justify-content-center">
                                    {!! $statuses->links('pagination::bootstrap-4') !!}
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
        $('#incidentStatuses').DataTable({
            responsive: true,
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

    });
</script>
@endsection
