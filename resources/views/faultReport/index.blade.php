@extends('layouts.adminlte')

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
                                        @forelse($reports as $report)
                                                <tr>
                                                    <td>{{ $report->id }}</td>
                                                    <td>{{ $report->title }}</td>
                                                    <td>
                                                        @switch($report->status)
                                                            @case('pending')
                                                                Pendiente
                                                                @break
                                                            @case('in_review')
                                                                En revisión
                                                                @break
                                                            @case('completed')
                                                                Completado
                                                                @break
                                                            @default
                                                                {{ $report->status }}
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($report->date_report)->format('d/m/Y') }}</td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-0">
                                                            @can('viewFaultReport')
                                                            <button type="button" class="btn btn-info btn-sm mx-1 mb-1" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $report->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @endcan

                                                            @can('editFaultReport')
                                                            @if(auth()->user()->hasRole('customer'))
                                                            <button type="button" class="btn btn-warning btn-sm mx-1 mb-1" data-toggle="modal" title="Editar Registro" data-target="#edit{{ $report->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @else
                                                            <button type="button" class="btn btn-secondary btn-sm mx-1 mb-1" title="No editable" disabled>
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @endif
                                                            @endcan

                                                            @can('deleteFaultReport')
                                                            @if($report->status === 'completed')
                                                            <button type="button" class="btn btn-danger btn-sm mx-1 mb-1" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $report->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            @else
                                                            <button type="button" class="btn btn-secondary btn-sm mx-1 mb-1" title="Solo se pueden eliminar reportes completados" disabled>
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            @endif
                                                            @endcan

                                                            <button type="button" class="btn bg-purple btn-sm mx-1 mb-1" data-toggle="modal" title="Cambiar Estatus" data-target="#changeStatusModal" data-fault-report-id="{{ $report->id }}" data-fault-report-title="{{ $report->title }}">
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </button>

                                                            <button type="button" class="btn bg-maroon btn-sm mx-1 mb-1" data-toggle="modal" title="Historial de Reporte" data-target="#historyModal{{ $report->id }}">
                                                                <i class="fas fa-history"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('faultReport.show')
                                                @include('faultReport.edit')
                                                @include('faultReport.delete')
                                                @include('faultReport.historyModal')
                                        @empty
                                            <tr>
                                                <td colspan="6">No hay resultados</td>
                                            </tr>
                                        @endforelse
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
    @include('faultReport.changeStatusModal')
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

        $('#changeStatusModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var faultReportId = button.data('fault-report-id');
            var faultReportTitle = button.data('fault-report-title');

            var modal = $(this);
            modal.find('#faultReportId').val(faultReportId);
            modal.find('#faultReportTitleDisplay').text(faultReportTitle);
            modal.find('#statusSelect').val('');
            modal.find('#descriptionText').val('');
        });

        $('#changeStatusForm').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: "{{ route('faultReport.updateStatus') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.success ? 'Éxito' : 'Error',
                        text: response.message,
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        if (response.success) location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al actualizar el estatus',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        });
    });
</script>
@endsection
