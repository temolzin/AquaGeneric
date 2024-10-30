<div class="modal fade" id="waterConnectionPayments" tabindex="-1" role="dialog" aria-labelledby="waterConnectionPayments" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom bg-purple">
                <h5 class="modal-title" id="waterConnectionPayments">Pagos por Toma de Agua</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="waterConnectionPaymentForm" method="GET" action="{{ route('report.waterConnectionPayments') }}" target="_blank">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="customerId" class="form-label">Seleccionar Cliente(*)</label>
                        <select class="form-control select2" name="waterCustomerId" id="waterCustomerId" required>
                            <option value="">Selecciona un cliente</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="waterConnectionId" class="form-label">Seleccionar Toma(*)</label>
                        <select class="form-control select2" name="waterConnectionId" id="waterConnectionId" required>
                            <option value="">Selecciona una toma</option>
                        </select>
                        <label for="waterStartDate" class="form-label">Fecha Inicio(*)</label>
                        <input type="date" id="waterStartDate" name="waterStartDate" class="form-control"
                            required placeholder="Ingrese fecha inicio"/>
                        <label for="waterEndDate" class="form-label">Fecha Fin(*)</label>
                        <input type="date" id="waterEndDate" name="waterEndDate" class="form-control"
                            required placeholder="Ingrese fecha fin"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn bg-purple">Generar Reporte</button>
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
