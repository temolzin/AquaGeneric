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
                    <input type="hidden" name="discount_percentage" id="payment_discount_percentage_hidden">
                    <input type="hidden" name="discount_amount" id="payment_discount_amount_hidden">
                    <input type="hidden" name="final_amount" id="payment_final_amount_hidden">
                    <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
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
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="is_future_payment" name="is_future_payment" value="1">
                                            <label class="custom-control-label" for="is_future_payment">¿El cliente va a pagar por adelantado? (solo para periodos futuros)</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="note" class="form-label">Nota</label>
                                            <textarea class="form-control" name="note" placeholder="Ingresa una nota"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="payment_has_discount" name="has_discount">
                                                    <label class="custom-control-label font-weight-bold text-success" for="payment_has_discount">
                                                        Aplicar descuento a esta deuda
                                                    </label>
                                                </div>
                                            </div>
                                            <div id="paymentDiscountContainer" style="display:none;">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Descuento(*)</label>
                                                            <select class="form-control select2" name="discount_id" id="payment_discount_id" disabled>
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
                                                                <input type="text" class="form-control" id="payment_discount_percentage" readonly>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Monto del Descuento</label>
                                                            <input type="text" class="form-control bg-light text-success font-weight-bold" id="payment_discount_amount" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Monto con Descuento</label>
                                                            <input type="text" class="form-control bg-light text-success font-weight-bold" id="payment_amount_with_discount" readonly>
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

