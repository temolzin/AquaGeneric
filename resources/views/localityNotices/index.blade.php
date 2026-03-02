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
<<<<<<< fix/customer-notice-status-and-report-list-view
                                <div class="card-box table-responsive">
                                    <table id="notices" class="table table-striped display responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>TÍTULO</th>
                                                <th>FECHA Y HORA DE INICIO</th>
                                                <th>FECHA Y HORA DE FIN</th>
                                                <th>ESTATUS</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($localityNotices) <= 0)
=======
                                <div class="card-box">

                                        <table id="notices" class="table table-striped display responsive nowrap" style="width:100%">
                                            <thead>
>>>>>>> develop
                                                <tr>
                                                    <th></th>
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
<<<<<<< fix/customer-notice-status-and-report-list-view
                                                        <td>{{ $notice->id }}</td>
                                                        <td>
                                                            {{ Str::limit($notice->title, 40) }}                                                       
                                                        </td>
                                                        <td>{{ $notice->start_date->format('d/m/Y H:i') }}</td>
                                                        <td>{{ $notice->end_date->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            @php
                                                                $status = $notice->status;
                                                                if ($status === 'active') {
                                                                    $badgeClass = 'badge-success';
                                                                    $text = 'Activo';
                                                                } elseif ($status === 'scheduled') {
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
                                                            <div class="d-flex flex-wrap gap-0">
                                                                @can('viewNotice')
                                                                <button type="button" class="btn btn-info btn-sm mx-1 mb-1" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $notice->id }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @endcan
                                                                @can('editNotice')
                                                                <button type="button" class="btn btn-warning btn-sm mx-1 mb-1" data-toggle="modal" title="Editar Aviso" data-target="#edit{{ $notice->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                @endcan
                                                                @can('deleteNotice')
                                                                <button type="button" class="btn btn-danger btn-sm mx-1 mb-1" data-toggle="modal" title="Eliminar Aviso" data-target="#delete{{ $notice->id }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                        @include('localityNotices.delete', ['notice' => $notice])
                                                        @include('localityNotices.edit', ['notice' => $notice])
                                                        @include('localityNotices.show', ['notice' => $notice])
=======
                                                        <td colspan="7" class="text-center">No hay avisos registrados</td>
>>>>>>> develop
                                                    </tr>
                                                @else
                                                    @foreach($localityNotices as $notice)
                                                        <tr>
                                                            <td></td>
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
                                                                <div class="d-flex flex-wrap gap-0">
                                                                    @can('viewNotice')
                                                                    <button type="button" class="btn btn-info btn-sm mx-1 mb-1" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $notice->id }}">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                    @endcan
                                                                    @can('editNotice')
                                                                    <button type="button" class="btn btn-warning btn-sm mx-1 mb-1" data-toggle="modal" title="Editar Aviso" data-target="#edit{{ $notice->id }}">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    @endcan
                                                                    @can('deleteNotice')
                                                                    <button type="button" class="btn btn-danger btn-sm mx-1 mb-1" data-toggle="modal" title="Eliminar Aviso" data-target="#delete{{ $notice->id }}">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                    @endcan
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>

                                        @if (count($localityNotices) > 0)
                                            @foreach($localityNotices as $notice)
                                                @include('localityNotices.delete', ['notice' => $notice])
                                                @include('localityNotices.edit', ['notice' => $notice])
                                                @include('localityNotices.show', ['notice' => $notice])
                                            @endforeach
                                        @endif

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

@section('css')
<style>
@media (max-width: 767.98px) {

  table.dataTable td.dtr-control:before {
    content: '+';
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 25px;
    height: 25px;

    border-radius: 50%;
    border: 2px solid #ffffff;
    background: #31b131;
    color: #ffffff;

    font-weight: 800;
    font-size: 15px;
    line-height: 1;
  }

}
@media (min-width: 768px) {

    table.dataTable td.dtr-control:before {

        content: '+';
        display: inline-flex;
        align-items: center;
        justify-content: center;

        width: 30px;
        height: 30px;

        border-radius: 50%;
        border: 3px solid #ffffff;
        background: #31b131;
        color: #ffffff;

        font-weight: 800;
        font-size: 18px;
        line-height: 0;
    }

}
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {

    const table = $('#notices').DataTable({
    responsive: {
        details: {
            type: 'column',
            target: 0
        }
    },
    autoWidth: false,
    paging: false,
    info: false,
    searching: false,
    order: [[1, 'desc']],

    columnDefs: [

        {
            targets: 0,
            className: 'dtr-control',
            orderable: false,
            searchable: false,
            responsivePriority: 1
        },

        {
            targets: 2,
            className: 'all',
            responsivePriority: 2
        },

        {
            targets: [1,3,4,5,6],
            className: 'min-tablet',
            responsivePriority: 100
        },

        {
            targets: 7,
            className: 'none',
            responsivePriority: 3
        }
    ],
});

$(window).on('resize', function () {
    table.columns.adjust();
    table.responsive.recalc();
});

setTimeout(function () {
    table.columns.adjust();
    table.responsive.recalc();
}, 0);

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
