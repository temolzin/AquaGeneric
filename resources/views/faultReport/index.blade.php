@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Reporte')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Reporte de Fallas</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                                <form method="GET" action="{{ route('faultReport.index') }}" class="flex-grow-1 mt-2" style="min-width: 330px; max-width: 30%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por ID o Título" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Falla">
                                                <i class="fas fa-search"></i>
                                                <span class="d-none d-md-inline">Buscar</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="faultReports" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>TÍTULO</th>
                                            <th>ESTADO</th>
                                            <th>FECHA DEL REPORTE</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($reports) <= 0)
                                            <tr>
                                                <td colspan="6">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach($reports as $report)
                                                <tr>
                                                    <td>{{ $report->id }}</td>
                                                    <td>{{ $report->title }}</td>
                                                    <td>
                                                        @switch($report->status)
                                                            @case('Earning') Abierto @break
                                                            @case('In process') En proceso @break
                                                            @case('Resolved') Resuelto @break
                                                            @case('Closed') Cerrado @break
                                                            @default {{ $report->status }} @break
                                                        @endswitch
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($report->date_report)->format('d/m/Y') }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            @can('viewFaultReport')
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $report->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @endcan

                                                            @can('editFaultReport')
                                                            <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{ $report->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @endcan

                                                            @can('deleteFaultReport')
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $report->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('faultReport.show')
                                                @include('faultReport.edit')
                                                @include('faultReport.delete')
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center">
                                    {!! $reports->links('pagination::bootstrap-4') !!}
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
    $(document).ready(function () {
        $('#faultReports').DataTable({
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
    });
</script>
@endsection

