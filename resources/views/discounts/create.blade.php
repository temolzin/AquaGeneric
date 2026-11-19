<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Agregar Descuento <small>&nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('discounts.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Ingrese los datos del descuento</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nombre(*)</label>
                                            <input type="text" class="form-control" name="name" placeholder="Ingrese el nombre del descuento" value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="percentage" class="form-label">Porcentaje(*)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="percentage" placeholder="Ej. 15" value="{{ old('percentage') }}" min="1" max="100" step="0.01" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="color">Color(*)</label>
                                            <div class="d-flex align-items-center" style="gap: 0;">
                                                <select name="color_index" class="form-control select2" id="colorSelect" style="flex: 1;" required>
                                                    <option value="">Seleccione un color</option>
                                                    <option value="13" data-color="#e74c3c">Rojo</option>
                                                    <option value="0"  data-color="#3498db">Azul</option>
                                                    <option value="10" data-color="#2ecc71">Verde</option>
                                                    <option value="4"  data-color="#f39c12">Naranja</option>
                                                    <option value="1"  data-color="#9b59b6">Púrpura</option>
                                                    <option value="6"  data-color="#1abc9c">Turquesa</option>
                                                    <option value="14" data-color="#34495e">Gris oscuro</option>
                                                </select>
                                                <span class="input-group-text" id="colorPreview" style="width: 45px; height: 45px; background-color: #6c757d; padding: 0; border: 1px solid #ced4da; margin-left: -1px;"></span>
                                            </div>
                                            @error('color_index') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción(*)</label>
                                            <textarea class="form-control" name="description" rows="3" maxlength="255" placeholder="Ingrese una descripción" required>{{ old('description') }}</textarea>
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
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorSelect = document.getElementById('colorSelect');
        const colorPreview = document.getElementById('colorPreview');

        const updatePreview = () => {
            if (!colorSelect || !colorPreview) return;
            const selected = colorSelect.options[colorSelect.selectedIndex];
            const color = selected?.dataset.color || '#6c757d';
            colorPreview.style.backgroundColor = color;
            colorPreview.style.border = `1px solid ${color}`;
        };

        const initializeColorSelect = () => {
            if (!colorSelect || !colorPreview) return;

            $('#colorSelect').on('change', updatePreview);
            updatePreview();
        };

        initializeColorSelect();
    });

    $(document).on('shown.bs.modal', '#createExpenseTypeModal', function() {
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
        
        const colorSelect = document.getElementById('colorSelect');
        const colorPreview = document.getElementById('colorPreview');
        if (colorSelect && colorPreview) {
            const selected = colorSelect.options[colorSelect.selectedIndex];
            const color = selected?.dataset.color || '#6c757d';
            colorPreview.style.backgroundColor = color;
            colorPreview.style.border = `1px solid ${color}`;
        }
    });
</script>
