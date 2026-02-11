<div class="modal fade" id="edit{{ $section->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card-warning">
            <div class="card-header bg-warning text-white">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h4 class="card-title">Editar Sección <small> &nbsp;(*) Campos requeridos</small></h4>
                    <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form action="{{ route('sections.update', $section->id) }}" method="post" id="edit-section-form-{{ $section->id }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Nombre de la Sección(*)</label>
                                        <input type="text" class="form-control" name="name" placeholder="Ingresa el nombre de la sección" value="{{ $section->name }}" required />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="zip_code" class="form-label">Código Postal(*)</label>
                                        <input type="text" class="form-control" name="zip_code" placeholder="Ingresa el código postal" value="{{ $section->zip_code }}" required />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="color" class="form-label">Color(*)</label>
                                        <div class="input-group">
                                            <select name="color_index" class="form-control" id="colorSelect{{ $section->id }}" required>
                                                <option value="">Selecciona un color</option>
                                                <option value="13" data-color="#e74c3c" {{ $section->color == 'bg-danger' ? 'selected' : '' }}>Rojo</option>
                                                <option value="0"  data-color="#3498db" {{ $section->color == 'bg-blue' ? 'selected' : '' }}>Azul</option>
                                                <option value="10" data-color="#2ecc71" {{ $section->color == 'bg-success' ? 'selected' : '' }}>Verde</option>
                                                <option value="4"  data-color="#f39c12" {{ $section->color == 'bg-orange' ? 'selected' : '' }}>Naranja</option>
                                                <option value="1"  data-color="#9b59b6" {{ $section->color == 'bg-purple' ? 'selected' : '' }}>Púrpura</option>
                                                <option value="6"  data-color="#1abc9c" {{ $section->color == 'bg-teal' ? 'selected' : '' }}>Turquesa</option>
                                                <option value="14" data-color="#34495e" {{ $section->color == 'bg-secondary' ? 'selected' : '' }}>Gris oscuro</option>
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="colorPreview{{ $section->id }}" style="width: 40px; background-color: {{ $section->color ? pdf_color($section->color) : '#f8f9fa' }}; border: 1px solid {{ $section->color ? pdf_color($section->color) : '#ccc' }};">
                                                </span>
                                            </div>
                                        </div>
                                        @error('color_index') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <input type="hidden" name="locality_id" value="{{ auth()->user()->locality_id }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm({{ $section->id }})">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-group {
        margin-bottom: 0.5rem;
    }
    .modal-content {
        padding-bottom: 0.2rem;
    }
    .modal-footer {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }

</style>

<script>
    function initializeColorSelect() {
        const select = document.getElementById('colorSelect{{ $section->id }}');
        const preview = document.getElementById('colorPreview{{ $section->id }}');

        if (!select || !preview) return;

        if ($(select).hasClass('select2-hidden-accessible')) {
            $(select).select2('destroy');
        }

        initializeSelect2Custom('#colorSelect{{ $section->id }}', {
            theme: 'bootstrap4',
            placeholder: 'Selecciona un color',
            allowClear: false
        });

        const updatePreview = () => {
            const selected = select.options[select.selectedIndex];
            const color = selected?.dataset.color;
            if (color) {
                preview.style.backgroundColor = color;
                preview.style.border = '1px solid ' + color;
            } else {
                preview.style.backgroundColor = '#f8f9fa';
                preview.style.border = '1px solid #ccc';
            }
        };

        $('#colorSelect{{ $section->id }}').off('change').on('change', updatePreview);
        updatePreview();
    }

    $(document).ready(function() {
        initializeColorSelect();
    });

    $(document).on('shown.bs.modal', '#edit{{ $section->id }}', function() {
        initializeColorSelect();
    });
</script>
