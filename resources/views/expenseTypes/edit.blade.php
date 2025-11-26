<div class="modal fade" id="editExpenseType{{ $expenseType->id }}" tabindex="-1" role="dialog" aria-labelledby="editExpenseTypeLabel{{ $expenseType->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card-warning">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Actualizar Tipo de Gasto (*) Campos requeridos</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('expenseTypes.update', $expenseType->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="card border-0">
                        <div class="card-header py-2 bg-secondary text-white">
                            <h3 class="card-title mb-0">Ingrese Datos del Tipo de Gasto</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">Nombre (*)</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $expenseType->name) }}" placeholder="Ingrese el nombre del tipo de gasto" required>
                                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="color">Color (*)</label>
                                        <div class="input-group">
                                            <select name="color_index" class="form-control select2" id="colorSelect{{ $expenseType->id }}" required>
                                                <option value="">Seleccione un color</option>
                                                <option value="13" data-color="#e74c3c" {{ $expenseType->color == 'bg-danger' ? 'selected' : '' }}>Rojo</option>
                                                <option value="0"  data-color="#3498db" {{ $expenseType->color == 'bg-blue' ? 'selected' : '' }}>Azul</option>
                                                <option value="10" data-color="#2ecc71" {{ $expenseType->color == 'bg-success' ? 'selected' : '' }}>Verde</option>
                                                <option value="4"  data-color="#f39c12" {{ $expenseType->color == 'bg-orange' ? 'selected' : '' }}>Naranja</option>
                                                <option value="1"  data-color="#9b59b6" {{ $expenseType->color == 'bg-purple' ? 'selected' : '' }}>Púrpura</option>
                                                <option value="6"  data-color="#1abc9c" {{ $expenseType->color == 'bg-teal' ? 'selected' : '' }}>Turquesa</option>
                                                <option value="14" data-color="#34495e" {{ $expenseType->color == 'bg-secondary' ? 'selected' : '' }}>Gris oscuro</option>
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text color-preview" id="colorPreview{{ $expenseType->id }}" style="width: 40px; background-color: {{ $expenseType->color ? pdf_color($expenseType->color) : '#6c757d' }};"></span>
                                            </div>
                                        </div>
                                        @error('color_index') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="description">Descripción</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Ingrese la descripción del tipo de gasto">{{ old('description', $expenseType->description) }}</textarea>
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
        const editModals = document.querySelectorAll('[id^="editExpenseType"]');
        
        const updatePreview = (select, preview) => {
            const selected = select.options[select.selectedIndex];
            const color = selected?.dataset.color || '#6c757d';
            preview.style.backgroundColor = color;
            preview.style.border = `1px solid ${color}`;
        };

        const initializeColorSelect = (modalId) => {
            const colorSelect = document.getElementById('colorSelect' + modalId);
            const colorPreview = document.getElementById('colorPreview' + modalId);
            
            if (!colorSelect || !colorPreview) return;

            $('#colorSelect' + modalId).select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Seleccione un color',
                allowClear: false,
                dropdownParent: $('#editExpenseType' + modalId)
            });
            
            $('#colorSelect' + modalId).on('change', function() {
                updatePreview(this, colorPreview);
            });
            
            updatePreview(colorSelect, colorPreview);
        };

        editModals.forEach(modal => {
            const modalId = modal.id.replace('editExpenseType', '');
            initializeColorSelect(modalId);
        });
    });
</script>
