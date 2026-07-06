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
                                            <input type="text" class="form-control" name="name" placeholder="Ingrese el nombre del descuento" value="{{ old('name',$discount->name) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Porcentaje (*)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="percentage" min="1" max="100" step="0.01" value="{{ old('percentage',$discount->percentage) }}" required>
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
                                                <select name="color" id="colorSelect{{ $discount->id }}" class="form-control" required>
                                                    <option value="">Seleccione un color</option>
                                                    <option value="#e74c3c" data-color="#e74c3c">Rojo</option>
                                                    <option value="#3498db" data-color="#3498db">Azul</option>
                                                    <option value="#2ecc71" data-color="#2ecc71">Verde</option>
                                                    <option value="#f39c12" data-color="#f39c12">Naranja</option>
                                                    <option value="#9b59b6" data-color="#9b59b6">Púrpura</option>
                                                    <option value="#1abc9c" data-color="#1abc9c">Turquesa</option>
                                                    <option value="#34495e" data-color="#34495e">Gris oscuro</option>
                                                </select>
                                                <span id="colorPreview{{ $discount->id }}" class="input-group-text" style="width:45px;height:38px;padding:0;"></span>
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
            const color = colorSelect.val() || '#6c757d';
            colorPreview.css({
                'background-color': color,
                'border': '1px solid ' + color
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

<script>
    (function(){
        $(document).on('submit', '[id^="editDiscountForm"]', function(e){
            e.preventDefault();
            const form = $(this);
            const name = $.trim(form.find('[name="name"]').val());
            const percentage = parseFloat(form.find('[name="percentage"]').val());
            const color = form.find('[name="color"]').val();

            if (!name) {
                Swal.fire({icon:'error', title:'Error', text:'Por favor ingresa el nombre del descuento.'});
                return;
            }
            if (isNaN(percentage) || percentage <= 0 || percentage > 100) {
                Swal.fire({icon:'error', title:'Error', text:'Por favor ingresa un porcentaje válido (1-100).'});
                return;
            }
            if (!color) {
                Swal.fire({icon:'error', title:'Error', text:'Por favor selecciona un color para el descuento.'});
                return;
            }

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method') || 'POST',
                data: form.serialize(),
                success: function(resp){
                    console.log('edit discount success response', resp);
                    const msg = resp && resp.success ? resp.success : 'Descuento actualizado con éxito.';
                    form.closest('.modal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({icon:'success', title:'Actualizado', text: msg}).then(function(){
                            location.reload();
                        });
                    } else {
                        alert(msg);
                        location.reload();
                    }
                },
                error: function(xhr){
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const first = Object.keys(errors)[0];
                        Swal.fire({icon:'error', title:'Error', text: errors[first][0]});
                        return;
                    }
                    Swal.fire({icon:'error', title:'Error', text: 'Ocurrió un error al actualizar el descuento.'});
                }
            });
        });
    })();
</script>
