<div class="modal fade" id="editIncidentStatus{{ $status->id }}" tabindex="-1" role="dialog" aria-labelledby="editIncidentStatusLabel{{ $status->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Actualizar Estatus <small>&nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal"
                                aria-label="Close" onclick="resetEditForm('editIncidentStatusForm{{ $status->id }}')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="editIncidentStatusForm{{ $status->id }}" action="{{ route('incidentStatuses.update', $status->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary text-white">
                                <h3 class="card-title mb-0">Ingrese Datos del Estatus</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Estatus (*)</label>
                                            <input type="text" class="form-control" name="status" id="status{{ $status->id }}" placeholder="Ingresa estatus" value="{{ old('status', $status->status) }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción (*)</label>
                                            <textarea class="form-control" name="description" id="description{{ $status->id }}" placeholder="Ingresa una descripción">{{ old('description', $status->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetEditForm('editIncidentStatusForm{{ $status->id }}')">Cerrar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function resetEditIncidentStatusForm(formId) {
    const form = document.getElementById(formId);
    if (form) form.reset();
}
</script>
