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
                                            <select name="color" class="form-control select2" id="colorSelect{{ $section->id }}" required>
                                                <option value="">Selecciona un color</option>
                                                <option value="#e74c3c" {{ $section->color == '#e74c3c' ? 'selected' : '' }}>Rojo</option>
                                                <option value="#3498db" {{ $section->color == '#3498db' ? 'selected' : '' }}>Azul</option>
                                                <option value="#2ecc71" {{ $section->color == '#2ecc71' ? 'selected' : '' }}>Verde</option>
                                                <option value="#f39c12" {{ $section->color == '#f39c12' ? 'selected' : '' }}>Naranja</option>
                                                <option value="#9b59b6" {{ $section->color == '#9b59b6' ? 'selected' : '' }}>Púrpura</option>
                                                <option value="#1abc9c" {{ $section->color == '#1abc9c' ? 'selected' : '' }}>Turquesa</option>
                                                <option value="#34495e" {{ $section->color == '#34495e' ? 'selected' : '' }}>Gris oscuro</option>
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="colorPreview{{ $section->id }}" style="width: 40px; background-color: {{ $section->color ?? '#f8f9fa' }}"></span>
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
    .select2-container .select2-selection--single {
        height: 36px; 
        display: flex;
        align-items: center;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('colorSelect{{ $section->id }}');
        const preview = document.getElementById('colorPreview{{ $section->id }}');

        if (select && preview) {
            $('#colorSelect{{ $section->id }}').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Selecciona un color',
                allowClear: false,
                dropdownParent: $('#edit{{ $section->id }}')
            });

            const updatePreview = () => {
                if (select.value) {
                    preview.style.backgroundColor = select.value;
                    preview.style.border = '1px solid ' + select.value;
                } else {
                    preview.style.backgroundColor = '#f8f9fa';
                    preview.style.border = '1px solid #ccc';
                }
            };

            $('#colorSelect{{ $section->id }}').on('change', updatePreview);
            updatePreview();

            $('#edit{{ $section->id }}').on('shown.bs.modal', function () {
                updatePreview();
            });
        }
    });
</script>
