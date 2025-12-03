<div class="modal fade" id="edit{{ $report->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Reporte de Falla <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('customerFaultReports.update', $report->id) }}" method="POST" id="edit-customerFaultReports-form-{{ $report->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="titleUpdate" class="form-label">Título(*)</label>
                                            <input type="text" class="form-control" name="titleUpdate" placeholder="Título del reporte" value="{{ $report->title }}" required/>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="descriptionUpdate" class="form-label">Descripción(*)</label>
                                            <textarea class="form-control" name="descriptionUpdate" placeholder="Descripción del reporte" required>{{ $report->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Estado</label>
                                            <input type="text" disabled class="form-control"
                                                value="@switch($report->status)
                                                            @case('Earring') Pendiente @break
                                                            @case('In process') En proceso @break
                                                            @case('Resolved') Resuelto @break
                                                            @case('Closed') Cerrado @break
                                                            @default {{ $report->status }} @break
                                                        @endswitch" />
                                    </div>
                                </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="dateReportUpdate" class="form-label">Fecha del reporte(*)</label>
                                            <input type="date" class="form-control" name="dateReportUpdate" 
                                                   value="{{ old('dateReportUpdate', \Carbon\Carbon::parse($report->date_report)->format('Y-m-d')) }}" required readonly/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
