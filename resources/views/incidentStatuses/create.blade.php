<div class="modal fade" id="createIncidentStatusModal" tabindex="-1" role="dialog" aria-labelledby="createIncidentStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Agregar Estatus de Incidencia (*) Campos requeridos</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('incidentStatuses.store') }}" method="POST">
                @csrf
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
                                        <input type="text" name="status" class="form-control @error('status') is-invalid @enderror" value="{{ old('status') }}" placeholder="Ingrese el nombre del estatus" required>
                                        @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="color">Color (*)</label>
                                        <div class="input-group">
                                            <select name="color_index" class="form-control" id="colorSelect" required>
                                                <option value="">Seleccione un color</option>
                                                <option value="13" data-color="#e74c3c">Rojo</option>
                                                <option value="0"  data-color="#3498db">Azul</option>
                                                <option value="10" data-color="#2ecc71">Verde</option>
                                                <option value="4"  data-color="#f39c12">Naranja</option>
                                                <option value="1"  data-color="#9b59b6">Púrpura</option>
                                                <option value="6"  data-color="#1abc9c">Turquesa</option>
                                                <option value="14" data-color="#34495e">Gris oscuro</option>
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="colorPreview" style="width: 40px; background-color: #6c757d;"></span>
                                            </div>
                                        </div>
                                        @error('color_index') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="description">Descripción (*)</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Ingrese la descripción del estatus">{{ old('description') }}</textarea>
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
    function initializeColorSelect() {
        const colorSelect = document.getElementById('colorSelect');
        const colorPreview = document.getElementById('colorPreview');

        if (!colorSelect || !colorPreview) return;

        const updatePreview = () => {
            const selected = colorSelect.options[colorSelect.selectedIndex];
            const color = selected?.dataset.color;

            if (color) {
                colorPreview.style.backgroundColor = color;
                colorPreview.style.border = '1px solid ' + color;
            } else {
                colorPreview.style.backgroundColor = '#6c757d';
                colorPreview.style.border = '1px solid #ccc';
            }
        };

        colorSelect.addEventListener('change', updatePreview);
        updatePreview();
    }

    $(document).ready(function() {
        initializeColorSelect();
    });

    $(document).on('shown.bs.modal', '#createIncidentStatusModal', function() {
        initializeColorSelect();
    });
</script>
