@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Mis Deudas')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Mis Deudas</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                                <form method="GET" action="{{ route('viewCustomerDebts.index') }}" class="flex-grow-1 mt-2" style="min-width: 328px; max-width: 40%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por Toma, Dirección" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Toma">
                                                <i class="fas fa-search d-lg-none"></i>
                                                <span class="d-none d-lg-inline">Buscar</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="waterConnectionsTable" class="table table-striped display responsive nowrap" style="width:100%; max-width: 100%; margin: 0 auto; margin-top: 30px;">
                                    <thead>
                                        <tr>
                                            <th>Tomas de Agua</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($waterConnections->count() == 0)
                                            <tr>
                                                <td colspan="1" class="text-center">
                                                    <div class="text-center py-4">
                                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                        <h5>¡No tienes deudas pendientes!</h5>
                                                        <p class="text-muted">Todas tus tomas de agua están al corriente.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            @foreach($waterConnections as $connection)
                                                <tr class="clickable-row" data-toggle="collapse" data-target="#collapse-{{ $connection->id }}">
                                                    <td>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong class="text-dark">
                                                                    {{ $connection->name ?: 'Toma #' . $connection->id }}
                                                                </strong>
                                                                <div class="connection-details mt-1">
                                                                    @if($connection->street || $connection->exterior_number)
                                                                        <div>
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-map-marker-alt"></i>
                                                                                @if($connection->street)
                                                                                    Calle {{ $connection->street }}
                                                                                @endif
                                                                                @if($connection->exterior_number)
                                                                                    #{{ $connection->exterior_number }}
                                                                                @endif
                                                                                @if($connection->interior_number)
                                                                                    Int. {{ $connection->interior_number }}
                                                                                @endif
                                                                            </small>
                                                                        </div>
                                                                    @endif

                                                                    @if($connection->type)
                                                                        <small class="badge badge-secondary mt-1">
                                                                            {{ $connection->type === 'residencial' ? 'Residencial' : 'Comercial' }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="badges">
                                                                <i class="fas fa-chevron-down rotate-icon ml-2"></i>
                                                            </div>
                                                        </div>
                                                        <div id="collapse-{{ $connection->id }}" class="collapse mt-3">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th>ID</th>
                                                                            <th>Fecha de Inicio</th>
                                                                            <th>Fecha de Fin</th>
                                                                            <th>Monto</th>
                                                                            <th>Pendiente</th>
                                                                            <th>Estatus</th>
                                                                            <th>Pago en línea</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $unpaidDebts = $connection->debts->where('status', '!=', 'paid');
                                                                        @endphp
                                                                        @if($unpaidDebts->count() > 0)
                                                                            @foreach($unpaidDebts as $debt)
                                                                            <tr>
                                                                                <td>{{ $debt->id }}</td>
                                                                                <td>{{ \Carbon\Carbon::parse($debt->start_date)->format('d/m/Y') }}</td>
                                                                                <td>{{ \Carbon\Carbon::parse($debt->end_date)->format('d/m/Y') }}</td>
                                                                                <td>${{ number_format($debt->amount, 2) }}</td>
                                                                                <td>
                                                                                    <span class="text font-weight">
                                                                                        ${{ number_format($debt->remainingAmount, 2) }}
                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    @php
                                                                                        $badgeClass = [
                                                                                            'pending' => 'danger',
                                                                                            'partial' => 'warning',
                                                                                            'paid' => 'success'
                                                                                        ][$debt->status] ?? 'secondary';

                                                                                        $statusLabels = [
                                                                                            'pending' => 'No pagada',
                                                                                            'partial' => 'Abonada',
                                                                                            'paid' => 'Pagada'
                                                                                        ];
                                                                                    @endphp
                                                                                    <span class="badge badge-{{ $badgeClass }}">
                                                                                        {{ $statusLabels[$debt->status] ?? $debt->status }}
                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    @if($debt->status !== 'paid')
                                                                                        <button type="button" 
                                                                                            class="btn btn-sm btn-success btn-open-payment"
                                                                                            data-debt-id="{{ $debt->id }}"
                                                                                            data-debt-amount="{{ $debt->amount }}"
                                                                                            data-remaining="{{ $debt->remainingAmount }}"
                                                                                            data-total-paid="{{ $debt->total_paid }}"
                                                                                            data-start-date="{{ \Carbon\Carbon::parse($debt->start_date)->format('d/m/Y') }}"
                                                                                            data-end-date="{{ \Carbon\Carbon::parse($debt->end_date)->format('d/m/Y') }}"
                                                                                            data-water-connection="{{ $connection->name ?: 'Toma #' . $connection->id }}">
                                                                                            <i class="fas fa-credit-card"></i> Pagar con Tarjeta
                                                                                        </button>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="6" class="text-center text-muted">
                                                                                    No hay deudas pendientes para esta toma
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @if($waterConnections->total() > $waterConnections->perPage())
                                <div class="d-flex justify-content-center mt-3">
                                    {!! $waterConnections->links() !!}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@include('viewCustomerDebts.openpayModal')
@endsection

@section('css')
<style>
    .rotate-icon {
        transition: transform 0.3s ease;
        transform-origin: center;
    }
    .collapse.show .rotate-icon {
        transform: rotate(180deg);
    }
    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: #f5f5f5;
    }
    .connection-details {
        font-size: 0.85rem;
    }
    .connection-details .badge {
        font-size: 0.7rem;
        margin-right: 5px;
    }
    .table-bordered {
        border: 1px solid #dee2e6;
    }
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .table-sm th,
    .table-sm td {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    .table-info {
        background-color: #d1ecf1 !important;
    }
    .saved-card-item {
        transition: all 0.2s ease;
        background: #fff;
        border: 1px solid #dee2e6 !important;
    }
    .saved-card-item:hover {
        background-color: #f8f9fa;
        border-color: #007bff !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .saved-card-item .card-brand-icon {
        width: 50px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 4px;
        margin-right: 12px;
    }
    .saved-card-item .card-brand-icon i {
        font-size: 24px;
    }
    .saved-card-item .card-info {
        flex: 1;
    }
    .saved-card-item .card-info .card-name {
        font-weight: 600;
        font-size: 14px;
        color: #333;
    }
    .saved-card-item .card-info .card-expiry {
        font-size: 12px;
        color: #6c757d;
    }
    .saved-card-item .card-arrow {
        color: #adb5bd;
        font-size: 16px;
    }
    .saved-card-item.selected {
        background-color: #e7f3ff;
        border-color: #007bff !important;
        border-width: 2px !important;
    }
    .saved-card-item.selected .card-arrow i {
        color: #007bff;
    }
    #saved-card-cvv-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-top: 10px;
    }
