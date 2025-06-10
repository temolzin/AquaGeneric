<div class="modal fade" id="view{{ $incident->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $incident->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información del Incidencia</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
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
                                        <label>ID Incidencia</label>
                                        <input type="text" disabled class="form-control" value="{{ $incident->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" disabled class="form-control" value="{{ $incident->name }} " />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fecha de Inicio</label>
                                        <input type="text" disabled class="form-control" value="{{ \Carbon\Carbon::parse($incident->start_date)->translatedFormat('d/F/Y') }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <input type="text" disabled class="form-control" value="{{ $incident->description }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="CategoryUpdate" class="form-label">Categoria(*)</label>
                                        <input type="text" disabled class="form-control" value="{{ $incident->incidentCategory->name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label">Estado(*)</label>
                                        <select class="form-control" disabled onchange="toggleResponsibleFieldView({{ $incident->id }})">
                                            <option value="">Selecciona una opción</option>
                                            <option value="2" {{ $incident->status == 2 ? 'selected' : '' }}>Pendiente</option>
                                            <option value="1" {{ $incident->status == 1 ? 'selected' : '' }}>Proceso</option>
                                            <option value="0" {{ $incident->status == 0 ? 'selected' : '' }}>Terminada</option>
                                        </select>
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

<script>
    function toggleResponsibleFieldView(id) {
        const statusSelect = document.getElementById('statusUpdate-' + id);
        const responsibleField = document.getElementById('responsibleNameUpdate-' + id);

        responsibleField.style.display = statusSelect.value === '0' ? 'block' : 'none';
    }

    function initializeModal(id) {
        toggleResponsibleFieldView(id);
    }
</script>
