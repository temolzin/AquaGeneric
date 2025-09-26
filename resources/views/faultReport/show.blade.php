<div class="modal fade" id="view{{ $report->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $report->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información del Reporte de Falla</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>ID</label>
                                        <input type="text" disabled class="form-control" value="{{ $report->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <label>Título</label>
                                        <input type="text" disabled class="form-control" value="{{ $report->title }}" />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Descripción de la Falla</label>
                                        <textarea disabled class="form-control">{{ $report->description }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fecha de Reporte</label>
                                        <input type="text" disabled class="form-control" value="{{ $report->date_report }}" />
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
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Reportado por</label>
                                        <input type="text" disabled class="form-control" value="{{ $report->creator->name ?? 'Desconocido' }} {{ $faultReport->creator->last_name ?? '' }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

