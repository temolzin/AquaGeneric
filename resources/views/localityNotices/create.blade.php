<div class="modal fade" id="createNotice" tabindex="-1" role="dialog" aria-labelledby="createNoticeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Registrar Nuevo Aviso <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('localityNotices.store') }}" method="POST" enctype="multipart/form-data" id="createNoticeForm" onsubmit="return handleCreateSubmit(this)">
                    @csrf                
                    <input type="hidden" name="locality_id" value="{{ auth()->user()->locality_id }}">
                    <input type="hidden" name="is_active" value="1">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="title" class="form-label">Título (*)</label>
                                            <input type="text" class="form-control" id="title" name="title" placeholder="Ingrese el título del aviso" value="{{ old('title') }}" maxlength="100" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción (*)</label>
                                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Ingrese la descripción del aviso" required>{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="start_date" class="form-label">Fecha y Hora de Inicio (*)</label>
                                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="end_date" class="form-label">Fecha y Hora de Fin (*)</label>
                                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="attachment" class="form-label">Archivo Adjunto</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                                                    <label class="custom-file-label" for="attachment">Seleccionar archivo...</label>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG (Tamaño máximo: 10MB)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="createBtn">Guardar Aviso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.custom-file-input {
    opacity: 0;
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    cursor: pointer;
}

.custom-file {
    position: relative;
    width: 100%;
}

.custom-file-label {
    display: block;
    width: 100%;
    height: calc(2.25rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    cursor: pointer;
}

.custom-file-label::after {
    content: "Examinar";
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    z-index: 3;
    display: block;
    height: calc(2.25rem + 2px);
    padding: 0.375rem 0.75rem;
    line-height: 1.5;
    color: #495057;
    background-color: #e9ecef;
    border-left: 1px solid #ced4da;
    border-radius: 0 0.25rem 0.25rem 0;
}

.input-group:hover .custom-file-label {
    border-color: #80bdff;
}

.input-group:hover .custom-file-label::after {
    background-color: #dae0e5;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('attachment');
    const fileLabel = document.querySelector('.custom-file-label');

    function formatDateTime(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    const startDateInput = document.getElementById('start_date');
    if (startDateInput) {
        const now = new Date();
        startDateInput.min = formatDateTime(now);
        
        startDateInput.value = formatDateTime(now);

        startDateInput.addEventListener('change', function() {
            const selectedStartDate = new Date(this.value);
            const minEndDate = new Date(selectedStartDate);
            minEndDate.setMinutes(minEndDate.getMinutes() + 30); // Mínimo 30 minutos después
            
            const endDateInput = document.getElementById('end_date');
            if (endDateInput) {
                endDateInput.min = formatDateTime(minEndDate);
                
                const currentEndDate = new Date(endDateInput.value);
                if (endDateInput.value && currentEndDate <= selectedStartDate) {
                    endDateInput.value = '';
                }
            }
        });
    }

    const endDateInput = document.getElementById('end_date');
    if (endDateInput) {
        const now = new Date();
        const minEndDate = new Date(now);
        minEndDate.setMinutes(minEndDate.getMinutes() + 30);
        endDateInput.min = formatDateTime(minEndDate);
        
        const defaultEndDate = new Date(now);
        defaultEndDate.setDate(defaultEndDate.getDate() + 1);
        endDateInput.value = formatDateTime(defaultEndDate);
    }

    fileInput.addEventListener('change', function (event) {
        const input = event.target;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        const file = input.files[0];

        if (file) {
            const fileType = file.type;
            const fileName = file.name.toLowerCase();
            const validExtensions = ['.jpg', '.jpeg', '.png', '.pdf'];
            const isValidExtension = validExtensions.some(ext => fileName.endsWith(ext));

            if (!allowedTypes.includes(fileType) || !isValidExtension) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor, sube un archivo PDF o una imagen (JPG, JPEG, PNG).',
                    confirmButtonText: 'Aceptar'
                });
                input.value = '';
                fileLabel.textContent = 'Seleccionar archivo...';
                return;
            }

            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El archivo es demasiado grande. Tamaño máximo permitido: 10MB.',
                    confirmButtonText: 'Aceptar'
                });
                input.value = '';
                fileLabel.textContent = 'Seleccionar archivo...';
                return;
            }

            fileLabel.textContent = file.name;
        } else {
            fileLabel.textContent = 'Seleccionar archivo...';
        }
    });

    $('#createNotice').on('hidden.bs.modal', function () {
        document.getElementById('createNoticeForm').reset();
        fileLabel.textContent = 'Seleccionar archivo...';
        
        const now = new Date();
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (startDateInput) {
            startDateInput.value = formatDateTime(now);
        }
        if (endDateInput) {
            const defaultEndDate = new Date(now);
            defaultEndDate.setDate(defaultEndDate.getDate() + 1);
            endDateInput.value = formatDateTime(defaultEndDate);
        }
        
        var createBtn = document.getElementById('createBtn');
        if (createBtn) {
            createBtn.disabled = false;
            createBtn.innerHTML = 'Guardar Aviso';
        }
    });
});

function handleCreateSubmit(form) {
    var submitBtn = document.getElementById('createBtn');
    
    if (submitBtn.disabled) {
        return false;
    }
    
    var startDateValue = document.getElementById('start_date').value;
    var endDateValue = document.getElementById('end_date').value;

    var startDate = new Date(startDateValue);
    var endDate = new Date(endDateValue);

    if (endDate <= startDate) {
        Swal.fire({
            icon: 'error',
            title: 'Error en fechas',
            text: 'La fecha y hora de fin debe ser posterior a la fecha y hora de inicio.'
        });
        return false;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Guardando...`;
    
    return true;
}
</script>
