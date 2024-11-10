<div class="modal fade" id="weeklyEarnings" tabindex="-1" role="dialog" aria-labelledby="weeklyEarningsLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom bg-olive">
                <h5 class="modal-title" id="weeklyEarningsLabel">Selecciona un Periodo de Fechas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="weeklyEarnigsForm" method="GET" action="{{ route('report.weeklyEarningsReport') }}" target="_blank">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="weekStartDate" class="form-label">Fecha inicio(*)</label>
                        <input type="date" id="weekStartDate" name="weekStartDate" class="form-control" required placeholder="Ingrese fecha inicio"/>
                        <label for="weekEndDate" class="form-label">Fecha fin(*)</label>
                        <input type="date" id="weekEndDate" name="weekEndDate" class="form-control" required placeholder="Ingrese fecha fin"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn bg-olive">Generar Reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal-header-custom {
        background-color: #667f9baa;
        color: white;
    }
</style>
