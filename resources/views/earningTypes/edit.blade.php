<div class="modal fade" id="editEarningType{{ $earningType->id }}" tabindex="-1" role="dialog" aria-labelledby="editEarningTypeLabel{{ $earningType->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card-warning">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Actualizar Tipo de Ingreso (*) Campos requeridos</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('earningTypes.update', $earningType->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="card border-0">
                        <div class="card-header py-2 bg-secondary text-white">
                            <h3 class="card-title mb-0">Ingrese Datos del Tipo de Ingreso</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">Nombre (*)</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $earningType->name) }}" placeholder="Ingrese el nombre del tipo de ingreso" required>
                                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="color">Color (*)</label>
                                        <div class="input-group">
                                            <select name="color_index" class="form-control" id="colorSelect{{ $earningType->id }}" required>
                                                <option value="">Seleccione un color</option>
                                                <option value="13" data-color="#e74c3c" {{ $earningType->color == 'bg-danger' ? 'selected' : '' }}>Rojo</option>
                                                <option value="0"  data-color="#3498db" {{ $earningType->color == 'bg-blue' ? 'selected' : '' }}>Azul</option>
                                                <option value="10" data-color="#2ecc71" {{ $earningType->color == 'bg-success' ? 'selected' : '' }}>Verde</option>
                                                <option value="4"  data-color="#f39c12" {{ $earningType->color == 'bg-orange' ? 'selected' : '' }}>Naranja</option>
                                                <option value="1"  data-color="#9b59b6" {{ $earningType->color == 'bg-purple' ? 'selected' : '' }}>Púrpura</option>
                                                <option value="6"  data-color="#1abc9c" {{ $earningType->color == 'bg-teal' ? 'selected' : '' }}>Turquesa</option>
                                                <option value="14" data-color="#34495e" {{ $earningType->color == 'bg-secondary' ? 'selected' : '' }}>Gris oscuro</option>
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text color-preview" id="colorPreview{{ $earningType->id }}" style="width: 40px; background-color: {{ $earningType->color ? pdf_color($earningType->color) : '#6c757d' }};"></span>
                                            </div>
                                        </div>
                                        @error('color_index') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="description">Descripción</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Ingrese la descripción del tipo de ingreso">{{ old('description', $earningType->description) }}</textarea>
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
    function initializeColorSelects() {
        document.querySelectorAll('[id^="colorSelect"]').forEach(colorSelect => {
            const colorPreview = document.getElementById(colorSelect.id.replace('colorSelect', 'colorPreview'));
            
            if (!colorSelect || !colorPreview) return;
            
            const updatePreview = () => {
                const selected = colorSelect.options[colorSelect.selectedIndex];
                const color = selected?.dataset.color || '#6c757d';
                colorPreview.style.backgroundColor = color;
                colorPreview.style.border = `1px solid ${color}`;
            };

            colorSelect.addEventListener('change', updatePreview);
            updatePreview();
        });
    }

    $(document).ready(function() {
        initializeColorSelects();
    });

    $(document).on('shown.bs.modal', '[id^="editEarningType"]', function() {
        initializeColorSelects();
    });
</script>
