<div class="modal fade" id="addMembership" tabindex="-1" role="dialog" aria-labelledby="addMembershipLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('memberships.store') }}" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addMembershipLabel">
                        Agregar Membresía
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="price">Precio</label>
                        <input type="number"
                               name="price"
                               class="form-control"
                               step="0.01"
                               min="0"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="term_months">Duración (Meses)</label>
                        <input type="number"
                               name="term_months"
                               class="form-control"
                               min="1"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="water_connections_number">Tomas de Agua</label>
                        <input type="number"
                               name="water_connections_number"
                               class="form-control"
                               min="0"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="users_number">Número de Usuarios</label>
                        <input type="number"
                               name="users_number"
                               class="form-control"
                               min="0"
                               required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>