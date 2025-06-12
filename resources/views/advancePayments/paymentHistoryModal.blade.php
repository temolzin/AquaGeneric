<div class="modal fade" id="paymentHistoryModal" tabindex="-1" role="dialog" aria-labelledby="advancePaymentsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Pagos Anticipados</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-header py-2 bg-secondary">
                            <h3 class="card-title">Periodo de Anticipación</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="customerId">Seleccionar Cliente(*)</label>
                                        <select class="form-control select2" name="selectCustomerId" id="selectCustomerId" required>
                                            <option value="">Selecciona un cliente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="waterConnectionId">Seleccionar Toma(*)</label>
                                        <select class="form-control select2" name="selectWaterConnectionId" id="selectWaterConnectionId" required>
                                            <option value="">Selecciona una toma</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6 text-center">
                                    <div class="bg-light p-2 rounded">
                                        <strong>Periodo Pagado Anticipado</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-light p-2 rounded text-right">
                                        <p class="mb-1"><strong>Fecha de Inicio:</strong> <span class="labelStarDate"></span></p>
                                        <p class="mb-0"><strong>Fecha de Fin:</strong> <span class="labelEndDate"></span></p>
                                        <a href="#" class="btn btn-success btn-sm mt-2 shadow-sm" id="btn-download-pdf">
                                            <i class="fas fa-file-download mr-1"></i> Descargar PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
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

    #calendarContainer {
        width: 100%;
        margin: 0 auto; 
        padding: 10px;
    }

    .fc-multimonth {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px; 
    }

    .fc-multimonth-month {
        flex: 0 0 200px;
        margin: 0 !important;
    }

    .fc-header-toolbar {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }
</style>

@section('js')
<script>
    $(document).ready(function () {
    const modal = $('#paymentHistoryModal');
    const customerSelect = $('#selectCustomerId');
    const connectionSelect = $('#selectWaterConnectionId');
    const startDateLabel = $('.labelStarDate');
    const endDateLabel = $('.labelEndDate');

    let calendar;

    function formatDate(isoDate) {
        if (!isoDate) return 'No disponible';

        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const date = new Date(isoDate + 'T12:00:00');
        return new Intl.DateTimeFormat('es-ES', options).format(date);
    }

    function clearCalendar() {
        if (!calendar) return;
        calendar.getEvents().forEach(event => event.remove());
    }

    function loadAdvancePaymentEvents(customerId, connectionId) {
        if (!customerId || !connectionId) {
            clearCalendar();
            return;
        }

        $.ajax({
            url: '{{ route("getAdvanceDebtDates") }}',
            type: 'GET',
            data: {
                customerId: customerId,
                waterConnectionId: connectionId
            },
            success: function (response) {
                startDateLabel.text(formatDate(response.start_date));
                endDateLabel.text(formatDate(response.end_date));

                clearCalendar();

                if (!response.start_date || !response.end_date) return;

                const adjustedEndDate = new Date(response.end_date);
                adjustedEndDate.setDate(adjustedEndDate.getDate() + 1);
                const isoAdjustedEndDate = adjustedEndDate.toISOString().split('T')[0];

                calendar.addEvent({
                    start: response.start_date,
                    end: isoAdjustedEndDate,
                    display: 'background',
                    color: '#16CB2B'
                });

                calendar.gotoDate(response.start_date);
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron obtener las fechas del pago anticipado.',
                    confirmButtonText: 'Aceptar'
                });
                clearCalendar();
            }
        });
    }

    modal.on('shown.bs.modal', function () {
        $('.select2').select2({
            dropdownParent: modal
        });

        if (!calendar) {
            calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'multiMonthSixMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'multiMonthSixMonth,dayGridMonth'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    multiMonthSixMonth: '6 Meses'
                },
                views: {
                    multiMonthSixMonth: {
                        type: 'multiMonth',
                        duration: { months: 6 },
                        multiMonthMaxColumns: 3,
                        titleFormat: { month: 'short', year: 'numeric' }
                    }
                },
                events: []
            });

            calendar.render();
        }

        $.ajax({
            url: '{{ route("getCustomersWithAdvancePayments") }}',
            type: 'GET',
            success: function (response) {
                customerSelect.empty().append('<option value="">Selecciona un cliente</option>');
                $.each(response.customers, function (index, customer) {
                    customerSelect.append(
                        `<option value="${customer.id}">${customer.name} ${customer.last_name}</option>`
                    );
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los clientes.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    modal.on('hidden.bs.modal', function () {
        customerSelect.val(null).trigger('change');
        connectionSelect.empty().append('<option value="">Selecciona una toma</option>');
        startDateLabel.text('');
        endDateLabel.text('');

        if (calendar) {
            calendar.destroy();
            calendar = null;
        }
    });

    customerSelect.on('change', function () {
        const customerId = $(this).val();

        if (customerId) {
            $.ajax({
                url: '{{ route("getCustomersWithAdvancePayments") }}',
                type: 'GET',
                data: { customerId: customerId },
                success: function (response) {
                    connectionSelect.empty().append('<option value="">Selecciona una toma</option>');
                    $.each(response.waterConnections, function (index, connection) {
                        connectionSelect.append(
                            `<option value="${connection.id}">${connection.id} - ${connection.name}</option>`
                        );
                    });
                    connectionSelect.trigger('change');
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar las tomas de agua.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        } 
    });

    connectionSelect.on('change', function () {
        const customerId = customerSelect.val();
        const connectionId = $(this).val();

        if (customerId && connectionId) {
            loadAdvancePaymentEvents(customerId, connectionId);
        } 
    });
});
</script>
@endsection
