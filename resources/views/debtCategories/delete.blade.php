<div class="modal fade" id="deleteDebtCategory{{ $category->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteDebtCategoryLabel{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('debtCategories.destroy', $category->id) }}" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteDebtCategoryLabel{{ $category->id }}">Eliminar Categoría de Deuda</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center text-danger">
                    ¿Estás seguro de eliminar la categoría <strong>{{ $category->name }}</strong>?
                    <br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>