@push('js')
<script>
$(document).ready(function () {

    function limpiarDescuento() {
        $('#payment_discount_id').val('').trigger('change');
        $('#payment_discount_id').prop('disabled', true);

        $('#payment_discount_percentage').val('');
        $('#payment_discount_amount').val('');
        $('#payment_amount_with_discount').val('');

        $('#payment_discount_percentage_hidden').val('');
        $('#payment_discount_amount_hidden').val('');
        $('#payment_final_amount_hidden').val('');

        $('#paymentDiscountContainer').hide();
        $('#payment_has_discount').prop('checked', false);
    }

    $('#createPayment').on('shown.bs.modal', function () {

        $('.select2').each(function () {

            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }

            $(this).select2({
                dropdownParent: $('#createPayment'),
                width: '100%'
            });

        });

        limpiarDescuento();

        $('#suggested_amount').text('Selecciona una deuda para ver el saldo pendiente.');

        $('#payment_amount').val('');

        $('#debt_start_date').remove();
        $('#debt_remaining_amount').remove();

    });

    $('#payment_has_discount').on('change', function () {

        if ($(this).is(':checked')) {

            $('#paymentDiscountContainer').slideDown();
            $('#payment_discount_id').prop('disabled', false);

            return;
        }

        limpiarDescuento();

    });

    $('#customer_id').on('change', function () {

        let customerId = $(this).val();

        $('#water_connection_id').empty()
            .append('<option value="">Selecciona una toma</option>')
            .trigger('change');

        $('#debt_id').empty()
            .append('<option value="">Selecciona una deuda</option>')
            .trigger('change');

        $('#suggested_amount').text('Selecciona una deuda para ver el saldo pendiente.');

        if (!customerId) {
            return;
        }

        $.ajax({

            url: '{{ route("getWaterConnectionsByCustomer") }}',

            data: {
                waterCustomerId: customerId
            },

            success: function (data) {

                $.each(data.waterConnections, function (i, connection) {

                    $('#water_connection_id').append(
                        '<option value="' + connection.id + '">' +
                        connection.name +
                        '</option>'
                    );

                });

                $('#water_connection_id').trigger('change');

            },

            error: function () {

                alert('Error al cargar las tomas.');

            }

        });

    });

    $('#water_connection_id').on('change', function () {

        let waterConnectionId = $(this).val();

        $('#debt_id').empty()
            .append('<option value="">Selecciona una deuda</option>')
            .trigger('change');

        $('#suggested_amount').text('Selecciona una deuda para ver el saldo pendiente.');

        limpiarDescuento();

        if (!waterConnectionId) {
            return;
        }

        $.ajax({

            url: '{{ route("getDebtsByWaterConnection") }}',

            data: {
                water_connection_id: waterConnectionId
            },

            success: function (data) {

                $.each(data.debts, function (i, debt) {

                    $('#debt_id').append(
                        '<option value="' + debt.id + '">' +
                        debt.start_date +
                        ' - $' +
                        debt.remaining_amount +
                        '</option>'
                    );

                });

                $('#debt_id').trigger('change');

            },

            error: function () {

                alert('Error al cargar las deudas.');

            }

        });

    });
        function calcularDescuento() {

        if (!$('#payment_has_discount').is(':checked')) {
            return;
        }

        let deuda = parseFloat($('#debt_remaining_amount').val()) || 0;

        let porcentaje = parseFloat(
            $('#payment_discount_id option:selected').data('percentage')
        ) || 0;

        let descuento = deuda * porcentaje / 100;
        let total = deuda - descuento;

        $('#payment_discount_percentage').val(porcentaje.toFixed(2));
        $('#payment_discount_amount').val('$' + descuento.toFixed(2));
        $('#payment_amount_with_discount').val('$' + total.toFixed(2));

        $('#payment_discount_percentage_hidden').val(porcentaje);
        $('#payment_discount_amount_hidden').val(descuento.toFixed(2));
        $('#payment_final_amount_hidden').val(total.toFixed(2));

        $('#payment_amount').val(total.toFixed(2));

    }

    $('#payment_discount_id').on('change', function () {
        calcularDescuento();
    });

    $('#debt_id').on('change', function () {

        let debtId = $(this).val();

        if (!debtId) {
            return;
        }

        $.ajax({

            url: '{{ route("getDebtsByWaterConnection") }}',

            data: {
                water_connection_id: $('#water_connection_id').val()
            },

            success: function (data) {

                let debt = data.debts.find(function (item) {
                    return item.id == debtId;
                });

                if (!debt) {
                    return;
                }

                $('#suggested_amount').text('$' + debt.remaining_amount);

                $('#payment_amount').val(debt.remaining_amount);

                $('#debt_start_date').remove();
                $('#debt_remaining_amount').remove();

                $('<input>')
                    .attr({type: 'hidden', id: 'debt_start_date', value: debt.start_date}) .appendTo('#paymentForm');

                $('<input>')
                    .attr({type: 'hidden', id: 'debt_remaining_amount', value: debt.remaining_amount})
                    .appendTo('#paymentForm');

                let today = new Date('{{ date("Y-m-d") }}');
                let debtDate = new Date(debt.start_date);
                let isFuture = debtDate.getFullYear() > today.getFullYear() || (debtDate.getFullYear() == today.getFullYear() && debtDate.getMonth() > today.getMonth());
                $('#is_future_payment').prop('checked', isFuture);
                if ($('#payment_has_discount').is(':checked')) {
                    calcularDescuento();
                }
            },
            error: function () {
                alert('Error al obtener la deuda.');
            }
        });
    });

    $('#payment_amount').on('keyup change', function () {
        if ($('#payment_has_discount').is(':checked')) {
            calcularDescuento();
        }
    });

    $('#paymentForm').on('submit', function (e) {
        let startDate = $('#debt_start_date').val();
        if (!startDate) {
            return;
        }

        let today = new Date('{{ date("Y-m-d") }}');
        let debtDate = new Date(startDate);

        let deudaPeriodo = debtDate.getFullYear() * 100 + debtDate.getMonth();
        let hoyPeriodo = today.getFullYear() * 100 + today.getMonth();
        let pagoFuturo = $('#is_future_payment').is(':checked');

        if (deudaPeriodo > hoyPeriodo && !pagoFuturo) {
            e.preventDefault();
            alert(
                'La deuda pertenece a un periodo futuro. Debe marcar "¿El cliente va a pagar por adelantado?".'
            );
            return;
        }
        if (deudaPeriodo <= hoyPeriodo && pagoFuturo) {
            e.preventDefault();
            alert(
                'La deuda seleccionada no corresponde a un periodo futuro.'
            );
            return;
        }
    });
});
</script>
@endpush
