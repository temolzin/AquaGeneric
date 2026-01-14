<div class="modal fade" id="changeAmountModal{{ $component->id }}" tabindex="-1" role="dialog"
    aria-labelledby="changeAmountModalLabel{{ $component->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header bg-purple">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Cambiar Cantidad <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('inventory.updateAmount') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Datos del Cambio de Cantidad</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Componente</label>
                                            <p class="form-control-plaintext mb-0 font-weight-bold">
                                                {{ $component->name }}</p>
                                            <input type="hidden" name="inventory_id" value="{{ $component->id }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Cantidad Actual</label>
                                            <p class="form-control-plaintext mb-0">
                                                <span class="badge bg-info">{{ $component->amount }} unidades</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="amount" class="form-label">Nueva Cantidad (*)</label>
                                            <input type="number" class="form-control" name="amount" min="0" max="10000" required
                                                placeholder="Ingresa la nueva cantidad">
                                            <small class="form-text text-muted">
                                                La diferencia se calculará automáticamente
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción (*)</label>
                                            <textarea class="form-control" name="description"
                                                placeholder="¿Por qué se cambia la cantidad? Ej: Se usaron 5 unidades para reparación"
                                                rows="3" required></textarea>
                                            <small class="form-text text-muted">
                                                Describe el motivo del cambio (salida, entrada, ajuste, etc.)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar Cambio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector('.imageInput');
        const container = document.querySelector('.imageButtonsContainer');
        const modalImg = document.getElementById('multiModalImagePreview');
        const saveBtn = document.getElementById('saveAmount');
        const form = document.getElementById('changeAmountForm');

        input.addEventListener('change', function () {
            container.innerHTML = '';
            Array.from(this.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-sm btn-info mr-2 mb-2';
                        btn.textContent = 'Ver imagen';
                        btn.dataset.toggle = 'modal';
                        btn.dataset.target = '#multiImagePreviewModal';
                        btn.dataset.imageSrc = e.target.result;

                        btn.addEventListener('click', function () {
                            modalImg.src = this.dataset.imageSrc;
                        });

                        container.appendChild(btn);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        saveBtn.addEventListener('click', function () {
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    const amount = data.success ? 'success' : 'error';
                    const title = data.success ? 'Cantidad actualizado' : 'Error';
                    const icon = amount;
                    const message = data.message;

                    Swal.fire({
                        icon: icon,
                        title: title,
                        text: message,
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (data.success && result.isConfirmed) {
                            location.reload();
                        }
                    });

                    if (data.success) {
                        $('#createResponsible').modal('hide');
                        form.reset();
                        container.innerHTML = '';
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor.'
                    });
                });
        });
    });
</script>