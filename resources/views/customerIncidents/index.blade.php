@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Mis Incidencias')

@section('content')
    <section class="content">
        <div class="right_col" role="main">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Mis Incidencias</h2>
                        <div class="row mb-2">
                            <div class="col-lg-12">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                                    <form method="GET" action="{{ route('customerIncidents.index') }}" class="flex-grow-1 mt-2" style="min-width: 330px; max-width: 30%;">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Buscar por ID o Título" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary" title="Buscar Incidencia">
                                                    <i class="fas fa-search"></i>
                                                    <span class="d-none d-md-inline">Buscar</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <button class="btn btn-success mt-2" data-toggle='modal'
                                        data-target="#createIncidence" title="Registrar Incidencia">
                                        <i class="fa fa-plus"></i>
                                        <span class="d-none d-md-inline">Registrar Incidencia</span>
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
                                    <table id="incident" class="table table-striped display responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>TÍTULO</th>
                                                <th>ESTADO</th>
                                                <th>FECHA DE LA INCIDENCIA</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($incidents) <= 0)
                                                <tr>
                                                    <td colspan="5">No hay incidencias registradas.</td>
                                                </tr>
                                            @else
                                                @foreach ($incidents as $incident)
                                                    <tr>
                                                        <td>{{ $incident->id }}</td>
                                                        <td>{{ $incident->name }}</td>
                                                        <td>
                                                            @php
                                                                if ($incident->status_id) {
                                                                    $status = \App\Models\IncidentStatus::find($incident->status_id);
                                                                    if ($status) {
                                                                        echo '<span class="badge ' . $status->color . ' text-white" style="color: #fff !important;">' . $status->status . '</span>';
                                                                    } else {
                                                                        echo '<span class="badge badge-secondary">Estatus no encontrado</span>';
                                                                    }
                                                                } else {
                                                                    echo '<span class="badge badge-secondary">Pendiente</span>';
                                                                }
                                                            @endphp
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($incident->start_date)->format('d/m/Y') }}</td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $incident->id }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>

                                                                <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{ $incident->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>

                                                                <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Incidencia" data-target="#delete{{ $incident->id }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                                
                                                                <button type="button" class="btn bg-maroon mr-2" data-toggle="modal" title="Ver Historial" data-target="#historyModal{{ $incident->id }}">
                                                                    <i class="fas fa-history"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @include('customerIncidents.edit')
                                                    @include('customerIncidents.delete')
                                                    @include('customerIncidents.show')
                                                    @include('customerIncidents.incidentHistoryModal')
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    @include('customerIncidents.create')
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
@endsection
