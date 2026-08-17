<div class="modal fade" id="delete{{ $discount->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteDiscountLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">
                    Eliminar Descuento
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center text-danger">
                    ¿Estás seguro de eliminar el descuento
                    <strong>{{ $discount->name }}</strong>?
                    <br><br>
                    Esta acción no podrá deshacerse.
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
