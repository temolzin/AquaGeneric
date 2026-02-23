@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Mis Tarjetas')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-credit-card"></i> Mis Tarjetas Guardadas
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                    data-target="#addCardModal"
                                    {{ $cards->count() >= 10 ? 'disabled title=\"Has alcanzado el límite de 10 tarjetas\"' : '' }}>
                                    <i class="fas fa-plus"></i> Agregar Tarjeta
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-shield-alt"></i>
                                        <strong>Seguridad:</strong> Tus tarjetas se almacenan de forma segura.
                                        Solo guardamos los últimos 4 dígitos y un token seguro para pagos.
                                    </div>
                                </div>
                            </div>

                            @if($cards->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fas fa-credit-card fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">No tienes tarjetas guardadas</h4>
                                    <p class="text-muted">Agrega una tarjeta para realizar pagos más rápidos.</p>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#addCardModal">
                                        <i class="fas fa-plus"></i> Agregar mi primera tarjeta
                                    </button>
                                </div>
                            @else
                                <div class="row">
                                    @foreach($cards as $card)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div
                                                class="card h-100 {{ $card->is_default ? 'border-primary' : '' }} {{ $card->is_expired ? 'border-danger' : '' }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <i class="{{ $card->brand_icon }} fa-2x"></i>
                                                        </div>
                                                        <div class="dropdown">
                                                            <button class="btn btn-link p-0 text-muted" data-toggle="dropdown">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a href="#" class="dropdown-item btn-edit-alias"
                                                                    data-card-id="{{ $card->id }}" data-alias="{{ $card->alias }}">
                                                                    <i class="fas fa-edit"></i> Editar Alias
                                                                </a>
                                                                @if(!$card->is_default)
                                                                    <a href="#" class="dropdown-item btn-set-default"
                                                                        data-card-id="{{ $card->id }}">
                                                                        <i class="fas fa-star"></i> Establecer como predeterminada
                                                                    </a>
                                                                @endif
                                                                <div class="dropdown-divider"></div>
                                                                <a href="#" class="dropdown-item text-danger btn-delete-card"
                                                                    data-card-id="{{ $card->id }}"
                                                                    data-display-name="{{ $card->display_name }}">
                                                                    <i class="fas fa-trash"></i> Eliminar
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <h5 class="mb-1" id="card-display-{{ $card->id }}">
                                                        {{ $card->alias ?: ucfirst($card->brand) }}
                                                    </h5>
                                                    <p class="text-muted mb-2">
                                                        <span class="card-number-dots">•••• •••• ••••</span> {{ $card->last_four }}
                                                    </p>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">
                                                            Vence: {{ $card->expiration_month }}/{{ $card->expiration_year }}
                                                        </small>
                                                        <div>
                                                            @if($card->is_expired)
                                                                <span class="badge badge-danger">Expirada</span>
                                                            @endif
                                                            @if($card->is_default)
                                                                <span class="badge badge-primary">
                                                                    <i class="fas fa-star"></i> Predeterminada
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="mt-2">
                                                        <small class="text-muted">{{ $card->holder_name }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('customerCards.addCardModal')
    @include('customerCards.editAliasModal')
    @include('customerCards.deleteCardModal')

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text" id="loadingText">Procesando...</div>
    </div>

    <div class="success-modal-overlay" id="successOverlay">
        <div class="success-modal-content">
            <div class="success-modal-icon">
                <i class="fas fa-check"></i>
            </div>
            <h4 class="success-modal-title" id="successTitle">Éxito</h4>
            <p class="success-modal-text" id="successText">Operación realizada correctamente</p>
            <button type="button" class="btn btn-primary success-modal-btn" id="successAcceptBtn">Aceptar</button>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .card-number-dots {
            letter-spacing: 2px;
        }

        .card.border-primary {
            border-width: 2px !important;
        }

        .card.border-danger {
            border-width: 2px !important;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            color: white;
            margin-top: 15px;
            font-size: 18px;
            font-weight: 500;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .success-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .success-modal-overlay.active {
            display: flex;
        }

        .success-modal-content {
            background: white;
            border-radius: 10px;
            padding: 30px 50px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
        }

        .success-modal-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #28a745;
            background: transparent;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
        }

        .success-modal-icon i {
            font-size: 40px;
            color: #28a745;
        }

        .success-modal-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .success-modal-text {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
        }

        .success-modal-btn {
            padding: 10px 40px;
            font-size: 16px;
            border-radius: 5px;
            background-color: #6f42c1;
            border-color: #6f42c1;
        }

        .success-modal-btn:hover {
            background-color: #5a32a3;
            border-color: #5a32a3;
        }

        .btn-loading .spinner-border {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }
    </style>
@endsection

@section('js')
    <script type="text/javascript" src="https://openpay.s3.amazonaws.com/openpay.v1.min.js"></script>
    <script type="text/javascript" src="https://openpay.s3.amazonaws.com/openpay-data.v1.min.js"></script>
    <script>
        var openpayDeviceSessionId = null;

        $(document).ready(function () {
            OpenPay.setId('{{ config("openpay.merchant_id") }}');
            OpenPay.setApiKey('{{ config("openpay.public_key") }}');
            OpenPay.setSandboxMode({{ config("openpay.sandbox") ? 'true' : 'false' }});

            $('#addCardModal').on('show.bs.modal', function () {
                openpayDeviceSessionId = OpenPay.deviceData.setup("add-card-form", "deviceIdHiddenFieldName");
                resetAddCardForm();
            });

            $('#add-holder-name').on('input', function () {
                var value = $(this).val();
                var cleaned = value.replace(/[^A-Za-z\s]/g, '');
                if (value !== cleaned) {
                    $(this).val(cleaned);
                }
            });

            $('#add-card-number').on('input', function () {
                var value = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(value);

                if (value.length >= 6) {
                    var cardType = OpenPay.card.cardType(value);
                    var cardBrand = '';
                    var brandValue = '';
                    switch (cardType) {
                        case 'visa':
                            cardBrand = '<i class="fab fa-cc-visa fa-lg text-primary"></i> Visa';
                            brandValue = 'visa';
                            break;
                        case 'mastercard':
                            cardBrand = '<i class="fab fa-cc-mastercard fa-lg text-warning"></i> MasterCard';
                            brandValue = 'mastercard';
                            break;
                        case 'american_express':
                            cardBrand = '<i class="fab fa-cc-amex fa-lg text-info"></i> AMEX';
                            brandValue = 'american_express';
                            break;
                    }
                    $('#add-card-brand-display').html(cardBrand);
                    $('#add-card-brand').val(brandValue || cardType);
                } else {
                    $('#add-card-brand-display').html('');
                    $('#add-card-brand').val('');
                }
            });

            $('#add-exp-year').on('input', function () {
                var value = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(value);
            });

            $('#add-cvv').on('input', function () {
                var value = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(value);
            });

            $('#btn-save-card').on('click', function () {
                var btn = $(this);
                btn.prop('disabled', true);
                $('#btn-save-text').hide();
                $('#btn-save-loading').show();
                $('#add-card-error').hide();

                var cardNumber = $('#add-card-number').val().replace(/[^0-9]/g, '');
                var cvv = $('#add-cvv').val();
                var month = $('#add-exp-month').val();
                var year = $('#add-exp-year').val();
                var holderName = $('#add-holder-name').val();
                var alias = $('#add-alias').val();

                if (!holderName || holderName.trim() === '') {
                    showAddCardError('Ingresa el nombre del titular');
                    return;
                }
                if (!/^[A-Za-z\s]+$/.test(holderName)) {
                    showAddCardError('El nombre del titular solo debe contener letras sin acentos');
                    return;
                }
                if (!/^[0-9]{13,19}$/.test(cardNumber)) {
                    showAddCardError('El número de tarjeta debe tener entre 13 y 19 dígitos');
                    return;
                }
                if (!OpenPay.card.validateCardNumber(cardNumber)) {
                    showAddCardError('Número de tarjeta inválido');
                    return;
                }
                if (!month || month === '') {
                    showAddCardError('Selecciona el mes de expiración');
                    return;
                }
                var currentYear = new Date().getFullYear() % 100;
                var yearNum = parseInt(year, 10);
                if (!/^[0-9]{2}$/.test(year)) {
                    showAddCardError('El año debe tener 2 dígitos');
                    return;
                }
                if (yearNum < currentYear) {
                    showAddCardError('El año debe ser ' + currentYear + ' o mayor');
                    return;
                }
                if (!/^[0-9]{3,4}$/.test(cvv)) {
                    showAddCardError('El CVV debe tener 3 o 4 dígitos');
                    return;
                }
                if (!OpenPay.card.validateCVC(cvv)) {
                    showAddCardError('CVV inválido');
                    return;
                }
                if (!OpenPay.card.validateExpiry(month, year)) {
                    showAddCardError('Fecha de expiración inválida');
                    return;
                }

                OpenPay.token.extractFormAndCreate(
                    'add-card-form',
                    function (response) {
                        var currentDeviceSessionId = openpayDeviceSessionId || $('input[name="deviceIdHiddenFieldName"]').val();

                        $('#addCardModal').modal('hide');
                        showLoading('Guardando tarjeta...');

                        $.ajax({
                            url: '{{ route("customerCards.store") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                token_id: response.data.id,
                                device_session_id: currentDeviceSessionId,
                                alias: alias,
                                holder_name: holderName,
                                card_number: cardNumber,
                                expiration_month: month,
                                expiration_year: year,
                                brand: $('#add-card-brand').val() || 'unknown'
                            },
                            success: function (data) {
                                if (data.success) {
                                    showSuccess('Tarjeta registrada correctamente', function () {
                                        location.reload();
                                    });
                                } else {
                                    hideLoading();
                                    toastr.error(data.error || 'Error al guardar la tarjeta');
                                }
                            },
                            error: function (xhr) {
                                hideLoading();
                                var errorMsg = 'Error al guardar la tarjeta';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMsg = xhr.responseJSON.error;
                                }
                                toastr.error(errorMsg);
                            }
                        });
                    },
                    function (error) {
                        var errorMsg = 'Error al procesar la tarjeta';
                        if (error.data && error.data.description) {
                            errorMsg = error.data.description;
                        }
                        showAddCardError(errorMsg);
                    }
                );
            });

            $(document).on('click', '.btn-set-default', function (e) {
                e.preventDefault();
                var cardId = $(this).data('card-id');
                var btn = $(this);
                btn.prop('disabled', true);
                showLoading('Actualizando...');

                $.ajax({
                    url: '/customerCards/' + cardId + '/default',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (data) {
                        if (data.success) {
                            showSuccess('Tarjeta predeterminada actualizada', function () {
                                location.reload();
                            });
                        } else {
                            hideLoading();
                            btn.prop('disabled', false);
                            toastr.error(data.error || 'Error al establecer como predeterminada');
                        }
                    },
                    error: function () {
                        hideLoading();
                        btn.prop('disabled', false);
                        toastr.error('Error de conexión');
                    }
                });
            });

            $(document).on('click', '.btn-edit-alias', function (e) {
                e.preventDefault();
                var cardId = $(this).data('card-id');
                var alias = $(this).data('alias');

                $('#edit-alias-card-id').val(cardId);
                $('#edit-alias-input').val(alias || '');
                $('#editAliasModal').modal('show');
            });

            $('#btn-save-alias').on('click', function () {
                var cardId = $('#edit-alias-card-id').val();
                var alias = $('#edit-alias-input').val();
                var btn = $(this);
                btn.prop('disabled', true);
                $('#editAliasModal').modal('hide');
                showLoading('Actualizando alias...');

                $.ajax({
                    url: '/customerCards/' + cardId + '/alias',
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        alias: alias
                    },
                    success: function (data) {
                        if (data.success) {
                            showSuccess('Alias actualizado correctamente', function () {
                                location.reload();
                            });
                        } else {
                            hideLoading();
                            btn.prop('disabled', false);
                            toastr.error(data.error || 'Error al actualizar alias');
                        }
                    },
                    error: function () {
                        hideLoading();
                        btn.prop('disabled', false);
                        toastr.error('Error de conexión');
                    }
                });
            });

            $(document).on('click', '.btn-delete-card', function (e) {
                e.preventDefault();
                var cardId = $(this).data('card-id');
                var displayName = $(this).data('display-name');

                $('#delete-card-id').val(cardId);
                $('#delete-card-name').text(displayName);
                $('#deleteCardModal').modal('show');
            });

            $('#btn-confirm-delete').on('click', function () {
                var cardId = $('#delete-card-id').val();
                var btn = $(this);
                btn.prop('disabled', true);
                $('#deleteCardModal').modal('hide');
                showLoading('Eliminando tarjeta...');

                $.ajax({
                    url: '/customerCards/' + cardId,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (data) {
                        if (data.success) {
                            showSuccess('Tarjeta eliminada correctamente', function () {
                                location.reload();
                            });
                        } else {
                            hideLoading();
                            toastr.error(data.error || 'Error al eliminar tarjeta');
                            btn.prop('disabled', false);
                        }
                    },
                    error: function () {
                        hideLoading();
                        toastr.error('Error de conexión');
                        btn.prop('disabled', false);
                    }
                });
            });

            function showAddCardError(message) {
                $('#add-card-error-text').text(message);
                $('#add-card-error').show();
                $('#btn-save-card').prop('disabled', false);
                $('#btn-save-text').show();
                $('#btn-save-loading').hide();
            }

            function resetAddCardForm() {
                $('#add-card-form')[0].reset();
                $('#add-card-brand-display').html('');
                $('#add-card-brand').val('');
                $('#add-card-error').hide();
                $('#btn-save-card').prop('disabled', false);
                $('#btn-save-text').show();
                $('#btn-save-loading').hide();
            }

            function showLoading(text) {
                text = text || 'Procesando...';
                $('#loadingText').text(text);
                $('#loadingOverlay').addClass('active');
            }

            function hideLoading() {
                $('#loadingOverlay').removeClass('active');
            }

            var successCallback = null;

            function showSuccess(text, callback) {
                text = text || 'Operación realizada correctamente';
                hideLoading();
                $('#successText').text(text);
                $('#successOverlay').addClass('active');
                successCallback = callback;
            }

            $('#successAcceptBtn').on('click', function () {
                $('#successOverlay').removeClass('active');
                if (successCallback && typeof successCallback === 'function') {
                    successCallback();
                }
            });

            @if(config('openpay.sandbox'))
                toastr.info('Modo de prueba activo. Usa tarjetas de prueba.', 'Sandbox', { timeOut: 4000 });
            @endif
        });
    </script>
@endsection