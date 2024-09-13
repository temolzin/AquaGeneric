<div class="modal fade" id="annualEarnings" tabindex="-1" role="dialog" aria-labelledby="annualEarningsLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title" id="annualEarningsLabel">Selecciona un año</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="yearForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="year" class="form-label">Año(*)</label>
                        <input type="number" id="year" name="year" class="form-control"  min="1900" max="{{ date('Y') }}" 
                               required placeholder="Ingrese el año ejemplo 2024" value="{{ old('year') }}" />
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
    document.getElementById('yearForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const year = document.getElementById('year').value;
        window.open(`/annual-earnings-report/${year}`, '_blank');
        $('#annualEarnings').modal('hide');
    });
</script>