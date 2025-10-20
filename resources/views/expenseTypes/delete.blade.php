<div class="modal fade" id="deleteExpenseType{{ $expenseType->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteExpenseTypeLabel{{ $expenseType->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteExpenseTypeLabel{{ $expenseType->id }}">Eliminar Tipo de Gasto</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('expenseTypes.destroy', $expenseType->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center text-danger">
                    ¿Estás seguro de que quieres eliminar el tipo de gasto <strong>{{ $expenseType->name }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>
