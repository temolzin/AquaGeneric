<div class="modal fade" id="editPaymentConfig" tabindex="-1" role="dialog" aria-labelledby="editPaymentConfigLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="editPaymentConfigLabel">
                    <i class="fas fa-credit-card mr-2"></i>Configuración de Pagos
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @if($authUser->hasRole(['Supervisor', 'Secretaria']))
            <form id="webhookConfigForm" action="{{ route('profile.webhook-config.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Configuración de Webhook:</strong> Configura aquí el correo y contraseña de tu cuenta de OpenPay para recibir notificaciones de pagos.
                    </div>
                    <div class="card">
                        <div class="card-header bg-secondary">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-shield-alt mr-2"></i>Autenticación del Webhook
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="openpay_webhook_user">
                                            Correo de OpenPay <span class="text-danger">*</span>
                                            @if($authUser->locality->openpay_webhook_user)
                                                <span class="badge badge-success ml-2"><i class="fas fa-check mr-1"></i>Configurado</span>
                                            @endif
                                        </label>
                                        <input type="email" class="form-control" name="openpay_webhook_user" id="openpay_webhook_user" placeholder="tu-email@example.com">
                                        <small class="text-muted">
                                            {{ $authUser->locality->openpay_webhook_user ? 'Deja en blanco para mantener el correo actual' : 'Ingresa el correo de tu cuenta de OpenPay' }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="openpay_webhook_password">
                                            Contraseña de OpenPay <span class="text-danger">*</span>
                                            @if($authUser->locality->openpay_webhook_password)
                                                <span class="badge badge-success ml-2"><i class="fas fa-check mr-1"></i>Configurada</span>
                                            @endif
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="openpay_webhook_password" id="openpay_webhook_password" placeholder="••••••••••••••••">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" onclick="toggleWebhookPassword()">
                                                    <i class="fas fa-eye" id="webhook-eye-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $authUser->locality->openpay_webhook_password ? 'Deja en blanco para mantener la contraseña actual' : 'Ingresa la contraseña de tu cuenta de OpenPay' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-3">
                                <i class="fas fa-info-circle mr-1"></i>
                                Estos datos se utilizan para autenticar las notificaciones de pagos desde OpenPay. No se mostrarán una vez guardados.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-info" onclick="testWebhookConnection()">
                        <i class="fas fa-plug mr-1"></i> Probar Conexión
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar Configuración
                    </button>
                </div>
            </form>
            @else
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Nota:</strong> Los Supervisores y Secretarias configuran el webhook desde sus perfiles.
                    </div>
                    <div class="card">
                        <div class="card-header bg-secondary">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-shield-alt mr-2"></i>Estado del Webhook
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($authUser->locality->openpay_webhook_user && $authUser->locality->openpay_webhook_password)
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function toggleWebhookPassword() {
        var input = document.getElementById('openpay_webhook_password');
        var icon = document.getElementById('webhook-eye-icon');
        if (!input) return;

        var isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !isPassword);
        icon.classList.toggle('fa-eye-slash', isPassword);
    }

    function testWebhookConnection() {
        Swal.fire({
            title: 'Probando conexión...',
            text: 'Por favor espera mientras verificamos la configuración',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ route("profile.webhook-config.test") }}',
            method: 'GET',
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Configuración válida',
                    text: response.message
                });
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

    document.addEventListener('DOMContentLoaded', function() {
        const webhookForm = document.getElementById('webhookConfigForm');
        if (webhookForm) {
            webhookForm.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Guardando configuración...',
                    text: 'Por favor espera mientras guardamos los datos',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: webhookForm.action,
                    method: 'POST',
                    data: new FormData(webhookForm),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Guardado!',
                            text: 'La configuración del webhook se ha guardado correctamente',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        let message = 'Error al guardar la configuración';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            });
        }
    });
</script>
