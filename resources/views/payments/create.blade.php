<div class="modal fade" id="createPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Agregar Pago <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('payments.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Ingrese Datos del Pago</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-10"></div>
                                    <div class="col-lg-2 text-right">
                                        <div class="form-group text-right">
                                            <label for="payment_date_display" class="form-label">Fecha de Pago</label>
                                            <input type="text" class="form-control" id="payment_date_display" value="{{ date('d-m-Y') }}" readonly />
                                            <input type="hidden" name="payment_date" value="{{ date('Y-m-d') }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="customer_id" class="form-label">Seleccionar Usuario(*)</label>
                                            <select class="form-control select2" name="customer_id" id="customer_id" required>
                                                <option value="">Selecciona un usuario</option>
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
                                            <label for="debt_id" class="form-label">Seleccionar Deuda(*)</label>
                                            <select class="form-control select2" name="debt_id" id="debt_id" required>
                                                <option value="">Selecciona una deuda</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="suggested_amount" class="form-label" style="font-weight: bold; color: #555;">Monto Sugerido a Pagar</label>
                                            <div style="border-radius: 8px; background-color: #d2e0ca; padding: 10px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); display: flex; align-items: center;">
                                                <i class="fas fa-money-bill-wave" style="margin-right: 8px; color: #28a745;"></i>
                                                <p id="suggested_amount" class="form-control-static" style="margin: 0; font-size: 16px; color: #333;">Selecciona una deuda para ver el monto sugerido.</p>
                                            </div>
                                        </div>
                                    </div>                                                                       
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="amount" class="form-label">Monto a Pagar(*)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                                </div>
                                                <input type="number" class="form-control" name="amount" placeholder="Ingresa el monto" required />
                                            </div>
                                        </div>
                                    </div>                                
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="note" class="form-label">Nota</label>
                                            <textarea class="form-control" name="note" placeholder="Ingresa una nota"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="save" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .select2-container .select2-selection--single {
        height: 40px;
        display: flex;
        align-items: center;
    }
</style>
