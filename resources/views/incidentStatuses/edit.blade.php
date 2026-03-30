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
                                        <div class="d-flex align-items-center" style="gap: 0;">
                                            <select name="color_index" class="form-control select2" id="colorSelect{{ $status->id }}" style="flex: 1;" required>
                                                <option value="">Seleccione un color</option>
                                                <option value="13" data-color="#e74c3c" {{ $status->color == 'bg-danger' ? 'selected' : '' }}>Rojo</option>
                                                <option value="0"  data-color="#3498db" {{ $status->color == 'bg-blue' ? 'selected' : '' }}>Azul</option>
                                                <option value="10" data-color="#2ecc71" {{ $status->color == 'bg-success' ? 'selected' : '' }}>Verde</option>
                                                <option value="4"  data-color="#f39c12" {{ $status->color == 'bg-orange' ? 'selected' : '' }}>Naranja</option>
                                                <option value="1"  data-color="#9b59b6" {{ $status->color == 'bg-purple' ? 'selected' : '' }}>Púrpura</option>
                                                <option value="6"  data-color="#1abc9c" {{ $status->color == 'bg-teal' ? 'selected' : '' }}>Turquesa</option>
                                                <option value="14" data-color="#34495e" {{ $status->color == 'bg-secondary' ? 'selected' : '' }}>Gris oscuro</option>
                                            </select>
                                                <span class="input-group-text color-preview" id="colorPreview{{ $status->id }}"
                                                    style="width: 45px; height: 45px; padding: 0; background-color: {{ $status->color ? pdf_color($status->color) : '#6c757d' }}; border: 1px solid #ced4da; margin-left: -1px;"></span>
                                        </div>
                                        @error('color_index') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
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
        document.querySelectorAll('[id^="colorSelect"]').forEach(el => {
            const id = el.id.replace('colorSelect', '');
            const colorSelect = document.getElementById('colorSelect' + id);
            const colorPreview = document.getElementById('colorPreview' + id);

            const updatePreview = () => {
                if (!colorSelect || !colorPreview) return;
                const selected = colorSelect.options[colorSelect.selectedIndex];
                const color = selected?.dataset.color || '#6c757d';
                colorPreview.style.backgroundColor = color;
                colorPreview.style.border = `1px solid ${color}`;
            };

            if (colorSelect && colorPreview) {
                $('#colorSelect' + id).on('change', updatePreview);
                updatePreview();
            }
        });
    });

    $(document).on('shown.bs.modal', '[id^="editIncidentStatus"]', function() {
        var modalElement = $(this);
        var dropdownParent = modalElement.find('.modal-body');
        
        modalElement.find('.select2').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    dropdownParent: dropdownParent,
                    allowClear: false,
                    width: '100%'
                });
            }
        });
        
        modalElement.on('keydown', function(e) {
            if ($('.select2-container--open').length && e.keyCode === 27) {
                e.stopPropagation();
            }
        });
        
        modalElement.find('[id^="colorSelect"]').each(function() {
            const id = this.id.replace('colorSelect', '');
            const colorSelect = document.getElementById('colorSelect' + id);
            const colorPreview = document.getElementById('colorPreview' + id);
            if (colorSelect && colorPreview) {
                const selected = colorSelect.options[colorSelect.selectedIndex];
                const color = selected?.dataset.color || '#6c757d';
                colorPreview.style.backgroundColor = color;
                colorPreview.style.border = `1px solid ${color}`;
            }
        });
    });
</script>
