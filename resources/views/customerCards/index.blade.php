@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Mis Tarjetas')
@section('plugins.Toastr', true)
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-header d-flex flex-column flex-md-row align-items-md-center">
                        <h3 class="card-title mb-2 mb-md-0">
                            <i class="fas fa-credit-card"></i> Mis Tarjetas Guardadas
                        </h3>
                        <div class="ml-md-auto">
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                data-target="#addCardModal" {{ $cards->count() >= 10 ? 'disabled title=\"Has alcanzado el límite de 10 tarjetas\"' : '' }}>
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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCardModal">
                                    <i class="fas fa-plus"></i> Agregar mi primera tarjeta
                                </button>
                            </div>
                        @else
                            <div class="d-block d-md-none">
                                @foreach($cards as $card)
                                    <div class="mobile-card-row d-flex align-items-center p-3 mb-2 rounded border {{ $card->is_default ? 'border-primary' : ($card->is_expired ? 'border-danger' : 'border-secondary') }}"
                                        style="cursor:pointer; background:#fff;" data-card-id="{{ $card->id }}"
                                        data-alias="{{ e($card->alias) }}" data-brand-icon="{{ $card->brand_icon }}"
                                        data-display-name="{{ e($card->display_name) }}" data-last-four="{{ $card->last_four }}"
                                        data-holder="{{ e($card->holder_name) }}"
                                        data-expiry="{{ $card->expiration_month }}/{{ $card->expiration_year }}"
                                        data-is-default="{{ $card->is_default ? '1' : '0' }}"
                                        data-is-expired="{{ $card->is_expired ? '1' : '0' }}">
                                        <div class="mr-3" style="pointer-events:none;">
                                            <i class="{{ $card->brand_icon }} fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1" style="pointer-events:none;">
                                            <div class="font-weight-bold" id="mobile-display-{{ $card->id }}">
                                                {{ $card->alias ?: ucfirst($card->brand) }}
                                            </div>
                                            <small class="text-muted">&bull;&bull;&bull;&bull; {{ $card->last_four }}</small>
                                        </div>
                                        <div class="d-flex align-items-center" style="pointer-events:none;">
                                            @if($card->is_expired)
                                                <span class="badge badge-danger mr-2">Expirada</span>
                                            @elseif($card->is_default)
                                                <span class="badge badge-primary mr-2"><i class="fas fa-star"></i></span>
                                            @endif
                                            <i class="fas fa-chevron-right text-muted"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-none d-md-flex flex-wrap row">
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
    </section>
    @include('customerCards.addCardModal')
    @include('customerCards.editAliasModal')
    @include('customerCards.deleteCardModal')
    <div class="modal fade" id="mobileCardDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" id="mobileCardDetailHeader">
                    <h5 class="modal-title">
                        <i id="mobileCardDetailIcon" class=""></i>
                        <span id="mobileCardDetailTitle"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-user mr-1"></i> Titular</span>
                            <strong id="mobileCardDetailHolder"></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-credit-card mr-1"></i> Número</span>
                            <strong>&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; <span
                                    id="mobileCardDetailLastFour"></span></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted"><i class="far fa-calendar-alt mr-1"></i> Vencimiento</span>
                            <strong id="mobileCardDetailExpiry"></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between" id="mobileCardDetailStatusRow">
                            <span class="text-muted"><i class="fas fa-info-circle mr-1"></i> Estado</span>
                            <span id="mobileCardDetailStatus"></span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer d-flex flex-column" style="gap:8px;">
                    <button type="button" class="btn btn-outline-secondary btn-block mobile-action-edit-alias">
                        <i class="fas fa-edit"></i> Editar Alias
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-block mobile-action-set-default"
                        style="display:none;">
                        <i class="fas fa-star"></i> Establecer como predeterminada
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-block mobile-action-delete">
                        <i class="fas fa-trash"></i> Eliminar tarjeta
                    </button>
                </div>
            </div>
        </div>
    </div>
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
        @if(isset($locality) && $locality && $locality->hasOpenPayEnabled())
        var openpayEnabled = true;
        @else
        var openpayEnabled = false;
        @endif
        
        $(document).ready(function () {
            @if(isset($locality) && $locality && $locality->hasOpenPayEnabled())
            OpenPay.setId('{{ $locality->openpay_merchant_id }}');
            OpenPay.setApiKey('{{ $locality->openpay_public_key }}');
            OpenPay.setSandboxMode({{ $locality->openpay_sandbox ? 'true' : 'false' }});
            @else
            OpenPay.setId('');
            OpenPay.setApiKey('');
            OpenPay.setSandboxMode(true);
            @endif

            $('#addCardModal').on('show.bs.modal', function (e) {
                if (!openpayEnabled) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No disponible',
                        text: 'El registro de tarjetas no está habilitado para tu localidad. Por favor contacta al administrador.',
                        confirmButtonText: 'Entendido'
                    });
                    return false;
                }
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

            var cardBrandConfig = {
                'visa': { icon: 'fab fa-cc-visa fa-lg text-primary', label: 'Visa', value: 'visa' },
                'mastercard': { icon: 'fab fa-cc-mastercard fa-lg text-warning', label: 'MasterCard', value: 'mastercard' },
                'american_express': { icon: 'fab fa-cc-amex fa-lg text-info', label: 'AMEX', value: 'american_express' }
            };

            $('#add-card-number').on('input', function () {
                var value = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(value);
                if (value.length >= 6) {
                    var cardType = OpenPay.card.cardType(value);
                    var config = cardBrandConfig[cardType] || { icon: '', label: '', value: cardType };
                    var cardBrand = config.icon ? '<i class="' + config.icon + '"></i> ' + config.label : '';
                    $('#add-card-brand-display').html(cardBrand);
                    $('#add-card-brand').val(config.value);
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
                                var handlers = {
                                    success: function() { showSuccess('Tarjeta registrada correctamente', function() { location.reload(); }); },
                                    duplicate: function() { hideLoading(); toastr.warning(data.error, 'Tarjeta ya registrada', { timeOut: 5000 }); },
                                    error: function() { hideLoading(); toastr.error(data.error || 'Error al guardar la tarjeta'); }
                                };
                                var action = data.success ? 'success' : (data.duplicate ? 'duplicate' : 'error');
                                handlers[action]();
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
                        handleAjaxResponse(data, 'Tarjeta predeterminada actualizada', 'Error al establecer como predeterminada', btn);
                    },
                    error: function () {
                        handleAjaxError(btn);
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
                        handleAjaxResponse(data, 'Alias actualizado correctamente', 'Error al actualizar alias', btn);
                    },
                    error: function () {
                        handleAjaxError(btn);
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
                        handleAjaxResponse(data, 'Tarjeta eliminada correctamente', 'Error al eliminar tarjeta', btn);
                    },
                    error: function () {
                        handleAjaxError(btn);
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

            function handleAjaxResponse(data, successMsg, errorMsg, btn) {
                data.success
                    ? showSuccess(successMsg, function() { location.reload(); })
                    : (hideLoading(), btn && btn.prop('disabled', false), toastr.error(data.error || errorMsg));
            }

            function handleAjaxError(btn) {
                hideLoading();
                btn && btn.prop('disabled', false);
                toastr.error('Error de conexión');
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
                if (typeof toastr !== 'undefined') {
                    toastr.info('Modo de prueba activo. Usa tarjetas de prueba.', 'Sandbox', { timeOut: 4000 });
                }
            @endif

                var mobileActiveCardId = null;

            $(document).on('click', '.mobile-card-row', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var row = $(this);
                mobileActiveCardId = row.data('card-id');
                var isDefault = row.data('is-default') == '1';
                var isExpired = row.data('is-expired') == '1';
                var alias = row.data('alias');
                var brand = row.data('brand-icon');
                var name = alias
                    ? alias + ' \u2022\u2022\u2022\u2022 ' + row.data('last-four')
                    : row.data('display-name');
                var header = $('#mobileCardDetailHeader');
                header.removeClass('bg-danger bg-primary bg-secondary');
                var headerClass = isExpired ? 'bg-danger' : (isDefault ? 'bg-primary' : 'bg-secondary');
                header.addClass(headerClass + ' text-white');
                $('#mobileCardDetailIcon').attr('class', brand + ' mr-2');
                $('#mobileCardDetailTitle').text(name);
                $('#mobileCardDetailHolder').text(row.data('holder'));
                $('#mobileCardDetailLastFour').text(row.data('last-four'));
                $('#mobileCardDetailExpiry').text(row.data('expiry'));
                var statusConfig = {
                    expired: { badge: 'badge-danger', text: 'Expirada', icon: '' },
                    default: { badge: 'badge-primary', text: 'Predeterminada', icon: '<i class="fas fa-star"></i> ' },
                    active: { badge: 'badge-success', text: 'Activa', icon: '' }
                };

                var statusKey = isExpired ? 'expired' : (isDefault ? 'default' : 'active');
                var status = statusConfig[statusKey];
                $('#mobileCardDetailStatus').html('<span class="badge ' + status.badge + '">' + status.icon + status.text + '</span>');
                $('.mobile-action-set-default').toggle(!isDefault);
                $('#mobileCardDetailModal').modal('show');
            });

            $('.mobile-action-edit-alias').on('click', function () {
                $('#mobileCardDetailModal').modal('hide');
                var row = $('.mobile-card-row[data-card-id="' + mobileActiveCardId + '"]');
                setTimeout(function () {
                    $('#edit-card-id').val(mobileActiveCardId);
                    $('#edit-alias').val(row.data('alias') || '');
                    $('#editAliasModal').modal('show');
                }, 300);
            });

            $('.mobile-action-set-default').on('click', function () {
                $('#mobileCardDetailModal').modal('hide');
                setTimeout(function () {
                    showLoading('Actualizando tarjeta predeterminada...');
                    $.ajax({
                        url: '/customerCards/' + mobileActiveCardId + '/default',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (data) {
                            if (data.success) {
                                showSuccess('Tarjeta establecida como predeterminada', function () {
                                    location.reload();
                                });
                            }
                        },
                        error: function () {
                            hideLoading();
                            toastr.error('Error de conexión');
                        }
                    });
                }, 300);
            });

            $('.mobile-action-delete').on('click', function () {
                $('#mobileCardDetailModal').modal('hide');
                var row = $('.mobile-card-row[data-card-id="' + mobileActiveCardId + '"]');
                setTimeout(function () {
                    $('#delete-card-id').val(mobileActiveCardId);
                    $('#delete-card-name').text(row.data('display-name'));
                    $('#deleteCardModal').modal('show');
                }, 300);
            });
        });
    </script>
@endsection
