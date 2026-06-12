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
                            <div class="col-12 col-lg-8">
                                <form method="GET" action="{{ route('incidents.index') }}" class="m-0" id="incidents-filter-form">
                                    <div class="form-row align-items-center">
                                        <div class="col-8 col-md-5 mb-2 mb-md-0">
                                            <select name="category" class="form-control select2 w-100">
                                                <option value="">Filtrar por categoría</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4 col-md-auto mb-3 mb-md-0">
                                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                                <i class="fas fa-filter"></i> <span class="d-none d-sm-inline">Filtrar</span>
                                            </button>
                                        </div>
                                        @if($canToggleIncidentType)
                                            <div class="col-12 col-md-auto mb-2 mb-md-0">
                                                <input type="hidden" name="show_customer_incidents" id="show_customer_incidents_input" value="{{ $showCustomerIncidents ? '1' : '0' }}">
                                                <div class="border rounded p-1 px-2 bg-white text-center">
                                                    <div class="custom-control custom-switch d-inline-block">
                                                        <input type="checkbox" class="custom-control-input" id="show_customer_incidents" {{ $showCustomerIncidents ? 'checked' : '' }}>
                                                        <label class="custom-control-label font-weight-normal mb-0" for="show_customer_incidents">Incidencias Cliente</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if(request('category'))
                                            <div class="col-12 col-md-auto mb-2 mb-md-0">
                                                <a href="{{ route('incidents.index') }}" class="btn btn-secondary btn-sm btn-block px-3">
                                                    <i class="fas fa-times"></i> <span class="d-none d-sm-inline">Limpiar</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                            <div class="col-12 col-lg-4 mt-2 mt-lg-0">
                                <div class="form-row justify-content-lg-end">
                                    <div class="col-12 col-md-auto mb-2 mb-lg-0">
                                        <button class="btn btn-success btn-block px-3.5" data-toggle='modal' data-target="#createIncidence">
                                            <i class="fa fa-plus"></i> <span>Registrar Incidencia</span>
                                        </button>
                                    </div>
                                    <div class="col-12 col-md-auto">
                                        <a class="btn btn-secondary btn-block px-3.5" target="_blank" href="{{ route('report.generateIncidentListReport') }}">
                                            <i class="fas fa-file-pdf"></i> <span>Generar Lista</span>
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
                                <div class="card-box table-responsive" id="incidents-table-container">
                                    <table id="incident" class="table table-striped display responsive" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>INCIDENCIA</th>
                                                <th>EMPLEADO</th>
                                                <th>FECHA DE LA INCIDENCIA</th>
                                                <th>CATEGORIA</th>
                                                <th>ESTATUS</th>
                                                <th>CREADO POR</th>
                                                <th class="not-export">OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($incidents as $incident)
                                                <tr>
                                                    <td>{{ $incident->id }}</td>
                                                    <td>{{ $incident->name }}</td>
                                                    <td>
                                                        @if ($incident->responsible_employees->isEmpty())
                                                            <span class="text-muted">Sin asignar</span>
                                                        @else
                                                            <div class="d-flex align-items-center">
                                                                @foreach ($incident->responsible_employees->take(5) as $employee)
                                                                    @php
                                                                        $employeePhoto = $employee->getFirstMedia('employeeGallery');

                                                                        $employeePhotoUrl = $employeePhoto
                                                                            ? asset('storage/' . $employeePhoto->id . '/' . $employeePhoto->file_name)
                                                                                . '?t=' . (optional($employee->updated_at)->timestamp ?? now()->timestamp)
                                                                            : asset('img/userDefault.png');
                                                                    @endphp

                                                                    <img
                                                                        src="{{ $employeePhotoUrl }}" alt="Empleado" title="{{ $employee->name }} {{ $employee->last_name }}" class="img-thumbnail mr-1" style="width:32px; height:32px; object-fit:cover; border-radius:50%;">
                                                                @endforeach
                                                                @if($incident->responsible_employees->count() > 5)
                                                                    <span class="badge badge-secondary ml-1">
                                                                        +{{ $incident->responsible_employees->count() - 5 }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($incident->start_date)->translatedFormat('d/F/Y') }}</td>
                                                    <td>
                                                         <span class="badge {{ $incident->incidentCategory->color ?? 'bg-secondary' }} text-white" style="color: #fff !important;">
                                                            {{ $incident->incidentCategory->name }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            if (!$incident->status) {
                                                                echo '<span class="badge badge-secondary">Pendiente</span>';
                                                            }
                                                            if ($incident->status) {
                                                                $statusName = $incident->status->status;
                                                                $statusColor = (preg_match('/^#[a-f0-9]{6}$/i', $incident->status->color)) ? $incident->status->color : '#6c757d';
                                                                echo '<span class="badge ' . $incident->status->color . ' text-white" style="color: #fff !important;">' . $statusName . '</span>';
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @if (!$incident->creator)
                                                            <span class="text-muted">Sin especificar</span>
                                                        @endif
                                                        @if ($incident->creator)
                                                            @php
                                                                $creatorPhoto = $incident->creator->getFirstMedia('userGallery');
                                                                $creatorPhotoUrl = $creatorPhoto ? asset('storage/' . $creatorPhoto->id . '/' . $creatorPhoto->file_name) . '?t=' . (optional($incident->creator->updated_at)->timestamp ?? now()->timestamp) : asset('img/userDefault.png');
                                                            @endphp
                                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                                <img src="{{ $creatorPhotoUrl }}" alt="Creador" title="{{ $incident->creator->name }} {{ $incident->creator->last_name }}"
                                                                    class="img-thumbnail" style="width: 32px; height: 32px; object-fit: cover; border-radius: 50%;">
                                                                <span>{{ $incident->creator->name }} {{ $incident->creator->last_name }}</span>
                                                            </div>
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
                                                        @include('incidents.edit')
                                                        @include('incidents.delete')
                                                        @include('incidents.show')
                                                        @include('incidents.incidentHistoryModal')
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
    @include('incidents.changeStatusModal')

@endsection

@section('js')
<script>
    function initIncidentDataTable() {
        if ($.fn.DataTable.isDataTable('#incident')) {
            $('#incident').DataTable().destroy();
        }

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
    }

    $(document).ready(function() {
        initIncidentDataTable();
        $(document).on('click', '#incident .btn', function(e) {
            e.stopPropagation();
            
            var target = $(this).data('target');
            if (target && target.startsWith('#')) {
                var $modal = $(target);
                if ($modal.length && !$modal.parent().is('body')) {
                    $modal.appendTo('body');
                    $modal.modal('show');
                }
            }
        });

        function updateIncidentsTable(url, data) {
            $('#incidents-table-container').css('opacity', '0.5');

            $.ajax({
                url: url,
                type: 'GET',
                data: data,
                success: function(response) {
                    const newHtml = $(response).find('#incidents-table-container').html();
                    $('#incidents-table-container').html(newHtml);
                    initIncidentDataTable();
                    $('#incidents-table-container').css('opacity', '1');
                },
                error: function() {
                    $('#incidents-table-container').css('opacity', '1');
                    Swal.fire('Error', 'No se pudieron actualizar las incidencias', 'error');
                }
            });
        }

        $(document).on('submit', '#incidents-filter-form', function(e) {
            e.preventDefault();
            updateIncidentsTable($(this).attr('action'), $(this).serialize());
        });

        $(document).on('change', '#show_customer_incidents', function() {
            var isChecked = this.checked ? '1' : '0';
            $('#show_customer_incidents_input').val(isChecked);
            
            const form = $('#incidents-filter-form');
            updateIncidentsTable(form.attr('action'), form.serialize());
        });

        $(document).on('click', '#incidents-table-container .pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            if (url) updateIncidentsTable(url, {});
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

        @if(session('success'))
            (function() {
                let hasReloaded = false;
                window.addEventListener('load', function () {
                    if (!hasReloaded) {
                        hasReloaded = true;
                        location.reload();
                    }
                });
            })();
        @endif

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
</script>
@endsection
