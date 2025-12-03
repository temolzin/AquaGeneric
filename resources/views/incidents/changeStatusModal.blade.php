<div class="modal fade" id="createResponsible" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header bg-purple">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Cambiar Estatus <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="changeStatusForm" action="{{ route('incidents.updateStatus') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Datos del Cambio de Estatus</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="incident_name" class="form-label">Incidencia</label>
                                            <p class="form-control-plaintext mb-0" id="incidentNameDisplay"></p>
                                            <input type="hidden" name="incident_id" id="incidentId">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="employee" class="form-label">Responsable(*)</label>
                                            <select class="form-control select2" name="employee" required>
                                                <option value="">Selecciona una opción</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}">
                                                        {{ $employee->name }} - {{ $employee->rol }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Estatus(*)</label>
                                            <select class="form-control select2" name="status_id" id="status_id" required>
                                                <option value="">Selecciona una opción</option>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status->id }}">{{ $status->status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción</label>
                                            <textarea class="form-control" name="description" placeholder="Agrega una descripción" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="imagesInput" class="form-label">Imágenes de referencia <small class="text-muted">(opcional)</small></label>
                                            <input type="file" class="imageInput form-control" name="images[]" multiple accept="image/*">
                                            <small class="form-text text-muted mt-1">
                                                Puedes subir varias imágenes para dar contexto. Formatos permitidos: JPG, PNG.
                                            </small>
                                            <div class="imageButtonsContainer mt-3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" id="saveStatus" class="btn btn-success">Guardar</button>
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
        const saveBtn = document.getElementById('saveStatus');
        const form = document.getElementById('changeStatusForm');

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
                const status = data.success ? 'success' : 'error';
                const title = data.success ? 'Estatus actualizado' : 'Error';
                const icon = status;
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
