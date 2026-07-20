<div class="modal fade" id="createDebt" tabindex="-1" role="dialog" aria-labelledby="createDebtLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Agregar Deuda <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('debts.store') }}" method="post" enctype="multipart/form-data" id="createDebtForm">
                    @csrf
                    <input type="hidden" name="discount_id" id="debt_discount_id_hidden">
                    <input type="hidden" name="discount_percentage" id="debt_discount_percentage_hidden">
                    <input type="hidden" name="discount_amount" id="debt_discount_amount_hidden">
                    <input type="hidden" name="final_amount" id="debt_final_amount_hidden">
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Ingrese Datos de la deuda</h3>
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
                                            <label for="customer_id" class="form-label">Seleccionar Cliente(*)</label>
                                            <select class="form-control select2" name="customer_id" id="customer_id" required>
                                                <option value="">Selecciona un cliente</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}">
                                                        {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="water_connection_id" class="form-label">Seleccionar Toma(*)</label>
                                            <select class="form-control select2" name="water_connection_id" id="water_connection_id" required>
                                                <option value="">Selecciona una toma</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="start_date" class="form-label">Fecha de Inicio(*)</label>
                                            <input type="month" class="form-control" name="start_date" value="{{ old('start_date') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="end_date" class="form-label">Fecha de Fin(*)</label>
                                            <input type="month" class="form-control" name="end_date" value="{{ old('end_date') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="amount" class="form-label">Monto(*)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="amount" id="debt_amount" placeholder="Ingresa el monto" value="{{ old('amount') }}" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="card border-success">
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="hidden" name="has_discount" id="debt_has_discount_hidden" value="0">
                                                        <input type="checkbox" class="custom-control-input" id="debt_has_discount" name="has_discount" value="1">
                                                        <label class="custom-control-label font-weight-bold text-success" for="debt_has_discount">
                                                            Aplicar descuento a esta deuda
                                                        </label>
                                                    </div>
                                                </div>
                                                <div id="debtDiscountContainer" style="display:none;">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Descuento(*)</label>
                                                                <select class="form-control select2" id="debt_discount_id">
                                                                    <option value="">Seleccione un descuento</option>
                                                                    @foreach($discounts as $discount)
                                                                        <option value="{{ $discount->id }}" data-percentage="{{ $discount->percentage }}">
                                                                            {{ $discount->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Porcentaje</label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" id="debt_discount_percentage" readonly>
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Monto del Descuento</label>
                                                                <input type="text" class="form-control bg-light text-success font-weight-bold" id="debt_discount_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Monto con Descuento</label>
                                                                <input type="text" class="form-control bg-light text-success font-weight-bold" id="debt_amount_with_discount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 d-flex align-items-end">
                                                            <div class="alert alert-info w-100 mb-0">
                                                                <i class="fa fa-info-circle"></i>
                                                                El descuento se aplicará directamente al saldo de la deuda.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="debt_category_id" class="form-label">Categoría(*)</label>
                                            <select class="form-control select2" name="debt_category_id" id="debt_category_id" required>
                                                <option value="">Selecciona una categoría</option>
                                                @foreach($debtCategories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="note" class="form-label">Observación</label>
                                            <textarea class="form-control" name="note" placeholder="Ingresa una observación">{{ old('note') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="saveDebt" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</style>

<script>
    document.getElementById('createDebtForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await response.json();
            Swal.fire({
                icon: data.error ? 'error' : 'success',
                title: data.error ? 'Error' : 'Éxito',
                text: data.error || data.success,
                confirmButtonText: 'Aceptar'
            }).then(() => {
                if (data.success) window.location.href = "{{ route('debts.index') }}";
            });
            
        } catch (error) {
            console.error('Error:', error);
        }
    });
</script>
