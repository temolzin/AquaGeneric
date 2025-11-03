<div class="modal fade" id="createInventoryCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createInventoryCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Agregar Categoría de Inventario (*) Campos requeridos</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('inventoryCategories.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="card border-0">
                        <div class="card-header py-2 bg-secondary text-white">
                            <h3 class="card-title mb-0">Ingrese Datos de la Categoría de Inventario</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">Nombre (*)</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ingrese el nombre de la categoría" required>
                                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="color">Color (*)</label>
                                        <div class="input-group">
                                            <select name="color" class="form-control @error('color') is-invalid @enderror" id="colorSelect" required>
                                                <option value="">Seleccione un color</option>
                                                <option value="#e74c3c" {{ old('color') == '#e74c3c' ? 'selected' : '' }}>Rojo</option>
                                                <option value="#3498db" {{ old('color') == '#3498db' ? 'selected' : '' }}>Azul</option>
                                                <option value="#2ecc71" {{ old('color') == '#2ecc71' ? 'selected' : '' }}>Verde</option>
                                                <option value="#f39c12" {{ old('color') == '#f39c12' ? 'selected' : '' }}>Naranja</option>
                                                <option value="#9b59b6" {{ old('color') == '#9b59b6' ? 'selected' : '' }}>Púrpura</option>
                                                <option value="#1abc9c" {{ old('color') == '#1abc9c' ? 'selected' : '' }}>Turquesa</option>
                                                <option value="#34495e" {{ old('color') == '#34495e' ? 'selected' : '' }}>Gris oscuro</option>
                                                <option value="#f1c40f" {{ old('color') == '#f1c40f' ? 'selected' : '' }}>Amarillo</option>
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="colorPreview" style="width: 40px; background-color: #f8f9fa;"></span>
                                            </div>
                                        </div>
                                        @error('color') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="description">Descripción</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Ingrese la descripción de la categoría" rows="3">{{ old('description') }}</textarea>
                                        @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
        const colorSelect = document.getElementById('colorSelect');
        const colorPreview = document.getElementById('colorPreview');
        
        if (colorSelect && colorPreview) {
            colorSelect.addEventListener('change', function() {
                if (this.value) {
                    colorPreview.style.backgroundColor = this.value;
                    colorPreview.style.border = '1px solid ' + this.value;
                } else {
                    colorPreview.style.backgroundColor = '#f8f9fa';
                    colorPreview.style.border = '1px solid #ccc';
                }
            });
            
            if (colorSelect.value) {
                colorPreview.style.backgroundColor = colorSelect.value;
                colorPreview.style.border = '1px solid ' + colorSelect.value;
            }
        }
        
        $('#createInventoryCategoryModal').on('shown.bs.modal', function () {
            if (colorSelect && colorSelect.value) {
                colorPreview.style.backgroundColor = colorSelect.value;
                colorPreview.style.border = '1px solid ' + colorSelect.value;
            }
        });
    });
</script>
