<!-- Modal para Reporte de Empleados por Rol -->
<div class="modal fade" id="reportByRoleModal" tabindex="-1" role="dialog" aria-labelledby="reportByRoleLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Reporte de Empleados por Rol</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('report.showEmployeeReportByRole') }}" method="POST" target="_blank">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="roleSelect" class="form-label">Selecciona el Rol(*)</label>
                            <select class="form-control select2" name="role" id="roleSelect" required>
                                <option value="">Selecciona un rol</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Recepcionista">Recepcionista</option>
                                <option value="Encargado">Encargado</option>
                                <option value="Seguridad">Seguridad</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
