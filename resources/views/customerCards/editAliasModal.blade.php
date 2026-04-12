<div class="modal fade" id="editAliasModal" tabindex="-1" role="dialog" aria-labelledby="editAliasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title" id="editAliasModalLabel">
                    <i class="fas fa-edit"></i> Editar Alias de Tarjeta
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-alias-card-id">
                <div class="form-group">
                    <label for="edit-alias-input">Alias de la tarjeta</label>
                    <input type="text" class="form-control" id="edit-alias-input" maxlength="50"
                        placeholder="Ej: Mi tarjeta personal">
                    <small class="text-muted">Deja vacío para mostrar solo la marca de la tarjeta</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" id="btn-save-alias" class="btn btn-info">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
