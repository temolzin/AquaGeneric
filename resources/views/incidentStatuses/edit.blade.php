<div class="modal fade" id="editIncidentStatus{{ $status->id }}" tabindex="-1" role="dialog" aria-labelledby="editIncidentStatusLabel{{ $status->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card-warning">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Actualizar Estatus (*) Campos requeridos</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('incidentStatuses.update', $status->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="card border-0">
                        <div class="card-header py-2 bg-secondary text-white">
                            <h3 class="card-title mb-0">Ingrese Datos del Estatus</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="status">Estatus (*)</label>
                                        <input type="text" name="status" class="form-control @error('status') is-invalid @enderror" value="{{ old('status', $status->status) }}" placeholder="Ingrese el nombre del estatus" required>
                                        @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="color">Color (*)</label>
                                        <div class="input-group">
                                            <select name="color" class="form-control select2" id="colorSelect{{ $status->id }}" required>
                                                <option value="">Seleccione un color</option>
                                                <option value="#e74c3c" {{ old('color', $status->color) == '#e74c3c' ? 'selected' : '' }}>Rojo</option>
                                                <option value="#3498db" {{ old('color', $status->color) == '#3498db' ? 'selected' : '' }}>Azul</option>
                                                <option value="#2ecc71" {{ old('color', $status->color) == '#2ecc71' ? 'selected' : '' }}>Verde</option>
                                                <option value="#f39c12" {{ old('color', $status->color) == '#f39c12' ? 'selected' : '' }}>Naranja</option>
                                                <option value="#9b59b6" {{ old('color', $status->color) == '#9b59b6' ? 'selected' : '' }}>Púrpura</option>
                                                <option value="#1abc9c" {{ old('color', $status->color) == '#1abc9c' ? 'selected' : '' }}>Turquesa</option>
                                                <option value="#34495e" {{ old('color', $status->color) == '#34495e' ? 'selected' : '' }}>Gris oscuro</option>
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text color-preview" id="colorPreview{{ $status->id }}" style="width: 40px; background-color: {{ $status->color }}; border: 1px solid {{ $status->color }};"></span>
                                            </div>
                                        </div>
                                        @error('color') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="description">Descripción (*)</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Ingrese la descripción del estatus">{{ old('description', $status->description) }}</textarea>
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
        const editModals = document.querySelectorAll('[id^="editIncidentStatus"]');
        
        editModals.forEach(modal => {
            const modalId = modal.id.replace('editIncidentStatus', '');
            const colorSelect = document.getElementById('colorSelect' + modalId);
            const colorPreview = document.getElementById('colorPreview' + modalId);
            
            if (colorSelect && colorPreview) {
                $('#colorSelect' + modalId).select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: 'Seleccione un color',
                    allowClear: false,
                    dropdownParent: $('#editIncidentStatus' + modalId)
                });
                
                $('#colorSelect' + modalId).on('change', function() {
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
        });
    });
</script>
