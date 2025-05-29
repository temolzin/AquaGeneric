<div class="modal fade" id="advancedPayments" tabindex="-1" role="dialog" aria-labelledby="advancedPayments"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom bg-teal">
                <h5 class="modal-title" id="advancedPayments">Pagos Adelantados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="advancedPaymentForm" method="GET" action="{{ route('advancePayments.report') }}"target="_blank">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="customerId" class="form-label">Seleccionar Cliente(*)</label>
                        <select class="form-control select2" name="customer_id" id="advancedCustomerId" required>
                            <option value="">Selecciona un cliente</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="waterConnectionId" class="form-label">Seleccionar Toma(*)</label>
                        <select class="form-control select2" name="water_connection_id" id="advancedWaterConnectionId"
                            required>
                            <option value="">Selecciona una toma</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn bg-teal">Generar Reporte</button>
                </div>
            </form>
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
