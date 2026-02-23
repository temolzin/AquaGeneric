<div class="modal fade" id="addCardModal" tabindex="-1" role="dialog" aria-labelledby="addCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title" id="addCardModalLabel">
                    <i class="fas fa-credit-card"> </i> Agregar Nueva Tarjeta
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted">Tarjetas de débito</small>
                            <div><img src="{{ asset('img/cards2.png') }}" alt="Débito" class="img-fluid" style="max-height: 35px;"></div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted">Tarjetas de crédito</small>
                            <div><img src="{{ asset('img/cards1.png') }}" alt="Crédito" style="max-height: 35px;"></div>
                        </div>
                    </div>
                </div>

                <form id="add-card-form">
                    @csrf
                    <input type="hidden" id="add-card-brand" name="brand">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-tag"></i> Alias (opcional)</label>
                                <input type="text" class="form-control" id="add-alias" name="alias"
                                    placeholder="Ej: Mi tarjeta personal" maxlength="50">
                                <small class="text-muted">Un nombre para identificar esta tarjeta</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Nombre del titular</label>
                                <input type="text" class="form-control" id="add-holder-name"
                                    placeholder="Como aparece en la tarjeta" autocomplete="off"
                                    data-openpay-card="holder_name" pattern="[A-Za-z ]+"
                                    title="Solo letras sin acentos" required>
                                <small class="text-muted">Solo letras sin acentos</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-credit-card"></i> Número de tarjeta</label>
                        <input type="text" class="form-control" id="add-card-number"
                            placeholder="•••• •••• •••• ••••" autocomplete="off" data-openpay-card="card_number"
                            maxlength="19" pattern="[0-9]{13,19}" inputmode="numeric"
                            title="Entre 13 y 19 dígitos numéricos" required>
                        <div id="add-card-brand-display" class="mt-1" style="min-height: 20px;"></div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label>Mes</label>
                                <select class="form-control" id="add-exp-month" data-openpay-card="expiration_month" required>
                                    <option value="">MM</option>
                                    <option value="01">01 - Enero</option>
                                    <option value="02">02 - Febrero</option>
                                    <option value="03">03 - Marzo</option>
                                    <option value="04">04 - Abril</option>
                                    <option value="05">05 - Mayo</option>
                                    <option value="06">06 - Junio</option>
                                    <option value="07">07 - Julio</option>
                                    <option value="08">08 - Agosto</option>
                                    <option value="09">09 - Septiembre</option>
                                    <option value="10">10 - Octubre</option>
                                    <option value="11">11 - Noviembre</option>
                                    <option value="12">12 - Diciembre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Año</label>
                                <input type="text" class="form-control" id="add-exp-year" placeholder="AA"
                                    maxlength="2" pattern="[0-9]{2}" inputmode="numeric"
                                    data-openpay-card="expiration_year" title="2 dígitos del año" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>CVV <i class="fas fa-question-circle text-muted" title="Código de seguridad en el reverso"></i></label>
                                <input type="text" class="form-control" id="add-cvv" placeholder="•••" maxlength="4"
                                    pattern="[0-9]{3,4}" inputmode="numeric" autocomplete="off"
                                    data-openpay-card="cvv2" title="3 o 4 dígitos numéricos" required>
                            </div>
                        </div>
                    </div>

                    <div id="add-card-error" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="add-card-error-text"></span>
                    </div>

                    <div class="openpay-footer text-center py-2 px-3 bg-light rounded">
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
                <button type="button" id="btn-save-card" class="btn btn-success">
                    <span id="btn-save-text">
                        <i class="fas fa-save"></i> Guardar Tarjeta
                    </span>
                    <span id="btn-save-loading" style="display: none;">
                        <span class="spinner-border spinner-border-sm"></span> Guardando...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
