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

@section('js')
<script>
$(document).ready(function() {
    $('#createPayment').on('shown.bs.modal', function() {
        $('.select2').select2({
            dropdownParent: $('#createPayment')
        });
    });

    $('#waterCustomerId').on('change', function() {
        const customerId = $(this).val();
        const waterConnectionSelect = $('#waterConnectionId');

        waterConnectionSelect.empty().append('<option value="">Selecciona una toma</option>');

        if (customerId) {
            $.ajax({
                url: '{{ route("getWaterConnectionsByCustomer") }}',
                method: 'GET',
                data: { waterCustomerId: customerId },
                success: function(response) {
                    $.each(response.waterConnections, function(index, connection) {
                        const connectionId = connection.id;
                        const connectionName = connection.name;

                        if (connectionId && connectionName) {
                            waterConnectionSelect.append(`<option value="${connectionId}">${connectionId} - ${connectionName}</option>`);
                        }
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error', textStatus, errorThrown);
                }
            });
        }
    });

    $('#water_connection_id').change(function() {
        var waterConnectionId = $(this).val();
        if (waterConnectionId) {
            $.ajax({
                url: '{{ route("getDebtsByWaterConnection") }}',
                type: 'GET',
                data: { water_connection_id: waterConnectionId },
                success: function(response) {
                    $('#debt_id').empty();
                    $('#debt_id').append('<option value="">Selecciona una deuda</option>');
                    $.each(response.debts, function(index, debt) {
                        $('#debt_id').append('<option value="' + debt.id + '" data-remaining-amount="' + debt.remaining_amount + '">' + debt.start_date + ' - ' + debt.end_date + ' | Monto: ' + debt.amount + '</option>');
                    });
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                }
            });
        } else {
            $('#debt_id').empty().append('<option value="">Selecciona una deuda</option>');
        }
    });

    $('#debt_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var remainingAmount = selectedOption.data('remaining-amount');

        if (remainingAmount !== undefined) {
            var roundedAmount = parseFloat(remainingAmount).toFixed(2);
            $('#suggested_amount').text('Saldo pendiente: $' + roundedAmount);
        } else {
            $('#suggested_amount').text('');
        }
    });

    $('#payments').DataTable({
        responsive: true,
        paging: false,
        info: false,
        searching: false
    });

    var successMessage = "{{ session('success') }}";
    var errorMessage = "{{ session('error') }}";

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: successMessage,
            confirmButtonText: 'Aceptar'
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            confirmButtonText: 'Aceptar'
        });
    }

    $('#formSearch').on('submit', function(e) {
        var period = $('#searchPeriod').val();
        var regex = /^(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre)\/\d{4}$/i;

        if (period && !regex.test(period)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Formato inválido',
                text: 'El formato debe ser "mes/año".'
            });
        }
    });
});

$('#clientPayments').on('shown.bs.modal', function(){
    $('.select2').select2({
        dropdownParent: $('#clientPayments')
    });
});

$('#waterConnectionPayments').on('shown.bs.modal', function(){
    $('.select2').select2({
        dropdownParent: $('#waterConnectionPayments')
    });
});

$('#customer_id').on('change', function() {
    var customerId = $(this).val();

    if (customerId) {
        $.ajax({
            url: "{{ route('getWaterConnections') }}",
            type: "GET",
            data: { customer_id: customerId },
            success: function(response) {
                var waterConnectionSelect = $('#water_connection_id');
                waterConnectionSelect.empty();
                waterConnectionSelect.append('<option value="">Selecciona una toma</option>');

                $.each(response.waterConnections, function(index, waterConnection) {
                    waterConnectionSelect.append(
                        '<option value="' + waterConnection.id + '">' + waterConnection.id + ' - ' + waterConnection.name + '</option>'
                    );
                });
                waterConnectionSelect.trigger('change');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar las tomas de agua para el cliente seleccionado.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    } else {
        $('#water_connection_id').empty().append('<option value="">Selecciona una toma</option>');
    }
});
</script>
@endsection
