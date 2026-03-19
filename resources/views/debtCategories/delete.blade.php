<div class="modal fade" id="delete{{ $category->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteCategoryLabel">Eliminar categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('debtCategories.destroy', $category->id) }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-body text-center text-danger">
                    ¿Estás seguro de eliminar la categoría
                    <strong>{{ $category->name }}</strong>?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-danger">
                        Confirmar
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
