<div class="modal fade" id="assignDebtModal" tabindex="-1" role="dialog" aria-labelledby="assignDebtModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="assignDebtModalLabel">Asignar Deuda a Todos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('debts.assignAll') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="start_date">Mes de Inicio</label>
                        <input type="month" name="start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">Mes de Fin</label>
                        <input type="month" name="end_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="note">Nota</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Ingresa una nota para la deuda"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="debt_category_id">Categoría</label>
                        @php
                            $serviceCat = null;
                            if (isset($debtCategories)) {
                                $serviceCat = $debtCategories->firstWhere('name', 'Servicio de Agua');
                            }
                        @endphp
                        @if($serviceCat)
                            <input type="hidden" name="debt_category_id" value="{{ $serviceCat->id }}">
                            <input type="text" class="form-control" value="{{ $serviceCat->name }}" disabled>
                        @else
                            <input type="hidden" name="debt_category_id" value="">
                            <input type="text" class="form-control" value="Servicio de Agua" disabled>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar Deuda</button>
                </div>
            </form>
        </div>
    </div>
</div>
