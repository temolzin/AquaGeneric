<div class="modal fade" id="edit{{ $discount->id }}" tabindex="-1" role="dialog" aria-labelledby="editDiscountLabel{{ $discount->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">
                            Actualizar Descuento
                            <small>&nbsp;(*) Campos requeridos</small>
                        </h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close" onclick="resetForm{{ $discount->id }}()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="editDiscountForm{{ $discount->id }}" action="{{ route('discounts.update',$discount->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Ingrese los datos del descuento</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Nombre (*)</label>
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $discount->name) }}" placeholder="Ingrese el nombre del tipo de ingreso" required>
                                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Porcentaje (*)</label>
                                            <div class="input-group">
                                                <input type="number" name="percentage" class="form-control" value="{{ old('percentage', $discount->percentage) }}" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Color (*)</label>
                                            <div class="d-flex align-items-center">
                                                <select name="color_index" class="form-control select2" id="colorSelect{{ $discount->id }}" style="flex: 1;" required>
                                                    <option value="">Seleccione un color</option>
                                                    <option value="13" data-color="#e74c3c" {{ $discount->color == 'bg-danger' ? 'selected' : '' }}>Rojo</option>
                                                    <option value="0"  data-color="#3498db" {{ $discount->color == 'bg-blue' ? 'selected' : '' }}>Azul</option>
                                                    <option value="10" data-color="#2ecc71" {{ $discount->color == 'bg-success' ? 'selected' : '' }}>Verde</option>
                                                    <option value="4"  data-color="#f39c12" {{ $discount->color == 'bg-orange' ? 'selected' : '' }}>Naranja</option>
                                                    <option value="1"  data-color="#9b59b6" {{ $discount->color == 'bg-purple' ? 'selected' : '' }}>Púrpura</option>
                                                    <option value="6"  data-color="#1abc9c" {{ $discount->color == 'bg-teal' ? 'selected' : '' }}>Turquesa</option>
                                                    <option value="14" data-color="#34495e" {{ $discount->color == 'bg-secondary' ? 'selected' : '' }}>Gris oscuro</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text color-preview" id="colorPreview{{ $discount->id }}" style="width: 45px; height: 45px; padding: 0; background-color: {{ $discount->color ? pdf_color($discount->color) : '#6c757d' }}; border: 1px solid #ced4da;"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Descripción</label>
                                            <textarea class="form-control" name="description" rows="3" maxlength="255" placeholder="Ingrese una descripción">{{ old('description',$discount->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm{{ $discount->id }}()">Cerrar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const colorSelect = $('#colorSelect{{ $discount->id }}');
        const colorPreview = $('#colorPreview{{ $discount->id }}');

        function updatePreview() {
            const selected = colorSelect.find(':selected');
            const color = selected.data('color') || '#6c757d';

            colorPreview.css({
                backgroundColor: color,
                border: '1px solid ' + color
            });
        }

        colorSelect.on('change', updatePreview);
        updatePreview();
    });

    function resetForm{{ $discount->id }}() {
        document.getElementById('editDiscountForm{{ $discount->id }}').reset();

        $('#colorSelect{{ $discount->id }}').trigger('change');
    }
</script>
