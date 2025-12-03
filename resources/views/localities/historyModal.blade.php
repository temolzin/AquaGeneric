<div class="modal fade" id="historyModal{{ $locality->id }}" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel{{ $locality->id }}" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document"> 
        <div class="modal-content">
            <div class="modal-header bg-maroon text-white">
                <h5 class="modal-title" id="historyModalLabel{{ $locality->id }}">
                    Historial de Movimientos
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('reports.generatePdfMovementsHistory') }}" method="GET" target="_blank">
                <div class="modal-body p-3"> 
                    <input type="hidden" name="locality_id" value="{{ $locality->id }}">
                    <div class="form-group"> 
                        <label for="module_{{ $locality->id }}">Módulo</label>
                        <select name="module" id="module_{{ $locality->id }}" class="form-control" required>
                            <option value="">Selecciona un módulo</option>
                            <option value="todos">Todos los Módulos</option>
                            <option value="pagos">Pagos</option>
                            <option value="costos">Costos</option>
                            <option value="deudas">Deudas</option>
                            <option value="gastos">Gastos</option>
                        </select>
                    </div>
                    <div class="form-group"> 
                        <label for="responsible_{{ $locality->id }}">Responsable</label>
                        <select name="responsible_id" id="responsible_{{ $locality->id }}" class="form-control">
                            <option value="">Todos los Responsables</option>
                             @foreach($locality->users()->distinct()->get() as $user)
                                @if($user->hasAnyRole(['Supervisor', 'Secretaria']))
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"> 
                        <label for="start_date_{{ $locality->id }}">Fecha Inicio</label>
                        <input type="date" name="start_date" id="start_date_{{ $locality->id }}" class="form-control">
                    </div>
                    <div class="form-group"> 
                        <label for="end_date_{{ $locality->id }}">Fecha Fin</label>
                        <input type="date" name="end_date" id="end_date_{{ $locality->id }}" class="form-control">
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="show_module_column" id="show_module_column_{{ $locality->id }}" class="custom-control-input" value="1">
                        <label class="custom-control-label" for="show_module_column_{{ $locality->id }}">
                            Agrupar por módulo
                        </label>
                    </div>
                </div>
                <div class="modal-footer p-1"> 
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn bg-maroon btn-sm">Generar PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('js')
<script>
    $(document).ready(function() {
        $(document).on('change', 'select[id^="module_"]', function() {
            var selectedValue = $(this).val();
            var checkboxId = $(this).attr('id').replace('module_', '');
            var $checkbox = $('#show_module_column_' + checkboxId);

            if (selectedValue === 'todos') {
                $checkbox.prop('disabled', false);
            } else {
                $checkbox.prop('checked', false).prop('disabled', true);
            }
        });

        $('input[id^="show_module_column_"]').each(function() {
            $(this).trigger('change');
        });

        $(document).on('shown.bs.modal', function(e) {
            $(e.target).find('input[id^="show_module_column_"]').each(function() {
                $(this).trigger('change');
            });
        });
    });
</script>
@endsection