</style>
@endsection

@section('js')
<script type="text/javascript" src="https://openpay.s3.amazonaws.com/openpay.v1.min.js"></script>
<script type="text/javascript" src="https://openpay.s3.amazonaws.com/openpay-data.v1.min.js"></script>
<script>
    var openpayDeviceSessionId = null;
    var savedCards = [];
    var currentPaymentMode = 'new';
    var selectedSavedCard = null;

    $(document).ready(function() {
        $('#waterConnectionsTable').DataTable({
            responsive: true,
            paging: false,
            info: false,
            searching: false,
            order: [[0, 'asc']]
        });

        $('#waterConnectionsTable').on('click', '.clickable-row', function(e) {
            if (!$(e.target).is('a') && !$(e.target).is('button') && !$(e.target).closest('a').length && !$(e.target).closest('button').length) {
                var target = $(this).data('target');
                var $target = $(target);
                var icon = $(this).find('.rotate-icon');
                if ($target.length) {
                    $target.collapse('toggle');
                    if ($target.hasClass('show')) {
                        icon.css('transform', 'rotate(180deg)');
                    } else {
                        icon.css('transform', 'rotate(0deg)');
                    }
                }
            }
        });

        @if(isset($locality) && $locality && $locality->hasOpenPayEnabled())
        OpenPay.setId('{{ $locality->openpay_merchant_id }}');
        OpenPay.setApiKey('{{ $locality->openpay_public_key }}');
        OpenPay.setSandboxMode({{ $locality->openpay_sandbox ? 'true' : 'false' }});
        var openpayEnabled = true;
        @else
        OpenPay.setId('');
        OpenPay.setApiKey('');
        OpenPay.setSandboxMode(true);
        var openpayEnabled = false;
        @endif

        function loadSavedCards(callback) {
            $.ajax({
                url: '{{ route("customerCards.forPayment") }}',
                method: 'GET',
                success: function(response) {
                    savedCards = response.cards || [];
                    if (callback) callback();
                },
                error: function() {
                    savedCards = [];
                    if (callback) callback();
                }
            });
        }

        function renderSavedCards() {
            var container = $('#saved-cards-list');
            container.empty();

            if (savedCards.length === 0) {
                $('#saved-cards-section').hide();
                $('#use-saved-cards-section').hide();
                showNewCardForm();
                return;
            }

            $('#saved-cards-section').show();
            $('#use-saved-cards-section').hide();
            $('#new-card-form-section').hide();
            $('#saved-card-cvv-section').hide();
            $('#card-brands-section').hide();

            savedCards.forEach(function(card) {
                var defaultBadge = card.is_default ? ' <i class="fas fa-star text-primary" title="Predeterminada"></i>' : '';
                var selectedClass = (selectedSavedCard && selectedSavedCard.id === card.id) ? ' selected' : '';
                var html = `
                    <div class="saved-card-item rounded p-3 mb-2 d-flex align-items-center${selectedClass}"
                            data-card-id="${card.id}" data-openpay-card-id="${card.openpay_card_id}" style="cursor: pointer;">
                        <div class="card-brand-icon">
                            <i class="${card.brand_icon}"></i>
                        </div>
                        <div class="card-info">
                            <div class="card-name">${card.display_name}${defaultBadge}</div>
                            <div class="card-expiry">Vence: ${card.expiration}</div>
                        </div>
                        <div class="card-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                `;
                container.append(html);
            });
        }

        function showNewCardForm() {
            currentPaymentMode = 'new';
            $('#saved-cards-section').hide();
            $('#saved-card-cvv-section').hide();
            $('#new-card-form-section').show();
            $('#card-brands-section').show();
            $('#modal-use-saved-card').val('0');
            $('#modal-saved-card-id').val('');
            selectedSavedCard = null;

            // Mostrar botón "Usar Tarjetas Guardadas" si hay tarjetas guardadas
            if (savedCards.length > 0) {
                $('#use-saved-cards-section').show();
            } else {
                $('#use-saved-cards-section').hide();
            }
        }

        function isMobileDevice() {
            return window.innerWidth < 768;
        }

        function showSavedCardCvv(card) {
            currentPaymentMode = 'saved';
            selectedSavedCard = card;
            $('#modal-use-saved-card').val('1');
            $('#modal-saved-card-id').val(card.openpay_card_id);

            // Mostrar información de la tarjeta guardada en la sección de CVV
            $('#saved-card-info-display').text(card.display_name + ' •••• ' + card.last_four);

            var amount = parseFloat($('#modal-amount').val()) || 0;
            var maxAmount = parseFloat($('#modal-amount').attr('max')) || 0;

            if (isMobileDevice()) {
                $('#savedCardPayIcon').attr('class', card.brand_icon);
                $('#savedCardPayName').text(card.display_name);
                $('#savedCardPayLastFour').text(card.last_four);
                $('#savedCardPayCvv').val('');
                $('#savedCardPayError').hide();
                $('#savedCardPayAmount').val(amount.toFixed(2)).attr('max', maxAmount);
                $('#savedCardPayModal').modal('show');
            } else {
                $('#new-card-form-section').hide();
                $('#saved-card-cvv-section').show();
                $('#card-brands-section').hide();
                $('#use-saved-cards-section').hide();
                $('#modal-saved-cvv').val('');
                $('#modal-amount-saved').val(amount.toFixed(2)).attr('max', maxAmount);
            }
        }

        $(document).on('click', '.saved-card-item', function() {
            var cardId = $(this).data('card-id');
            var openpayCardId = $(this).data('openpay-card-id');
            var card = savedCards.find(c => c.id == cardId);
            if (card) {
                $('.saved-card-item').removeClass('selected');
                $(this).addClass('selected');
                
                card.openpay_card_id = openpayCardId;
                showSavedCardCvv(card);
            }
        });

        $('#btn-use-new-card').on('click', function() {
            $('.saved-card-item').removeClass('selected');
            selectedSavedCard = null;
            showNewCardForm();
        });

        $('#btn-use-saved-cards').on('click', function() {
            $('#new-card-form-section').hide();
            $('#saved-card-cvv-section').hide();
            $('#use-saved-cards-section').hide();
            $('#card-brands-section').hide();
            $('#saved-cards-section').show();
            renderSavedCards();
        });

        $('#btn-use-other-card').on('click', function() {
            $('.saved-card-item').removeClass('selected');
            selectedSavedCard = null;
            showNewCardForm();
        });

        $('#btn-back-to-saved').on('click', function() {
            currentPaymentMode = 'new';
            $('#new-card-form-section').hide();
            $('#saved-card-cvv-section').hide();
            $('#saved-cards-section').show();
            renderSavedCards();
        });

        $('#modal-amount-saved').on('input', function() {
            var amount = parseFloat($(this).val()) || 0;
            var maxAmount = parseFloat($(this).attr('max')) || 0;
            
            if (amount > maxAmount) {
                $(this).val(maxAmount.toFixed(2));
                amount = maxAmount;
            }
            
            $('#modal-amount').val(amount.toFixed(2));
            $('#modal-amount-display').text(amount.toFixed(2));
        });

        $('#modal-saved-cvv, #modal-exp-year, #modal-cvv').on('input', function() {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });

        $(document).on('click', '.btn-open-payment', function() {
            if (!openpayEnabled) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pago en línea no disponible',
                    text: 'El pago con tarjeta no está habilitado para tu localidad. Por favor contacta al administrador o utiliza otro método de pago.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            var btn = $(this);
            var debtId = btn.data('debt-id');
            var totalPaid = parseFloat(btn.data('total-paid')) || 0;
            var remaining = parseFloat(btn.data('remaining')) || 0;

            $('#openpay-modal-form')[0].reset();
            $('#modal-debt-id').val(debtId);
            $('#modal-water-connection').text(btn.data('water-connection'));
            $('#modal-period').text(btn.data('start-date') + ' - ' + btn.data('end-date'));
            $('#modal-debt-amount').text('$' + parseFloat(btn.data('debt-amount')).toFixed(2));
            
            $('#modal-paid-container').hide();
            if (totalPaid > 0) {
                $('#modal-paid-container').show();
                $('#modal-total-paid').text('$' + totalPaid.toFixed(2));
            }
            
            $('#modal-remaining-amount').text('$' + remaining.toFixed(2));
            $('#modal-amount').val(remaining.toFixed(2)).attr('max', remaining);
            $('#modal-amount-display').text(remaining.toFixed(2));
            $('#modal-error-message, #modal-success-message').hide();
            $('#modal-card-brand').html('');
            $('#modal-pay-button').prop('disabled', false).show();
            $('#modal-button-text').show();
            $('#modal-button-loading').hide();

            currentPaymentMode = 'new';
            selectedSavedCard = null;
            $('#modal-use-saved-card').val('0');
            $('#modal-saved-card-id').val('');
            $('#modal-saved-cvv').val('');

            openpayDeviceSessionId = OpenPay.deviceData.setup("openpay-modal-form", "deviceIdHiddenFieldName");

            loadSavedCards(function() {
                renderSavedCards();
                $('#openpayModal').modal('show');
            });
        });

        $('#modal-holder-name').on('input', function() {
            var value = $(this).val();
            var cleaned = value.replace(/[^A-Za-z\s]/g, '');
            if (value !== cleaned) {
                $(this).val(cleaned);
            }
        });

        $('#modal-card-number').on('input', function() {
            var value = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(value);
            
            var cardBrand = '';
            if (value.length >= 6) {
                var cardType = OpenPay.card.cardType(value);
                var brandIcons = {
                    'visa': '<i class="fab fa-cc-visa fa-lg text-primary"></i> Visa',
                    'mastercard': '<i class="fab fa-cc-mastercard fa-lg text-warning"></i> MasterCard',
                    'american_express': '<i class="fab fa-cc-amex fa-lg text-info"></i> AMEX'
                };
                cardBrand = brandIcons[cardType] || '';
            }
            $('#modal-card-brand').html(cardBrand);
        });

        $('#modal-exp-year').on('input', function() {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });

        $('#modal-amount').on('input', function() {
            var amount = parseFloat($(this).val()) || 0;
            var maxAmount = parseFloat($(this).attr('max')) || 0;
            
            if (amount > maxAmount) {
                $(this).val(maxAmount.toFixed(2));
                amount = maxAmount;
            }
            
            $('#modal-amount-display').text(amount.toFixed(2));
        });

        var errorMessages = {
            'The card was declined by the bank': 'La tarjeta fue rechazada por el banco',
            'The card has expired': 'La tarjeta ha expirado',
            'The card doesn\'t have sufficient funds': 'Fondos insuficientes',
            'The card was reported as stolen': 'La tarjeta fue reportada como robada',
            'The card number is invalid': 'El número de tarjeta es inválido',
            'The security code is invalid': 'El código de seguridad es inválido',
            'The expiration date is invalid': 'La fecha de expiración es inválida',
            'Card declined': 'Tarjeta rechazada',
            'Insufficient funds': 'Fondos insuficientes'
        };

        function translateError(message) {
            if (errorMessages[message]) return errorMessages[message];
            for (var key in errorMessages) {
                if (message.toLowerCase().includes(key.toLowerCase())) return errorMessages[key];
            }
            return message;
        }

        function showModalError(message) {
            $('#modal-error-text').text(translateError(message));
            $('#modal-error-message').show();
            $('#modal-pay-button').prop('disabled', false);
            $('#modal-button-text').show();
            $('#modal-button-loading').hide();
        }

        function showPaymentSuccess(message) {
            $('#modal-success-text').text(message || '¡Pago procesado exitosamente!');
            $('#modal-success-message').show();
            $('#modal-pay-button').hide();
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        }

        function getDeviceSessionId() {
            return openpayDeviceSessionId || $('input[name="deviceIdHiddenFieldName"]').val();
        }

        $('#modal-pay-button').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true);
            $('#modal-button-text').hide();
            $('#modal-button-loading').show();
            $('#modal-error-message').hide();
            $('#modal-success-message').hide();

            var maxAmount = parseFloat($('#modal-amount').attr('max')) || 0;

            if (currentPaymentMode === 'saved' && selectedSavedCard) {
                var savedCvv = $('#modal-saved-cvv').val();
                var savedAmount = parseFloat($('#modal-amount-saved').val()) || 0;

                if (!/^[0-9]{3,4}$/.test(savedCvv)) {
                    showModalError('El CVV debe tener 3 o 4 dígitos numéricos');
                    return;
                }

                if (savedAmount <= 0) {
                    showModalError('El monto debe ser mayor a $0.00');
                    return;
                }
                if (savedAmount > maxAmount) {
                    showModalError('El monto no puede ser mayor al pendiente ($' + maxAmount.toFixed(2) + ')');
                    return;
                }

                $.ajax({
                    url: '{{ route("openpay.process") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        debt_id: $('#modal-debt-id').val(),
                        amount: savedAmount,
                        use_saved_card: '1',
                        saved_card_id: selectedSavedCard.openpay_card_id,
                        cvv2: savedCvv,
                        device_session_id: getDeviceSessionId()
                    },
                    success: function(data) {
                        if (data.success) {
                            showPaymentSuccess(data.message);
                        } else {
                            showModalError(data.error || 'Error al procesar el pago');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = xhr.responseJSON?.error || 'Error de conexión';
                        showModalError(errorMsg);
                    }
                });
                return;
            }

            var cardNumber = $('#modal-card-number').val().replace(/[^0-9]/g, '');
            var cvv = $('#modal-cvv').val();
            var month = $('#modal-exp-month').val();
            var year = $('#modal-exp-year').val();
            var holderName = $('#modal-holder-name').val();
            var amount = parseFloat($('#modal-amount').val()) || 0;

            if (!holderName || holderName.trim() === '') {
                showModalError('Ingresa el nombre del titular');
                return;
            }
            if (!/^[A-Za-z\s]+$/.test(holderName)) {
                showModalError('El nombre del titular solo debe contener letras sin acentos');
                return;
            }

            if (!/^[0-9]{13,19}$/.test(cardNumber)) {
                showModalError('El número de tarjeta debe tener entre 13 y 19 dígitos numéricos');
                return;
            }
            if (!OpenPay.card.validateCardNumber(cardNumber)) {
                showModalError('Número de tarjeta inválido');
                return;
            }

            if (!month || month === '') {
                showModalError('Selecciona el mes de expiración');
                return;
            }

            var currentYear = new Date().getFullYear() % 100;
            var yearNum = parseInt(year, 10);
            if (!/^[0-9]{2}$/.test(year)) {
                showModalError('El año debe tener 2 dígitos numéricos');
                return;
            }
            if (yearNum < currentYear) {
                showModalError('El año debe ser ' + currentYear + ' o mayor');
                return;
            }

            if (!/^[0-9]{3,4}$/.test(cvv)) {
                showModalError('El CVV debe tener 3 o 4 dígitos numéricos');
                return;
            }
            if (!OpenPay.card.validateCVC(cvv)) {
                showModalError('CVV inválido');
                return;
            }

            if (!OpenPay.card.validateExpiry(month, year)) {
                showModalError('Fecha de expiración inválida');
                return;
            }

            if (amount <= 0) {
                showModalError('El monto debe ser mayor a $0.00');
                return;
            }
            if (amount > maxAmount) {
                showModalError('El monto no puede ser mayor al pendiente ($' + maxAmount.toFixed(2) + ')');
                return;
            }

            OpenPay.token.extractFormAndCreate(
                'openpay-modal-form',
                function(response) {
                    $('#modal-token-id').val(response.data.id);
                    $('#modal-device-session-id').val(getDeviceSessionId());

                    var formData = new FormData($('#openpay-modal-form')[0]);

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
                        if (data.success) {
                            showPaymentSuccess(data.message);
                        } else {
                            showModalError(data.error || 'Error al procesar el pago');
                        }
                    })
                    .catch(() => {
                        showModalError('Error de conexión. Intenta de nuevo.');
                    });
                },
                function(error) {
                    var errorMsg = error.data?.description || 'Error al procesar la tarjeta';
                    showModalError(errorMsg);
                }
            );
        });

        $('#savedCardPayBtn').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true);
            $('#savedCardPayBtnText').hide();
            $('#savedCardPayBtnLoading').show();
            $('#savedCardPayError').hide();

            var cvv = $('#savedCardPayCvv').val();
            var amount = parseFloat($('#savedCardPayAmount').val()) || 0;
            var maxAmount = parseFloat($('#savedCardPayAmount').attr('max')) || 0;

            if (!/^[0-9]{3,4}$/.test(cvv)) {
                $('#savedCardPayErrorText').text('CVV inválido (3-4 dígitos)');
                $('#savedCardPayError').show();
                btn.prop('disabled', false);
                $('#savedCardPayBtnText').show();
                $('#savedCardPayBtnLoading').hide();
                return;
            }

            if (amount <= 0) {
                $('#savedCardPayErrorText').text('Ingresa un monto válido');
                $('#savedCardPayError').show();
                btn.prop('disabled', false);
                $('#savedCardPayBtnText').show();
                $('#savedCardPayBtnLoading').hide();
                return;
            }

            if (amount > maxAmount) {
                $('#savedCardPayErrorText').text('Máximo: $' + maxAmount.toFixed(2));
                $('#savedCardPayError').show();
                btn.prop('disabled', false);
                $('#savedCardPayBtnText').show();
                $('#savedCardPayBtnLoading').hide();
                return;
            }

            $.ajax({
                url: '{{ route("openpay.process") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    debt_id: $('#modal-debt-id').val(),
                    amount: amount,
                    use_saved_card: '1',
                    saved_card_id: selectedSavedCard.openpay_card_id,
                    cvv2: cvv,
                    device_session_id: getDeviceSessionId()
                },
                success: function(data) {
                    $('#savedCardPayModal').modal('hide');
                    if (data.success) {
                        showPaymentSuccess(data.message);
                    } else {
                        showModalError(data.error || 'Error al procesar el pago');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    $('#savedCardPayBtnText').show();
                    $('#savedCardPayBtnLoading').hide();
                    var errorMsg = xhr.responseJSON?.error || 'Error de conexión';
                    $('#savedCardPayErrorText').text(errorMsg);
                    $('#savedCardPayError').show();
                }
            });
        });

        $('#savedCardPayModal').on('hidden.bs.modal', function() {
            $('#savedCardPayCvv').val('');
            $('#savedCardPayError').hide();
            $('#savedCardPayBtn').prop('disabled', false);
            $('#savedCardPayBtnText').show();
            $('#savedCardPayBtnLoading').hide();
        });

        @if(config('openpay.sandbox'))
        toastr.info('Modo de prueba activo. Usa tarjetas de prueba.', 'Sandbox', {timeOut: 4000});
        @endif
    });
</script>
@endsection
