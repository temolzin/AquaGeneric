<div class="modal fade" id="edit{{$notice->id}}" tabindex="-1" role="dialog" aria-labelledby="editLabel{{$notice->id}}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Aviso <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('localityNotices.update', $notice->id) }}" method="POST" enctype="multipart/form-data" id="edit-notice-form-{{$notice->id}}"onsubmit="return handleUpdateSubmit(this, {{$notice->id}})">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="title_edit{{$notice->id}}" class="form-label">Título (*)</label>
                                            <input type="text" class="form-control" id="title_edit{{$notice->id}}" name="title" value="{{ $notice->title }}" maxlength="100" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description_edit{{$notice->id}}" class="form-label">Descripción (*)</label>
                                            <textarea class="form-control" id="description_edit{{$notice->id}}" name="description" rows="4" required>{{ $notice->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="start_date_edit{{$notice->id}}" class="form-label">Fecha de Inicio (*)</label>
                                            <input type="date" class="form-control" id="start_date_edit{{$notice->id}}" name="start_date" value="{{ $notice->start_date->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="end_date_edit{{$notice->id}}" class="form-label">Fecha de Fin (*)</label>
                                            <input type="date" class="form-control" id="end_date_edit{{$notice->id}}" name="end_date" value="{{ $notice->end_date->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="attachment_edit{{$notice->id}}" class="form-label">Archivo Adjunto</label>
                                            @if($notice->getFirstMedia('notice_attachments'))
                                                @php
                                                    $media = $notice->getFirstMedia('notice_attachments');
                                                @endphp
                                                <div class="alert alert">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <a href="{{ route('localityNotices.download', $notice->id) }}" class="btn btn-primary" target="_blank">
                                                            <i class="fas fa-eye"></i> Ver archivo
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning py-2">
                                                    <i class="fas fa-info-circle mr-2"></i>No hay archivo adjunto actualmente
                                                </div>
                                            @endif
                                            
                                            <div class="input-group mt-2">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="attachment_edit{{$notice->id}}" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                                                    <label class="custom-file-label" for="attachment_edit{{$notice->id}}">
                                                        {{ $notice->getFirstMedia('notice_attachments') ? 'Seleccionar nuevo archivo' : 'Seleccionar archivo' }}
                                                    </label>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">
                                                Dejar vacío para mantener el archivo actual. Formatos: PDF, JPG, PNG (Max: 10MB)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning" id="updateBtn{{$notice->id}}">Actualizar Aviso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editFileInput = document.getElementById('attachment_edit{{$notice->id}}');
    const editFileLabel = document.querySelector('#edit{{$notice->id}} .custom-file-label');
    
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    const startDateEdit = document.getElementById('start_date_edit{{$notice->id}}');
    const endDateEdit = document.getElementById('end_date_edit{{$notice->id}}');

    if (startDateEdit && endDateEdit) {
        startDateEdit.min = today.toISOString().split('T')[0];
        
        const currentStartDate = new Date(startDateEdit.value);
        const minEndDate = new Date(currentStartDate);
        minEndDate.setDate(minEndDate.getDate() + 1);
        
        if (minEndDate > today) {
            endDateEdit.min = minEndDate.toISOString().split('T')[0];
        } else {
            endDateEdit.min = tomorrow.toISOString().split('T')[0];
        }

        startDateEdit.addEventListener('change', function() {
            const selectedStartDate = new Date(this.value);
            const newMinEndDate = new Date(selectedStartDate);
            newMinEndDate.setDate(newMinEndDate.getDate() + 1);
            endDateEdit.min = newMinEndDate.toISOString().split('T')[0];
            
            const currentEndDate = new Date(endDateEdit.value);
            if (endDateEdit.value && currentEndDate <= selectedStartDate) {
                endDateEdit.value = '';
            }
        });

        endDateEdit.addEventListener('change', function() {
            const startDate = new Date(startDateEdit.value);
            const endDate = new Date(this.value);
            
            if (endDate <= startDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en fecha',
                    text: 'La fecha de fin debe ser posterior a la fecha de inicio.',
                    confirmButtonText: 'Aceptar'
                });
                this.value = '';
            }
        });
    }

    if (editFileInput) {
        editFileInput.addEventListener('change', function (event) {
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
                    editFileLabel.textContent = 'Seleccionar archivo...';
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
                    editFileLabel.textContent = 'Seleccionar archivo...';
                    return;
                }

                editFileLabel.textContent = file.name;
                
            } else {
                editFileLabel.textContent = 'Seleccionar archivo...';
            }
        });
    }

    $('#edit{{$notice->id}}').on('hidden.bs.modal', function () {
        document.getElementById('edit-notice-form-{{$notice->id}}').reset();
        
        if (editFileLabel) {
            const hasExistingFile = {{ $notice->getFirstMedia('notice_attachments') ? 'true' : 'false' }};
            editFileLabel.textContent = hasExistingFile ? 'Seleccionar nuevo archivo' : 'Seleccionar archivo';
        }
        
        var updateBtn = document.getElementById('updateBtn{{$notice->id}}');
        if (updateBtn) {
            updateBtn.disabled = false;
            updateBtn.innerHTML = 'Actualizar Aviso';
        }
    });
});

function handleUpdateSubmit(form, noticeId) {
    var submitBtn = document.getElementById('updateBtn' + noticeId);
    
    if (submitBtn.disabled) {
        return false;
    }
    
    var startDateValue = document.getElementById('start_date_edit' + noticeId).value;
    var endDateValue = document.getElementById('end_date_edit' + noticeId).value;

    if (!startDateValue || !endDateValue) {
        Swal.fire({
            icon: 'error',
            title: 'Fechas incompletas',
            text: 'Por favor, complete ambas fechas.',
            confirmButtonText: 'Aceptar'
        });
        return false;
    }
    
    var startDate = new Date(startDateValue);
    var endDate = new Date(endDateValue);
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Actualizando...`;

    return true;
}
</script>
