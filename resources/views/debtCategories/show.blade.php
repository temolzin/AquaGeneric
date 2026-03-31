<div class="modal fade" id="view{{ $category->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> {{ $category->name }}</p>
                <p><strong>Descripción:</strong> {{ $category->description }}</p>
                <p><strong>Color:</strong> {{ $category->color }}</p>
                <p><strong>Localidad:</strong> {{ $category->locality_id ? $category->locality->name ?? $category->locality_id : 'Global' }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
