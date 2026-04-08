<div class="modal fade" id="createDebtCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createDebtCategoryLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Agregar Categoría de Deuda (*) Campos requeridos</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                    style="opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('debtCategories.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="card border-0">
                        <div class="card-header py-2 bg-secondary text-white">
                            <h3 class="card-title mb-0">Ingrese Datos de la Categoría</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"><i
                                        class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">Nombre (*)</label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" placeholder="Ingrese el nombre de la categoría"
                                            required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="color">Color (*)</label>
                                        <div class="d-flex align-items-center" style="gap: 0;">
                                            <select name="color_index" class="form-control select2"
                                                id="colorSelectDebt" style="flex: 1;" required>
                                                <option value="">Seleccione un color</option>
                                                <option value="0" data-color="#3498db">Azul</option>
                                                <option value="13" data-color="#e74c3c">Rojo</option>
                                                <option value="10" data-color="#2ecc71">Verde</option>
                                                <option value="4" data-color="#f39c12">Naranja</option>
                                                <option value="1" data-color="#9b59b6">Púrpura</option>
                                                <option value="6" data-color="#1abc9c">Turquesa</option>
                                                <option value="14" data-color="#34495e">Gris oscuro</option>
                                                <option value="3" data-color="#f1c40f">Amarillo</option>
                                            </select>
                                            <div id="colorPreviewDebt"
                                                style="width: 30px; height: 30px; border-radius: 4px; margin-left: 10px; border: 1px solid #6c757d;">
                                            </div>
                                        </div>
                                        @error('color_index')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="description">Descripción (*)</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                            placeholder="Ingrese la descripción de la categoría" required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorSelect = document.getElementById('colorSelectDebt');
        const colorPreview = document.getElementById('colorPreviewDebt');

        const updatePreview = () => {
            if (!colorSelect || !colorPreview) return;
            const selected = colorSelect.options[colorSelect.selectedIndex];
            const color = selected?.dataset.color || '#6c757d';
            colorPreview.style.backgroundColor = color;
            colorPreview.style.border = `1px solid ${color}`;
        };

        const initializeColorSelect = () => {
            if (!colorSelect || !colorPreview) return;
            $('#colorSelectDebt').on('change', updatePreview);
            updatePreview();
        };
        initializeColorSelect();
    });

    $(document).on('shown.bs.modal', '#createDebtCategoryModal', function() {
        var modalElement = $(this);
        var dropdownParent = modalElement.find('.modal-body');
        modalElement.find('.select2').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    dropdownParent: dropdownParent,
                    allowClear: false,
                    width: '100%'
                });
            }
        });

        modalElement.on('keydown', function(e) {
            if ($('.select2-container--open').length && e.keyCode === 27) {
                e.stopPropagation();
            }
        });

        const colorSelectCategory = document.getElementById('colorSelectDebt');
        const colorPreviewCategory = document.getElementById('colorPreviewDebt');

        if (colorSelectCategory && colorPreviewCategory) {
            const selected = colorSelectCategory.options[colorSelectCategory.selectedIndex];
            const color = selected?.dataset.color || '#6c757d';
            colorPreviewCategory.style.backgroundColor = color;
            colorPreviewCategory.style.border = `1px solid ${color}`;
        }
    });
</script>
