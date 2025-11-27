<div class="modal fade" id="createExpenseTypeModal" tabindex="-1" role="dialog" aria-labelledby="createExpenseTypeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Agregar Tipo de Gasto (*) Campos requeridos</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('expenseTypes.store') }}" method="POST">
                @csrf
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
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ingrese el nombre del tipo de gasto" required>
                                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="color">Color(*)</label>
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
                                        @error('color_index') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="description">Descripción</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Ingrese la descripción del tipo de gasto">{{ old('description') }}</textarea>
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

<style>
    .input-group .select2-container .select2-selection--single {
        height: 38px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorSelect = document.getElementById('colorSelect');
        const colorPreview = document.getElementById('colorPreview');

        const updatePreview = () => {
            const selected = colorSelect.options[colorSelect.selectedIndex];
            const color = selected?.dataset.color || '#6c757d';
            colorPreview.style.backgroundColor = color;
            colorPreview.style.border = `1px solid ${color}`;
        };

        const initializeColorSelect = () => {
            if (!colorSelect || !colorPreview) return;

            $('#colorSelect').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Seleccione un color',
                allowClear: false
            });

            $('#colorSelect').on('change', updatePreview);
            updatePreview();
        };

        initializeColorSelect();

        $('#createExpenseTypeModal').on('shown.bs.modal', updatePreview);
    });
</script>
