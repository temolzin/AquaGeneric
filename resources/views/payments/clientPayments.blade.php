<div class="modal fade" id="clientPayments" tabindex="-1" role="dialog" aria-labelledby="clientPayments" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom bg-maroon">
                <h5 class="modal-title" id="clientPayments">Pagos por cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="clientPaymentForm" method="GET" action="{{ route('report.client-payments') }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="customerId" class="form-label">Cliente(*)</label>
                        <select class="form-control select2" name="customerId" id="customerId" required>
                            <option value="">Selecciona un cliente</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="startDate" class="form-label">Fecha inicio(*)</label>
                        <input type="date" id="startDate" name="startDate" class="form-control"
                            required placeholder="Ingrese fecha inicio"/>
                        <label for="endDate" class="form-label">Fecha fin(*)</label>
                        <input type="date" id="endDate" name="endDate" class="form-control"
                            required placeholder="Ingrese fecha fin"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn bg-maroon">Generar Reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('clientPaymentForm').onsubmit = function(event) {
        event.preventDefault();
        const form = event.target;
        const url = form.action;
        const customerId = document.getElementById('customerId').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
    
        const fullUrl = `${url}?customerId=${customerId}&startDate=${startDate}&endDate=${endDate}`;

        window.open(fullUrl, '_blank');
    };
</script>
