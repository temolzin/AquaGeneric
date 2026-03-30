<div class="modal fade" id="generateAdvancePaymentsReportModal" tabindex="-1" role="dialog"
    aria-labelledby="generateAdvancePaymentsReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom bg-teal">
                <h5 class="modal-title" id="generateAdvancePaymentsReportModalLabel">
                    Generar Reporte de Pagos Adelantados
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="advancePaymentsReportForm" method="GET" 
                action="{{ route('advancePayments.report') }}" target="_blank">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="advancePaymentsCustomerSelect" class="form-label">
                            Seleccionar Cliente(*)
                        </label>
                        <select class="form-control select2" name="customer_id" 
                            id="advancePaymentsCustomerSelect" required>
                            <option value="">Selecciona un cliente</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                </option>
                            @endforeach
                        </select>

                        <label for="advancePaymentsWaterConnectionSelect" class="form-label">
                            Seleccionar Toma(*)
                        </label>
                        <select class="form-control select2" name="water_connection_id"
                            id="advancePaymentsWaterConnectionSelect" required>
                            <option value="">Selecciona una toma</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="submit" class="btn bg-teal">
                        Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('js')
<script>
    $(function () {
        $('#generateAdvancePaymentsReportModal').on('shown.bs.modal', function () {
            $.ajax({
                url: '{{ route("getCustomersWithAdvancePayments") }}',
                method: 'GET',
                success: function (response) {
                    const customerSelect = $('#advancePaymentsCustomerSelect');
                    customerSelect.empty().append('<option value="">Selecciona un cliente</option>');
                    
                    $.each(response.customers, function (index, customer) {
                        customerSelect.append(
                            `<option value="${customer.id}">${customer.name} ${customer.last_name}</option>`
                        );
                    });
                },
                error: function (xhr) {
                    console.error('Error al cargar clientes con pagos adelantados', xhr.responseText);
                }
            });
        });

        $('#advancePaymentsCustomerSelect').on('change', function () {
            const customerId = $(this).val();
            const waterConnectionSelect = $('#advancePaymentsWaterConnectionSelect');
            waterConnectionSelect.empty().append('<option value="">Selecciona una toma</option>');

            if (customerId) {
                $.ajax({
                    url: '{{ route("getCustomersWithAdvancePayments") }}',
                    method: 'GET',
                    data: { customerId: customerId },
                    success: function (response) {
                        $.each(response.waterConnections, function (index, connection) {
                            waterConnectionSelect.append(
                                `<option value="${connection.id}">${connection.id} - ${connection.name}</option>`
                            );
                        });
                    },
                    error: function (xhr) {
                        console.error('Error al cargar tomas de agua con pagos adelantados', xhr.responseText);
                    }
                });
            }
        });
    });
</script>
@endpush
