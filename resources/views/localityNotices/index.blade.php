@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Avisos de Localidades')

@section('content')
    <section class="content">
        <div class="right_col" role="main">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Avisos de Localidades</h2>
                        <div class="row mb-2">
                            @include('localityNotices.create')
                            <div class="col-lg-12">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                                    <form id="formSearch" method="GET" action="{{ route('localityNotices.index') }}" class="flex-grow-1 mt-2" style="min-width: 300px; max-width: 40%;">
                                        <div class="input-group">
                                            <input type="text" name="search" id="searchName" class="form-control" placeholder="Buscar por título o descripción" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary" title="Buscar Aviso">
                                                    <i class="fas fa-search d-lg-none"></i>
                                                    <span class="d-none d-lg-inline">Buscar</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <button class="btn btn-success mt-2 mr-1" data-toggle='modal' data-target="#createNotice" title="Registrar Aviso">
                                        <i class="fa fa-plus"></i>
                                        <span class="d-none d-md-inline">Registrar Aviso</span>
                                        <span class="d-inline d-md-none">Registrar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="notices" class="table table-striped display responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>TÍTULO</th>
                                                <th>LOCALIDAD</th>
                                                <th>FECHA Y HORA DE INICIO</th>
                                                <th>FECHA Y HORA DE FIN</th>
                                                <th>ESTATUS</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($localityNotices) <= 0)
                                                <tr>
                                                    <td colspan="6" class="text-center">No hay avisos registrados</td>
                                                </tr>
                                            @else
                                                @foreach($localityNotices as $notice)
                                                    <tr>
                                                        <td>{{ $notice->id }}</td>
                                                        <td>
                                                            {{ Str::limit($notice->title, 40) }}                                                       
                                                        </td>
                                                        <td>{{ $notice->locality->name }}</td>
                                                        <td>{{ $notice->start_date->format('d/m/Y H:i') }}</td>
                                                        <td>{{ $notice->end_date->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            @php
                                                                $now = now();
                                                                $startDate = \Carbon\Carbon::parse($notice->start_date);
                                                                $endDate = \Carbon\Carbon::parse($notice->end_date);
                                                                
                                                                if ($notice->is_active && $startDate <= $now && $endDate >= $now) {
                                                                    $badgeClass = 'badge-success';
                                                                    $text = 'Activo';
                                                                } elseif ($startDate > $now) {
                                                                    $badgeClass = 'badge-primary';
                                                                    $text = 'Programado';
                                                                } else {
                                                                    $badgeClass = 'badge-secondary';
                                                                    $text = 'Expirado';
                                                                }
                                                            @endphp
                                                            
                                                            <span class="badge {{ $badgeClass }} px-1 py-1">
                                                                {{ $text }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group" aria-label="Opciones">
                                                                @can('viewNotice')
                                                                <button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $notice->id }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @endcan
                                                                @can('editNotice')
                                                                <button type="button" class="btn btn-warning btn-sm mr-1" data-toggle="modal" title="Editar Aviso" data-target="#edit{{ $notice->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                @endcan
                                                                @can('deleteNotice')
                                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" title="Eliminar Aviso" data-target="#delete{{ $notice->id }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                        @include('localityNotices.delete', ['notice' => $notice])
                                                        @include('localityNotices.edit', ['notice' => $notice])
                                                        @include('localityNotices.show', ['notice' => $notice])
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                       {!! $localityNotices->appends(request()->query())->links('pagination::bootstrap-4') !!}
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
    $('#notices').DataTable({
        responsive: true,
        paging: false,
        info: false,
        searching: false,
        order: [[0, 'desc']]
    });

    var successMessage = "{{ session('success') }}";
    var errorMessage = "{{ session('error') }}";

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: successMessage,
            confirmButtonText: 'Aceptar',
            timer: 3000
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

    $('#createNoticeForm').on('submit', function(e) {
        var startDate = new Date($('#start_date').val());
        var endDate = new Date($('#end_date').val());
        
        if (endDate <= startDate) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error en fechas',
                text: 'La fecha de fin debe ser posterior a la fecha de inicio.'
            });
        }
    });
});
</script>
@endsection
