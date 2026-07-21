<div class="modal fade" id="discountHistoryModal" tabindex="-1" role="dialog" aria-labelledby="discountHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="discountHistoryModalLabel">Historial de Descuentos</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('discounts.generateHistoryPdf') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <input type="hidden" name="locality_id" value="{{ auth()->user()->locality_id }}">
                    <div class="form-group">
                        <label for="discount_history_module">Módulo</label>
                        <select name="module" id="discount_history_module" class="form-control" required>
                            <option value="">Selecciona un módulo</option>
                            <option value="todos">Todos los módulos</option>
                            <option value="pagos">Pagos</option>
                            <option value="deudas">Deudas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="discount_history_start_date">Fecha Inicio</label>
                        <input type="date" name="start_date" id="discount_history_start_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="discount_history_end_date">Fecha Fin</label>
                        <input type="date" name="end_date" id="discount_history_end_date" class="form-control">
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="show_module_column" id="discount_history_show_module_column" class="custom-control-input" value="1">
                        <label class="custom-control-label" for="discount_history_show_module_column">Agrupar por módulo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-info">Generar PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('js')
<script>
    $(document).ready(function () {
        $('#discount_history_module').on('change', function () {
            const selectedValue = $(this).val();
            const $checkbox = $('#discount_history_show_module_column');

            const shouldEnableGrouping = selectedValue === 'todos';

            $checkbox.prop('disabled', !shouldEnableGrouping);

            if (!shouldEnableGrouping) {
                $checkbox.prop('checked', false);
            }
        }).trigger('change');
    });
</script>
@endsection
