<div class="modal fade" id="openpayConfigModal{{ $locality->id }}" tabindex="-1" role="dialog" aria-labelledby="openpayConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-primary">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">
                            <i class="fas fa-credit-card mr-2"></i>
                            Configuración OpenPay - {{ $locality->name }}
                        </h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('localities.openpay.update', $locality->id) }}" method="POST" id="openpay-config-form-{{ $locality->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="alert {{ $locality->hasOpenPayEnabled() ? 'alert-success' : 'alert-warning' }} mb-4">
                            <i class="fas {{ $locality->hasOpenPayEnabled() ? 'fa-check-circle' : 'fa-exclamation-triangle' }} mr-2"></i>
                            {{ $locality->hasOpenPayEnabled() ? 'OpenPay está configurado y habilitado para esta localidad.' : 'OpenPay no está configurado o habilitado para esta localidad.' }}
                        </div>
                        <div class="card mb-3">
                            <div class="card-header py-2 bg-info">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-link mr-2"></i>URL del Webhook
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2 text-muted small">
                                    Configura esta URL en tu panel de OpenPay para recibir notificaciones de pagos:
                                </p>
                                <div class="input-group">
                                    <input type="text" class="form-control" readonly value="{{ route('openpay.webhook') }}" id="webhook-url-{{ $locality->id }}">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyWebhookUrl({{ $locality->id }})" title="Copiar URL">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    El webhook es único para todas las localidades. El sistema identifica automáticamente la localidad por la transacción.
                                </small>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header py-2 bg-secondary">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-key mr-2"></i>Credenciales de API
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="openpay_merchant_id_{{ $locality->id }}">
                                                ID de Comercio <span class="text-danger">*</span>
                                            </label>
                                            <input  type="text" class="form-control" name="openpay_merchant_id" id="openpay_merchant_id_{{ $locality->id }}" value="{{ $locality->openpay_merchant_id }}" placeholder="Ej: m4xxxxxxxxxxxxxxxxxx">
                                            <small class="text-muted">ID del comercio proporcionado por OpenPay</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="openpay_public_key_{{ $locality->id }}">
                                                API key pública <span class="text-danger">*</span>
                                            </label>
                                            <input  type="text" class="form-control" name="openpay_public_key" id="openpay_public_key_{{ $locality->id }}" value="{{ $locality->openpay_public_key }}" placeholder="Ej: pk_xxxxxxxxxxxxxxxxxx">
                                            <small class="text-muted">Llave pública para el frontend</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="openpay_private_key_{{ $locality->id }}">
                                                Private Key <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input  type="password" class="form-control" name="openpay_private_key" id="openpay_private_key_{{ $locality->id }}" placeholder="{{ $locality->openpay_private_key ? '••••••••••••••••' : 'Ej: sk_xxxxxxxxxxxxxxxxxx' }}">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePrivateKey({{ $locality->id }})">
                                                        <i class="fas fa-eye" id="eye-icon-{{ $locality->id }}"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ $locality->openpay_private_key ? 'Deja en blanco para mantener la llave actual' : 'Llave privada para el backend (se guarda de forma segura)' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header py-2 bg-secondary">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-shield-alt mr-2"></i>Autenticación del Webhook
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($locality->openpay_webhook_user && $locality->openpay_webhook_password)
                                    <div class="alert alert-success mb-0">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        <strong>Webhook Configurado</strong>
                                        <p class="mb-0 mt-2 small">Los Supervisores o Secretarias han configurado el webhook para esta localidad. Si necesitas cambiar estas credenciales, solicita que lo hagan desde sus perfiles.</p>
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        <strong>Webhook No Configurado</strong>
                                        <p class="mb-0 mt-2 small">Los Supervisores o Secretarias deben configurar el correo y contraseña de OpenPay desde sus perfiles para activar las notificaciones de webhooks.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-cog mr-2"></i>Opciones
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="openpay_sandbox" id="openpay_sandbox_{{ $locality->id }}" value="1" {{ $locality->openpay_sandbox ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="openpay_sandbox_{{ $locality->id }}">
                                                <i class="fas fa-flask mr-1"></i> Modo Sandbox (Pruebas)
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            Activa este modo para realizar pruebas sin cobros reales
                                        </small>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="openpay_enabled" id="openpay_enabled_{{ $locality->id }}" value="1" {{ $locality->openpay_enabled ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="openpay_enabled_{{ $locality->id }}">
                                                <i class="fas fa-credit-card mr-1"></i> Habilitar pagos en línea
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            Permite a los clientes pagar con tarjeta desde el sistema
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-info" onclick="testOpenPayConnection({{ $locality->id }})">
                            <i class="fas fa-plug mr-1"></i> Probar Conexión
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePrivateKey(localityId) {
        var input = document.getElementById('openpay_private_key_' + localityId);
        var icon = document.getElementById('eye-icon-' + localityId);
        var isPassword = input.type === 'password';
        
        input.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !isPassword);
        icon.classList.toggle('fa-eye-slash', isPassword);
    }

    function copyWebhookUrl(localityId) {
        var input = document.getElementById('webhook-url-' + localityId);
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        Swal.fire({
            icon: 'success',
            title: 'URL copiada',
            text: 'La URL del webhook ha sido copiada al portapapeles',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }

    function testOpenPayConnection(localityId) {
    Swal.fire({
        title: 'Probando conexión...',
        text: 'Por favor espera mientras verificamos la conexión con OpenPay',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/localities/' + localityId + '/openpay/test',
        method: 'GET',
        success: function(response) {
            var config = response.success ? {
                icon: 'success',
                title: 'Conexión exitosa',
                html: `
                    <p>${response.message}</p>
                    <p class="mb-0"><strong>ID de Comercio:</strong> ${response.merchant_id}</p>
                    <p class="mb-0"><strong>Modo:</strong> ${response.sandbox ? 'Pruebas' : 'Producción'}</p>
                `
            } : {
                icon: 'error',
                title: 'Error de conexión',
                text: response.message
            };
            Swal.fire(config);
        },
        error: function(xhr) {
            var message = 'Error al conectar con el servidor';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
    });
}
</script>
