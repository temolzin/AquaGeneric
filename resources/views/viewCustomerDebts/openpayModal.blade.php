<div class="modal fade" id="openpayModal" tabindex="-1" role="dialog" aria-labelledby="openpayModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title" id="openpayModalLabel">
                    <i class="fas fa-credit-card"></i> Pago con Tarjeta
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card card-outline card-info mb-3">
                    <div class="card-body py-2">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong><i class="fas fa-user text-primary"></i> Cliente:</strong>
                                    <span id="modal-customer-name">{{ $customer->name ?? '' }}
                                        {{ $customer->last_name ?? '' }}</span>
                                </p>
                                <p class="mb-1">
                                    <strong><i class="fas fa-tint text-info"></i> Toma:</strong>
                                    <span id="modal-water-connection"></span>
                                </p>
                                <p class="mb-1">
                                    <strong><i class="far fa-calendar-alt text-secondary"></i> Periodo:</strong>
                                    <span id="modal-period"></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong><i class="fas fa-money-bill-wave text-success"></i> Monto Deuda:</strong>
                                    <span class="badge badge-info" id="modal-debt-amount">$0.00</span>
                                </p>
                                <p class="mb-0" id="modal-paid-container" style="display: none;">
                                    <strong><i class="fas fa-check-circle text-success"></i> Ya Pagado:</strong>
                                    <span class="badge badge-success" id="modal-total-paid">$0.00</span>
                                </p>
                                <p class="mb-0">
                                    <strong><i class="fas fa-exclamation-triangle text-warning"></i> Pendiente:</strong>
                                    <span class="badge badge-warning" id="modal-remaining-amount">$0.00</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="saved-cards-section" style="display: none;">
                    <div class="card card-outline card-primary mb-3">
                        <div class="card-header py-2">
                            <h6 class="mb-0"><i class="fas fa-wallet"></i> Mis Tarjetas Guardadas</h6>
                        </div>
                        <div class="card-body py-2">
                            <div id="saved-cards-list" class="mb-2">
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btn-use-new-card">
                                    <i class="fas fa-plus"></i> Usar otra tarjeta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card-brands-section" class="text-center mb-3">
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted">Tarjetas de débito</small>
                            <div><img src="{{ asset('img/cards2.png') }}" alt="Débito" class="img-fluid" style="max-height: 35px;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted">Tarjetas de crédito</small>
                            <div><img src="{{ asset('img/cards1.png') }}" alt="Crédito" style="max-height: 35px;"></div>
                        </div>
                    </div>
                </div>

                <form id="openpay-modal-form">
                    @csrf
                    <input type="hidden" name="debt_id" id="modal-debt-id">
                    <input type="hidden" id="modal-token-id" name="token_id">
                    <input type="hidden" id="modal-device-session-id" name="device_session_id">
                    <input type="hidden" id="modal-saved-card-id" name="saved_card_id">
                    <input type="hidden" id="modal-use-saved-card" name="use_saved_card" value="0">

                    <div id="new-card-form-section">
                        <div class="alert alert-info py-2 px-3 mb-3">
                            <i class="fas fa-info-circle"></i>
                            <small>Esta tarjeta <strong>no será registrada</strong>, solo se usará para este pago.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> Nombre del titular</label>
                                    <input type="text" class="form-control" id="modal-holder-name"
                                        placeholder="Como aparece en la tarjeta" autocomplete="off"
                                        data-openpay-card="holder_name" pattern="[A-Za-z ]+"
                                        title="Solo letras sin acentos">
                                    <small class="text-muted">Solo letras sin acentos</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-credit-card"></i> Número de tarjeta</label>
                                    <input type="text" class="form-control" id="modal-card-number"
                                        placeholder="•••• •••• •••• ••••" autocomplete="off" data-openpay-card="card_number"
                                        maxlength="19" pattern="[0-9]{13,19}" inputmode="numeric"
                                        title="Entre 13 y 19 dígitos numéricos">
                                    <div id="modal-card-brand" class="mt-1" style="min-height: 20px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 col-md-3">
                                <div class="form-group">
                                    <label>Mes</label>
                                    <select class="form-control" id="modal-exp-month" data-openpay-card="expiration_month">
                                        <option value="">MM</option>
                                        <option value="01">01</option>
                                        <option value="02">02</option>
                                        <option value="03">03</option>
                                        <option value="04">04</option>
                                        <option value="05">05</option>
                                        <option value="06">06</option>
                                        <option value="07">07</option>
                                        <option value="08">08</option>
                                        <option value="09">09</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4 col-md-3">
                                <div class="form-group">
                                    <label>Año</label>
                                    <input type="text" class="form-control" id="modal-exp-year" placeholder="AA"
                                        maxlength="2" pattern="[0-9]{2}" inputmode="numeric"
                                        data-openpay-card="expiration_year" title="2 dígitos del año">
                                </div>
                            </div>
                            <div class="col-4 col-md-3" id="cvv-new-card-container">
                                <div class="form-group">
                                    <label>CVV <i class="fas fa-question-circle text-muted"
                                            title="Código de seguridad"></i></label>
                                    <input type="text" class="form-control" id="modal-cvv" placeholder="•••" maxlength="4"
                                        pattern="[0-9]{3,4}" inputmode="numeric" autocomplete="off"
                                        data-openpay-card="cvv2" title="3 o 4 dígitos numéricos">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Monto a pagar</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="modal-amount" name="amount"
                                            step="1.00" min="1.00" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="saved-card-cvv-section" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label><i class="fas fa-lock"></i> CVV de la tarjeta guardada</label>
                                    <input type="password" class="form-control form-control-md" id="modal-saved-cvv" placeholder="•••" maxlength="4"
                                        pattern="[0-9]{3,4}" inputmode="numeric" autocomplete="off"
                                        title="3 o 4 dígitos numéricos" style="letter-spacing: 5px; text-align: center;">
                                    <small class="text-muted">Código de seguridad</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label>Monto a pagar</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="modal-amount-saved" name="amount_saved"
                                            step="1.00" min="1.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="modal-error-message" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="modal-error-text"></span>
                    </div>
                    <div id="modal-success-message" class="alert alert-success" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        <span id="modal-success-text"></span>
                    </div>
                    <div class="openpay-footer text-center py-2 px-3 bg-light rounded mb-3">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <small>Transacciones vía:</small>
                                <img src="{{ asset('img/openpay.png') }}" alt="OpenPay" style="max-height: 25px;">
                            </div>
                            <div class="col-6">
                                <img src="{{ asset('img/security.png') }}" alt="Seguridad" style="max-height: 25px;">
                                <small class="d-block">Encriptación 256 bits</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" id="modal-pay-button" class="btn btn-success">
                    <span id="modal-button-text">
                        <i class="fas fa-lock"></i> Pagar $<span id="modal-amount-display">0.00</span>
                    </span>
                    <span id="modal-button-loading" style="display: none;">
                        <span class="spinner-border spinner-border-sm"></span> Procesando...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="savedCardPayModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 280px; margin-left: auto; margin-right: auto;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-1 px-3">
                <small class="modal-title font-weight-bold">
                    <i class="fas fa-lock"></i> Confirmar Pago
                </small>
                <button type="button" class="close text-white p-1" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body py-2 px-3">
                <div class="text-center mb-2">
                    <i id="savedCardPayIcon" class=""></i>
                    <span class="font-weight-bold" id="savedCardPayName"></span>
                    <small class="text-muted ml-1">&bull;&bull;&bull;&bull; <span id="savedCardPayLastFour"></span></small>
                </div>
                <div class="form-group mb-2">
                    <label class="small mb-1">CVV</label>
                    <input type="password" class="form-control form-control-sm text-center" id="savedCardPayCvv" 
                        placeholder="•••" maxlength="4" inputmode="numeric" autocomplete="off"
                        style="letter-spacing: 6px;">
                </div>
                <div class="form-group mb-0">
                    <label class="small mb-1">Monto</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" class="form-control" id="savedCardPayAmount" 
                            step="1.00" min="1.00">
                    </div>
                </div>
                <div id="savedCardPayError" class="alert alert-danger mt-2 mb-0 py-1 px-2" style="display: none;">
                    <small id="savedCardPayErrorText"></small>
                </div>
            </div>
            <div class="modal-footer py-1 px-3">
                <button type="button" class="btn btn-secondary btn-sm py-0 px-2" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-sm py-0 px-2" id="savedCardPayBtn">
                    <span id="savedCardPayBtnText"><i class="fas fa-check"></i> Pagar</span>
                    <span id="savedCardPayBtnLoading" style="display: none;">
                        <span class="spinner-border spinner-border-sm"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
