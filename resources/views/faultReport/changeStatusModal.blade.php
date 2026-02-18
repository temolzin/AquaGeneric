<div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog" aria-labelledby="changeStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header bg-purple">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Cambiar Estatus <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="changeStatusForm" action="{{ route('faultReport.updateStatus') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Datos del Cambio de Estatus</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="faultReportTitle" class="form-label">Reporte</label>
                                            <p class="form-control-plaintext mb-0" id="faultReportTitleDisplay"></p>
                                            <input type="hidden" name="fault_report_id" id="faultReportId">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="statusSelect" class="form-label">Estatus(*)</label>
                                            <select class="form-control select2" name="status" id="statusSelect" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="Pendiente">Pendiente</option>
                                                <option value="En revisión">En revisión</option>
                                                <option value="Completado">Completado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="comentarioText" class="form-label">Comentario</label>
                                            <textarea class="form-control" name="comentario" id="comentarioText" placeholder="Agrega un comentario" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
