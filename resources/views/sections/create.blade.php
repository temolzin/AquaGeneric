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
                                        <label for="color" class="form-label">Color de identificación(*)</label>
                                        <div class="input-group">
                                            <select name="color_index" class="form-control select2" id="colorSelect" required>
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
                                                <span class="input-group-text" id="colorPreview" style="width: 40px; background-color: #f8f9fa;"></span>
                                            </div>
                                        </div>
                                        @error('color_index') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
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

        const updatePreview = () => {
            const selected = colorSelect.options[colorSelect.selectedIndex];
            const color = selected?.dataset.color || '#6c757d';
            colorPreview.style.backgroundColor = color;
            colorPreview.style.border = '1px solid ' + color;
        };

        if (colorSelect && colorPreview) {
            $('#colorSelect').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Seleccione un color',
                allowClear: false
            });

            $('#colorSelect').on('change', function() {
                updatePreview();
            });

            updatePreview();

            $('#createSection').on('shown.bs.modal', function () {
                updatePreview();
            });
        }
    });
</script>
