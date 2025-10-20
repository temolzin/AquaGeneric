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
                <form action="{{ route('payments.store') }}" method="post" enctype="multipart/form-data" id="paymentForm">
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
                                            <label for="customer_id" class="form-label">Seleccionar Cliente(*)</label>
                                            <select class="form-control select2" name="customer_id" id="customer_id" required>
                                                <option value="">Selecciona un cliente</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}">
                                                        {{ $customer->id }} - {{ $customer->user->name }} {{ $customer->user->last_name }}
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
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="debt_id" class="form-label">Seleccionar Deuda(*)</label>
                                            <select class="form-control select2" name="debt_id" id="debt_id" required>
                                                <option value="">Selecciona una deuda</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="suggested_amount" class="form-label" style="font-weight: bold; color: #555;">Saldo Pendiente</label>
                                            <div style="border-radius: 8px; background-color: #e49c9c; padding: 10px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); display: flex; align-items: center;">
                                                <i class="fas fa-money-bill-wave" style="margin-right: 8px; color: #a72828;"></i>
                                                <p id="suggested_amount" class="form-control-static" style="margin: 0; font-size: 16px; color: #333;">Selecciona una deuda para ver el saldo pendiente.</p>
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
                                                <input type="number" min="1" class="form-control" name="amount" placeholder="Ingresa el monto" value="{{ old('amount') }}" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="method" class="form-label">Método de Pago(*)</label>
                                            <select id="method" class="form-control select2" name="method" required>
                                                <option value="">Selecciona un método de pago</option>
                                                <option value="cash">Efectivo</option>
                                                <option value="card">Tarjeta</option>
                                                <option value="transfer">Transferencia</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <input type="checkbox" id="is_future_payment" name="is_future_payment" value="1">
                                            <label for="is_future_payment">¿El cliente va a pagar por adelantado? (solo para periodos futuros)</label>
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

<script>
$(document).ready(function() {
    $('#createPayment').on('shown.bs.modal', function () {
        $(this).find('.select2').select2('destroy');

        $(this).find('.select2').select2({
            dropdownParent: $('#createPayment')
        });
    });

    $('#customer_id').on('change', function() {
        var customerId = $(this).val();
        if (customerId) {
            $.ajax({
                url: '{{ route("getWaterConnectionsByCustomer") }}',
                data: { waterCustomerId: customerId },
                success: function(data) {
                    var waterConnectionSelect = $('#water_connection_id');
                    waterConnectionSelect.empty().append('<option value="">Selecciona una toma</option>');
                    $.each(data.waterConnections, function(index, connection) {
                        waterConnectionSelect.append('<option value="' + connection.id + '">' + 
                            connection.name + '</option>');
                    });
                    $('#debt_id').empty().append('<option value="">Selecciona una deuda</option>');
                    $('#suggested_amount').text('Selecciona una deuda para ver el saldo pendiente.');
                    $('#is_future_payment').prop('checked', false);
                },
                error: function(xhr) {
                    console.error('Error al cargar tomas:', xhr.responseText);
                    alert('Error al cargar las tomas. Por favor, intenta de nuevo.');
                }
            });
        } else {
            $('#water_connection_id').empty().append('<option value="">Selecciona una toma</option>');
            $('#debt_id').empty().append('<option value="">Selecciona una deuda</option>');
            $('#suggested_amount').text('Selecciona una deuda para ver el saldo pendiente.');
            $('#is_future_payment').prop('checked', false);
        }
    });

    $('#water_connection_id').on('change', function() {
        var waterConnectionId = $(this).val();
        if (waterConnectionId) {
            $.ajax({
                url: '{{ route("getDebtsByWaterConnection") }}',
                data: { water_connection_id: waterConnectionId },
                success: function(data) {
                    var debtSelect = $('#debt_id');
                    debtSelect.empty().append('<option value="">Selecciona una deuda</option>');
                    $.each(data.debts, function(index, debt) {
                        debtSelect.append('<option value="' + debt.id + '">' + 
                            debt.start_date + ' - $' + debt.remaining_amount + '</option>');
                    });
                    $('#suggested_amount').text('Selecciona una deuda para ver el saldo pendiente.');
                    $('#is_future_payment').prop('checked', false);
                },
                error: function(xhr) {
                    console.error('Error al cargar deudas:', xhr.responseText);
                    alert('Error al cargar las deudas. Por favor, intenta de nuevo.');
                }
            });
        } else {
            $('#debt_id').empty().append('<option value="">Selecciona una deuda</option>');
            $('#suggested_amount').text('Selecciona una deuda para ver el saldo pendiente.');
            $('#is_future_payment').prop('checked', false);
        }
    });

    $('#debt_id').on('change', function() {
        var debtId = $(this).val();
        if (debtId) {
            $.ajax({
                url: '{{ route("getDebtsByWaterConnection") }}',
                data: { water_connection_id: $('#water_connection_id').val() },
                success: function(data) {
                    var debt = data.debts.find(d => d.id == debtId);
                    if (debt) {
                        $('#suggested_amount').text('$' + debt.remaining_amount);
                        $('#debt_start_date').remove();
                        $('<input>').attr({type: 'hidden', id: 'debt_start_date', value: debt.start_date}).appendTo('#paymentForm');

                        var today = new Date('{{ date("Y-m-d") }}');
                        var debtDate = new Date(debt.start_date);
                        var isFutureDebt = debtDate.getFullYear() > today.getFullYear() || 
                            (debtDate.getFullYear() === today.getFullYear() && debtDate.getMonth() + 1 > today.getMonth() + 1);

                        $('#is_future_payment').prop('checked', isFutureDebt);
                    }
                },
                error: function(xhr) {
                    console.error('Error al cargar detalles de deuda:', xhr.responseText);
                    alert('Error al cargar los detalles de la deuda. Por favor, intenta de nuevo.');
                }
            });
        } else {
            $('#is_future_payment').prop('checked', false);
        }
    });

    $('#paymentForm').on('submit', function(e) {
        var isFuturePayment = $('#is_future_payment').is(':checked');
        var startDate = $('#debt_start_date').val();
        if (startDate) {
            var today = new Date('{{ date("Y-m-d") }}');
            var debtDate = new Date(startDate);
            var debtMonthYear = debtDate.getFullYear() * 100 + debtDate.getMonth() + 1;
            var todayMonthYear = today.getFullYear() * 100 + today.getMonth() + 1;

            if (debtMonthYear > todayMonthYear && !isFuturePayment) {
                e.preventDefault();
                alert('Error: La deuda seleccionada es de un periodo futuro. Debe marcar "¿El cliente va a pagar por adelantado?".');
                return false;
            }

            if (isFuturePayment && debtMonthYear <= todayMonthYear) {
                e.preventDefault();
                alert('Error: La deuda seleccionada no es de un periodo futuro.');
                return false;
            }
        }
    });
});
</script>
