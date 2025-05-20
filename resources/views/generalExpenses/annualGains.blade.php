<div class="modal fade" id="annualGains" tabindex="-1" role="dialog" aria-labelledby="annualGainsLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom bg-primary">
                <h5 class="modal-title" id="annualGainsLabel">Selecciona un año</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="yearGainsForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="yearGains" class="form-label">Año(*)</label>
                        <input type="number" id="yearGains" name="yearGains" class="form-control"  min="1900" max="{{ date('Y') }}" 
                               required placeholder="Ingrese el año ejemplo 2024" value="{{ old('yearGains') }}" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Generar Reporte</button>
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

<script>
    document.getElementById('yearGainsForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const yearGains = document.getElementById('yearGains').value;
        window.open(`/annual-gains-report/${yearGains}`, '_blank');
        $('#annualGains').modal('hide');
    });
</script>
