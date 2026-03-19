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
                        <select name="debt_category_id" id="assign_debt_category_id" class="form-control select2" required>
                            @if(isset($debtCategories) && $debtCategories->count())
                                @foreach($debtCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            @else
                                <option value="">Servicio de Agua</option>
                            @endif
                        </select>
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
