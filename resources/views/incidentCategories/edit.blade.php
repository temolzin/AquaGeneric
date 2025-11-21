<div class="modal fade" id="edit{{ $category->id }}" tabindex="-1" role="dialog" aria-labelledby="editIncidentCategoryLabel{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card-warning">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Actualizar Categoría (*) Campos requeridos</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('incidentCategories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="card border-0">
                        <div class="card-header py-2 bg-secondary text-white">
                            <h3 class="card-title mb-0">Ingrese Datos de la Categoría</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">Nombre (*)</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" placeholder="Ingrese el nombre de la categoría" required>
                                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="color">Color (*)</label>
                                        <div class="input-group">
                                            <select name="color" class="form-control select2" id="colorSelectCategory{{ $category->id }}" required>
                                                <option value="">Seleccione un color</option>
                                                <option value="#3498db" {{ old('color', $category->color) == '#3498db' ? 'selected' : '' }}>Azul</option>
                                                <option value="#e74c3c" {{ old('color', $category->color) == '#e74c3c' ? 'selected' : '' }}>Rojo</option>
                                                <option value="#2ecc71" {{ old('color', $category->color) == '#2ecc71' ? 'selected' : '' }}>Verde</option>
                                                <option value="#f39c12" {{ old('color', $category->color) == '#f39c12' ? 'selected' : '' }}>Naranja</option>
                                                <option value="#9b59b6" {{ old('color', $category->color) == '#9b59b6' ? 'selected' : '' }}>Púrpura</option>
                                                <option value="#1abc9c" {{ old('color', $category->color) == '#1abc9c' ? 'selected' : '' }}>Turquesa</option>
                                                <option value="#34495e" {{ old('color', $category->color) == '#34495e' ? 'selected' : '' }}>Gris oscuro</option>
                                                <option value="#f1c40f" {{ old('color', $category->color) == '#f1c40f' ? 'selected' : '' }}>Amarillo</option>
                                                <option value="#e67e22" {{ old('color', $category->color) == '#e67e22' ? 'selected' : '' }}>Naranja oscuro</option>
                                                <option value="#2980b9" {{ old('color', $category->color) == '#2980b9' ? 'selected' : '' }}>Azul oscuro</option>
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text color-preview" id="colorPreviewCategory{{ $category->id }}" style="width: 40px; background-color: {{ $category->color }}; border: 1px solid {{ $category->color }};"></span>
                                            </div>
                                        </div>
                                        @error('color') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="description">Descripción (*)</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Ingrese la descripción de la categoría">{{ old('description', $category->description) }}</textarea>
                                        @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModals = document.querySelectorAll('[id^="edit"]');
        
        const updatePreview = (select, preview) => {
            const hasValue = select.value;
            preview.style.backgroundColor = hasValue ? select.value : '#f8f9fa';
            preview.style.border = hasValue ? `1px solid ${select.value}` : '1px solid #ccc';
        };

        const initializeColorSelect = (modalId) => {
            const colorSelect = document.getElementById('colorSelectCategory' + modalId);
            const colorPreview = document.getElementById('colorPreviewCategory' + modalId);
            
            if (!colorSelect || !colorPreview) return;

            $('#colorSelectCategory' + modalId).select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Seleccione un color',
                allowClear: false,
                dropdownParent: $('#editIncidentCategory' + modalId)
            });
            
            $('#colorSelectCategory' + modalId).on('change', function() {
                updatePreview(this, colorPreview);
            });
            
            updatePreview(colorSelect, colorPreview);
        };

        editModals.forEach(modal => {
            const modalId = modal.id.replace('edit', '');
            initializeColorSelect(modalId);
        });
    });
</script>
