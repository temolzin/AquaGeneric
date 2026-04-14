@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Incidencias')

@section('content')
    <section class="content">
        <div class="right_col" incident="main">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Incidencias</h2>
                        <div class="row mb-2">
                            <div class="col-lg-12">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                                    <form method="GET" action="{{ route('incidents.index') }}" class="flex-grow-1 col-md-8 px-0" style="min-width: 200px;">
                                        <div class="d-flex">
                                            <select name="category" class="form-control select2 rounded-start border-end-0 w-100" style="min-width: 200px;">
                                                <option value="">Filtrar por categoría</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-primary border-start-0" title="Filtrar por categoría">
                                                <i class="fas fa-filter d-md-none"></i>
                                                <span class="d-none d-md-inline">Filtrar</span>
                                            </button>
                                             @if(request('category'))
                                                <a href="{{ route('incidents.index') }}" class="btn btn-secondary ml-2" title="Quitar filtro">
                                                    <i class="fas fa-times d-md-none"></i>
                                                    <span class="d-none d-md-inline">Limpiar</span>
                                                </a>
                                            @endif
                                        </div>
                                    </form>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end w-100 w-lg-auto">
                                        <button class="btn btn-success flex-grow-1 flex-lg-grow-0 mt-2 mr-1" data-toggle='modal'
                                                data-target="#createIncidence" title="Registrar Incidencia">
                                            <i class="fa fa-plus"></i>
                                            <span class="d-none d-md-inline">Registrar Incidencia</span>
                                            <span class="d-inline d-md-none">Registrar Incidencia</span>
                                        </button>
                                        <a type="button" class="btn btn-secondary flex-grow-1 flex-lg-grow-0 mt-2 ml-1" target="_blank"
                                        title="Generar Lista" href="{{ route('report.generateIncidentListReport') }}">
                                            <i class="fas fa-file-pdf"></i>
                                            <span class="d-none d-md-inline">Generar Lista</span>
                                            <span class="d-inline d-md-none">Generar Lista</span>
                                        </a>
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
                                    <table id="incident" class="table table-striped display responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>INCIDENCIA</th>
                                                <th>EMPLEADO</th>
                                                <th>FECHA DE LA INCIDENCIA</th>
                                                <th>CATEGORIA</th>
                                                <th>ESTATUS</th>
                                                <th>CREADO POR</th>
                                                <th>OPCIONES</th>
                                                <th class="not-export">OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($incidents) <= 0)
                                                <tr>
                                                    <td colspan="8">No hay incidentes registrados.</td>
                                                </tr>
                                            @else
                                                @foreach ($incidents as $incident)
                                                    <tr>
                                                        <td>{{ $incident->id }}</td>
                                                        <td>{{ $incident->name }}</td>
                                                        <td>
                                                            @forelse ($incident->responsible_employees as $employee)
                                                                <img src="{{ $employee->getFirstMediaUrl('employeeGallery') ?: asset('img/userDefault.png') }}" alt="Empleado" title="{{ $employee->name }} {{ $employee->last_name }}"
                                                                    class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 3px;">
                                                            @empty
                                                                <span class="text-muted">Sin asignar</span>
                                                            @endforelse
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($incident->start_date)->translatedFormat('d/F/Y') }}</td>
                                                        <td>
                                                             <span class="badge {{ $incident->incidentCategory->color ?? 'bg-secondary' }} text-white" style="color: #fff !important;">
                                                                {{ $incident->incidentCategory->name }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @php
                                                                if ($incident->status) {
                                                                    $statusName = $incident->status->status;
                                                                    $statusColor = (preg_match('/^#[a-f0-9]{6}$/i', $incident->status->color)) ? $incident->status->color : '#6c757d';
                                                                    echo '<span class="badge ' . $incident->status->color . ' text-white" style="color: #fff !important;">' . $statusName . '</span>';
                                                                } else {
                                                                    echo '<span class="badge badge-secondary">Pendiente</span>';
                                                                }
                                                            @endphp
                                                        </td>
                                                        <td>
                                                            @if ($incident->creator)
                                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                                    <img src="{{ $incident->creator->getFirstMediaUrl('userImage') ?: asset('img/userDefault.png') }}" alt="Creador" title="{{ $incident->creator->name }} {{ $incident->creator->last_name }}"
                                                                        class="img-thumbnail" style="width: 32px; height: 32px; object-fit: cover; border-radius: 50%;">
                                                                    <span>{{ $incident->creator->name }} {{ $incident->creator->last_name }}</span>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">Sin especificar</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $incident->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>

                                                            @can('editIncidents')
                                                                <button type="button" class="btn btn-warning btn-sm mr-1" data-toggle="modal" title="Editar Datos" data-target="#edit{{ $incident->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @endcan

                                                            @can('deleteIncidents')
                                                                <button type="button" class="btn bg-red btn-sm mr-1" data-toggle="modal" title="Eliminar Incidencia" data-target="#delete{{ $incident->id }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endcan

                                                            <button type="button" class="btn bg-purple btn-sm mr-1" data-toggle="modal" title="Cambiar Estatus de Incidencia" data-target="#createResponsible" data-incident-id="{{ $incident->id }}" data-incident-name="{{ $incident->name }}">
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </button>

                                                            <button type="button" class="btn bg-maroon btn-sm" data-toggle="modal" title="Historial de Incidencia" data-target="#historyModal{{ $incident->id }}">
                                                                <i class="fas fa-history"></i>
                                                            </button>
                                                        </td>
                                                        @include('incidents.edit')
                                                        @include('incidents.delete')
                                                        @include('incidents.show')
                                                        @include('incidents.incidentHistoryModal')
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    @include('incidents.changeStatusModal')
                                    <div class="d-flex justify-content-center">
                                        {!! $incidents->links('pagination::bootstrap-4') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('incidents.create')

@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#incident').DataTable({
            responsive: true,
            buttons:[
                {
                    extend: 'csv',
                    charset: 'utf-8',
                    bom: true,
                    exportOptions: {
                        columns: ':not(.not-export)'
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(.not-export)'
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':not(.not-export)'
                    }
                }
            ],
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
    });

    $(document).on('shown.bs.modal', '[id^="edit"]', function() {
        $(this).find('.select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            $(this).select2({
                allowClear: false,
                placeholder: 'Selecciona una opción',
                width: '100%',
                dropdownParent: $(this).closest('.modal')
            });
        });
    });

    $('#createResponsible').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var incidentId = button.data('incident-id');
        var incidentName = button.data('incident-name');

        var modal = $(this);
        modal.find('#incidentId').val(incidentId);
        modal.find('#incidentNameDisplay').text(incidentName);
        modal.find('#status_id').val('').trigger('change');
    });

    $(document).on('shown.bs.modal', '[id^="edit"]', function() {
        $(this).find('.select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            $(this).select2({
                allowClear: false,
                placeholder: 'Selecciona una opción',
                width: '100%',
                dropdownParent: $(this).closest('.modal')
            });
        });
    });

    $('#changeStatusForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var formData = form.serialize();

        $.ajax({
            url: "{{ route('incidents.updateStatus') }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al actualizar el estatus',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });
</script>
@endsection
