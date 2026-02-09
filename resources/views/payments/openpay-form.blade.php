@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Pago con Tarjeta')

@section('content_header')
<h1>
    <i class="fas fa-credit-card"></i>
    Pago con Tarjeta
</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Información de la deuda -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Información del Pago
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong><i class="fas fa-user"></i> Cliente:</strong>
                                {{ $customer->name }} {{ $customer->last_name }}
                            </p>
                            @if(isset($debt->waterConnection))
                                <p class="mb-1">
                                    <strong><i class="fas fa-tint"></i> Toma:</strong>
                                    {{ $debt->waterConnection->name ?? 'Toma #' . $debt->water_connection_id }}
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong><i class="far fa-calendar-alt"></i> Periodo:</strong>
                                {{ \Carbon\Carbon::parse($debt->start_date)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($debt->end_date)->format('d/m/Y') }}
                            </p>
                            <p class="mb-1">
                                <strong><i class="fas fa-money-bill-wave"></i> Monto Total de la Deuda:</strong>
                                <span class="badge badge-info badge-lg">${{ number_format($debt->amount, 2) }}</span>
                            </p>
                            @if($totalPaid > 0)
                            <p class="mb-1">
                                <strong><i class="fas fa-check-circle text-success"></i> Ya Pagado:</strong>
                                <span class="badge badge-success badge-lg">${{ number_format($totalPaid, 2) }}</span>
                            </p>
                            @endif
                            <p class="mb-0">
                                <strong><i class="fas fa-exclamation-triangle"></i> Pendiente por Pagar:</strong>
                                <span class="badge badge-warning badge-lg">${{ number_format($remainingAmount, 2) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Pago -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Tarjeta de crédito o débito</h3>
                </div>
                <div class="card-body">
                    <!-- Tarjetas aceptadas -->
                    <div class="card-expl mb-4">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h6 class="mb-2">Tarjetas de crédito</h6>
                                <img src="{{ asset('img/cards1.png') }}" alt="Tarjetas de crédito" class="img-fluid"
                                    style="max-height: 50px;">
                            </div>
                            <div class="col-md-8 text-center">
                                <h6 class="mb-2">Tarjetas de débito</h6>
                                <img src="{{ asset('img/cards2.png') }}" alt="Tarjetas de débito" class="img-fluid"
                                    style="max-height: 50px;">
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form id="payment-form" data-openpay="true">
                        @csrf
                        <input type="hidden" name="debt_id" value="{{ $debt->id }}">
                        <input type="hidden" id="token_id" name="token_id">
                        <input type="hidden" id="device_session_id" name="device_session_id">

                        <div class="row">
                            <!-- Nombre del titular -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="holder_name">Nombre del titular</label>
                                    <input type="text" class="form-control" id="holder_name"
                                        placeholder="Como aparece en la tarjeta" autocomplete="off"
                                        data-openpay-card="holder_name" required>
                                </div>
                            </div>

                            <!-- Número de tarjeta -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="card_number">Número de tarjeta</label>
                                    <input type="text" class="form-control" id="card_number"
                                        placeholder="•••• •••• •••• ••••" autocomplete="off"
                                        data-openpay-card="card_number" maxlength="16" required>
                                    <div id="card-brand" class="mt-1"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Fecha de expiración -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="expiration_month">Mes</label>
                                    <input type="text" class="form-control" id="expiration_month" placeholder="MM"
                                        maxlength="2" data-openpay-card="expiration_month" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="expiration_year">Año</label>
                                    <input type="text" class="form-control" id="expiration_year" placeholder="AA"
                                        maxlength="2" data-openpay-card="expiration_year" required>
                                </div>
                            </div>

                            <!-- CVV -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cvv2">
                                        Código de seguridad
                                        <i class="fas fa-question-circle" title="CVV"></i>
                                    </label>
                                    <input type="text" class="form-control" id="cvv2" placeholder="•••" maxlength="4"
                                        autocomplete="off" data-openpay-card="cvv2" required>
                                    <small class="text-muted">3 o 4 dígitos</small>
                                </div>
                            </div>

                            <!-- Monto -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="amount">Monto a pagar</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="amount" name="amount" step="0.01"
                                            min="0.01" max="{{ $remainingAmount }}" value="{{ $remainingAmount }}"
                                            required>
                                    </div>
                                    <small class="text-muted">Máximo pendiente:
                                        ${{ number_format($remainingAmount, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="note">Nota (opcional)</label>
                                    <textarea class="form-control" id="note" name="note" rows="2"
                                        placeholder="Agrega un comentario o referencia"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Mensajes -->
                        <div id="error-message" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span id="error-text"></span>
                        </div>
                        <div id="success-message" class="alert alert-success" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            <span id="success-text"></span>
                        </div>

                        <!-- OpenPay Logo y Seguridad -->
                        <div class="openpay-info mt-3 mb-3">
                            <div class="row">
                                <div class="col-md-6 text-center text-md-left">
                                    <p class="mb-1"><small>Transacciones realizadas vía:</small></p>
                                    <img src="{{ asset('img/openpay.png') }}" alt="OpenPay" style="max-height: 40px;">
                                </div>
                                <div class="col-md-6 text-center text-md-right">
                                    <img src="{{ asset('img/security.png') }}" alt="Seguridad"
                                        style="max-height: 40px; margin-right: 10px;">
                                    <p class="mb-0"><small>Tus pagos se realizan de forma segura con encriptación de 256
                                            bits</small></p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('viewCustomerDebts.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i>
                                    Cancelar
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" id="pay-button" class="btn btn-success btn-lg btn-block">
                                    <span id="button-text">
                                        <i class="fas fa-lock"></i>
                                        Pagar $<span
                                            id="amount-display">{{ number_format($remainingAmount, 2) }}</span>
                                    </span>
                                    <span id="button-loading" style="display: none;">
                                        <span class="spinner-border spinner-border-sm"></span>
                                        Procesando...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tarjetas de prueba (modo sandbox) -->
            @if($sandbox)
                <div class="card card-warning collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-flask"></i>
                            Modo de Prueba - Tarjetas de Prueba
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Número</th>
                                    <th>CVV</th>
                                    <th>Fecha</th>
                                    <th>Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-success">
                                    <td><code>4111111111111111</code></td>
                                    <td>123</td>
                                    <td>12/25</td>
                                    <td><span class="badge badge-success">Aprobada</span></td>
                                </tr>
                                <tr class="table-danger">
                                    <td><code>4000000000000002</code></td>
                                    <td>123</td>
                                    <td>12/25</td>
                                    <td><span class="badge badge-danger">Rechazada</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .badge-lg {
        font-size: 1rem;
        padding: 0.5rem 0.75rem;
    }

    #card-brand {
        min-height: 20px;
        font-weight: bold;
        color: #007bff;
        font-size: 0.9rem;
    }

    .openpay-info {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .card-expl img {
        max-width: 100%;
        height: auto;
    }
</style>
@stop

@section('js')
<!-- Scripts de OpenPay -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="https://openpay.s3.amazonaws.com/openpay.v1.min.js"></script>
<script type="text/javascript" src="https://openpay.s3.amazonaws.com/openpay-data.v1.min.js"></script>

<script>
    $(document).ready(function () {
        console.log('Inicializando OpenPay...');

        // Configuración de OpenPay
        OpenPay.setId('{{ $merchantId }}');
        OpenPay.setApiKey('{{ $publicKey }}');
        OpenPay.setSandboxMode({{ $sandbox ? 'true' : 'false' }});

        // Generar Device Session ID
        var deviceSessionId = OpenPay.deviceData.setup("payment-form", "deviceIdHiddenFieldName");

        console.log('OpenPay configurado:', {
            merchantId: '{{ $merchantId }}',
            sandbox: {{ $sandbox ? 'true' : 'false' }},
            deviceSessionId: deviceSessionId
        });

        // Actualizar monto en el botón
        $('#amount').on('input', function () {
            var amount = parseFloat($(this).val()) || 0;
            $('#amount-display').text(amount.toFixed(2));
        });

        // Detectar tipo de tarjeta
        $('#card_number').on('input', function () {
            var cardNumber = $(this).val().replace(/\s/g, '');
            if (cardNumber.length >= 6) {
                var cardType = OpenPay.card.cardType(cardNumber);
                var cardBrand = '';

                switch (cardType) {
                    case 'visa':
                        cardBrand = '<i class="fab fa-cc-visa fa-2x text-primary"></i> Visa';
                        break;
                    case 'mastercard':
                        cardBrand = '<i class="fab fa-cc-mastercard fa-2x text-warning"></i> MasterCard';
                        break;
                    case 'american_express':
                        cardBrand = '<i class="fab fa-cc-amex fa-2x text-info"></i> American Express';
                        break;
                }

                $('#card-brand').html(cardBrand);
            } else {
                $('#card-brand').html('');
            }
        });

        // Manejar envío del formulario
        $('#pay-button').on('click', function (event) {
            event.preventDefault();

            var payButton = $('#pay-button');
            var buttonText = $('#button-text');
            var buttonLoading = $('#button-loading');
            var errorDiv = $('#error-message');
            var errorText = $('#error-text');
            var successDiv = $('#success-message');
            var successText = $('#success-text');

            // Deshabilitar botón y mostrar loading
            payButton.prop('disabled', true);
            buttonText.hide();
            buttonLoading.show();
            errorDiv.hide();
            successDiv.hide();

            // Validar tarjeta
            var cardNumber = $('#card_number').val().replace(/\s/g, '');
            var cvv = $('#cvv2').val();
            var month = $('#expiration_month').val();
            var year = $('#expiration_year').val();

            if (!OpenPay.card.validateCardNumber(cardNumber)) {
                showError('Número de tarjeta inválido');
                return;
            }

            if (!OpenPay.card.validateCVC(cvv)) {
                showError('CVV inválido');
                return;
            }

            if (!OpenPay.card.validateExpiry(month, year)) {
                showError('Fecha de expiración inválida');
                return;
            }

            console.log('Creando token con OpenPay...');

            // Crear token con OpenPay
            OpenPay.token.extractFormAndCreate(
                'payment-form',
                function (response) {
                    // Token creado exitosamente
                    console.log('Token creado:', response.data.id);
                    $('#token_id').val(response.data.id);
                    
                    // Asegurar que el device_session_id esté correctamente asignado
                    var currentDeviceSessionId = deviceSessionId || $('input[name="deviceIdHiddenFieldName"]').val();
                    $('#device_session_id').val(currentDeviceSessionId);
                    console.log('Device Session ID asignado:', currentDeviceSessionId);

                    // Enviar formulario al servidor
                    var formData = new FormData($('#payment-form')[0]);

                    fetch('{{ route("openpay.process") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Respuesta del servidor:', data);

                            if (data.success) {
                                successText.text(data.message);
                                successDiv.show();

                                setTimeout(function () {
                                    window.location.href = '{{ route("viewCustomerDebts.index") }}';
                                }, 2000);
                            } else {
                                showError(data.error || 'Error al procesar el pago');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showError('Error de conexión. Por favor, intenta de nuevo.');
                        });
                },
                function (error) {
                    // Error al crear token
                    console.error('Error al crear token:', error);
                    var errorMsg = 'Error al procesar la tarjeta';

                    if (error.data && error.data.description) {
                        errorMsg = error.data.description;
                    }

                    showError(errorMsg);
                }
            );

            function showError(message) {
                errorText.text(message);
                errorDiv.show();
                payButton.prop('disabled', false);
                buttonText.show();
                buttonLoading.hide();

                $('html, body').animate({
                    scrollTop: errorDiv.offset().top - 100
                }, 500);
            }
        });

        // Notificación de modo sandbox
        @if($sandbox)
            toastr.info('Estás en modo de prueba. Usa las tarjetas de prueba para simular pagos.', 'Modo Sandbox', {
                timeOut: 5000
            });
        @endif
    });
</script>
@stop
