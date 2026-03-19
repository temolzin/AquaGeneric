<div class="modal fade" id="create">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('debtCategories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5>Registrar Categoría</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="color_index">Color (*)</label>
                        <div class="d-flex align-items-center">
                            <select name="color_index" class="form-control" id="colorSelectCategory" required>
                                <option value="">Seleccione un color</option>
                                <option value="0"  data-color="#3498db">Azul</option>
                                <option value="13" data-color="#e74c3c">Rojo</option>
                                <option value="10" data-color="#2ecc71">Verde</option>
                                <option value="4"  data-color="#f39c12">Naranja</option>
                                <option value="1"  data-color="#9b59b6">Púrpura</option>
                                <option value="6"  data-color="#1abc9c">Turquesa</option>
                                <option value="14" data-color="#34495e">Gris oscuro</option>
                                <option value="3"  data-color="#f1c40f">Amarillo</option>
                            </select>

                            <span id="colorPreviewCategory"
                                style="width: 45px; height: 45px;
                                       background-color: #6c757d;
                                       border: 1px solid #ced4da;
                                       margin-left: 5px;">
                            </span>
                        </div>

                        @error('color_index')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="submit" class="btn btn-success">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const select = document.getElementById('colorSelectCategory');
    const preview = document.getElementById('colorPreviewCategory');

    if (!select || !preview) return;

    const updatePreview = () => {
        const selected = select.options[select.selectedIndex];
        const color = selected?.dataset.color || '#6c757d';

        preview.style.backgroundColor = color;
        preview.style.border = `1px solid ${color}`;
    };

    select.addEventListener('change', updatePreview);

    // Inicial
    updatePreview();

    // Cuando se abre el modal
    $('#create').on('shown.bs.modal', function () {
        updatePreview();
    });
});
</script>
