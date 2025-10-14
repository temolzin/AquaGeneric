<div class="modal fade" id="createSection" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card-success">
            <div class="modal-header bg-success text-white">
                <h5 class="card-title">Ingrese los Datos de la Sección <small>&nbsp;(*) Campos requeridos</small></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('sections.store') }}" method="post">
                @csrf
                <div class="card-body">
                    <div class="card border-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Nombre de la sección(*)</label>
                                        <input type="text" class="form-control" name="name" placeholder="Ejemplo: Sección A" value="{{ old('name') }}" required />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="zip_code" class="form-label">Código Postal(*)</label>
                                        <input type="text" class="form-control" name="zip_code" placeholder="Ejemplo: 55010" maxlength="5" value="{{ old('zip_code') }}" required />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="color">Color de identificación(*)</label>
                                        <div class="input-group">
                                            <select name="color" class="form-control" id="colorSelect" required>
                                                <option value="">Seleccione un color</option>
                                                <option value="#e74c3c" {{ old('color') == '#e74c3c' ? 'selected' : '' }}>Rojo</option>
                                                <option value="#3498db" {{ old('color') == '#3498db' ? 'selected' : '' }}>Azul</option>
                                                <option value="#2ecc71" {{ old('color') == '#2ecc71' ? 'selected' : '' }}>Verde</option>
                                                <option value="#f39c12" {{ old('color') == '#f39c12' ? 'selected' : '' }}>Naranja</option>
                                                <option value="#9b59b6" {{ old('color') == '#9b59b6' ? 'selected' : '' }}>Púrpura</option>
                                                <option value="#1abc9c" {{ old('color') == '#1abc9c' ? 'selected' : '' }}>Turquesa</option>
                                                <option value="#34495e" {{ old('color') == '#34495e' ? 'selected' : '' }}>Gris oscuro</option>
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="colorPreview" style="width: 40px; background-color: #f8f9fa;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="locality_id" value="{{ auth()->user()->locality_id }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
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
            const updatePreview = () => {
                if (colorSelect.value) {
                    colorPreview.style.backgroundColor = colorSelect.value;
                    colorPreview.style.border = '1px solid ' + colorSelect.value;
                } else {
                    colorPreview.style.backgroundColor = '#f8f9fa';
                    colorPreview.style.border = '1px solid #ccc';
                }
            };

            colorSelect.addEventListener('change', updatePreview);
            updatePreview();

            $('#createSection').on('shown.bs.modal', function () {
                updatePreview();
            });
        }
    });
</script>
