<div class="modal fade" id="edit{{ $discount->id }}" tabindex="-1" role="dialog" aria-labelledby="editDiscountLabel" aria-hidden="true">
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
                <form id="editDiscountForm{{ $discount->id }}" action="{{ route('discounts.update', $discount->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">
                                    Ingrese los datos del descuento
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Nombre (*)</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name', $discount->name) }}" placeholder="Ingrese el nombre" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Porcentaje (%) (*)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="percentage" min="0" max="100" step="0.01" value="{{ old('percentage', $discount->percentage) }}" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Descripción</label>
                                            <textarea class="form-control" name="description" rows="4" placeholder="Ingrese una descripción">{{ old('description', $discount->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm{{ $discount->id }}()">
                            Cerrar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function resetForm{{ $discount->id }}() {
        document.getElementById('editDiscountForm{{ $discount->id }}').reset();
    }
</script>
