<div class="modal fade" id="edit{{ $employee->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Empleado <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal"
                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <form action="{{ route('employees.update', $employee->id) }}" enctype="multipart/form-data"
                    method="post" id="edit-employee-form-{{ $employee->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Datos del Empleado</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-8 offset-lg-2">
                                        <div class="form-group text-center">
                                            <label for="photo-{{ $employee->id }}" class="form-label"></label>
                                            <div class="image-preview-container"
                                                style="display: flex; justify-content: center; margin-bottom: 10px;">
                                                <img id="photo-preview-edit-{{ $employee->id }}"
                                                    src="{{ $employee->getFirstMediaUrl('employeeGallery') ? $employee->getFirstMediaUrl('employeeGallery') : asset('img/userDefault.png') }}"
                                                    alt="Foto Actual"
                                                    style="width: 120px; height: 120px; border-radius: 50%; margin-bottom: 5px;">
                                            </div>
                                            <input type="file" accept="image/*" class="form-control" name="photo"
                                                id="photo-{{ $employee->id }}"
                                                onchange="previewImageEdit(event, {{ $employee->id }})">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="nameUpdate" class="form-label">Nombre(*)</label>
                                            <input type="text" class="form-control" name="nameUpdate" id="nameUpdate"
                                                value="{{ $employee->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="lastNameUpdate" class="form-label">Apellido(*)</label>
                                            <input type="text" class="form-control" name="lastNameUpdate"
                                                id="lastNameUpdate" value="{{ $employee->last_name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="streetUpdate" class="form-label">Calle(*)</label>
                                            <input type="text" class="form-control" name="streetUpdate"
                                                id="streetUpdate" value="{{ $employee->street }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="blockUpdate" class="form-label">Colonia(*)</label>
                                            <input type="text" class="form-control" name="blockUpdate" id="blockUpdate"
                                                value="{{ $employee->block }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="localityUpdate" class="form-label">Localidad(*)</label>
                                            <input type="text" class="form-control" name="localityUpdate"
                                                id="localityUpdate" value="{{ $employee->locality }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="stateUpdate" class="form-label">Estado(*)</label>
                                            <input type="text" class="form-control" name="stateUpdate" id="stateUpdate"
                                                value="{{ $employee->state }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="zipCodeUpdate" class="form-label">Código Postal(*)</label>
                                            <input type="text" class="form-control" name="zipCodeUpdate"
                                                id="zipCodeUpdate" value="{{ $employee->zip_code }}" maxlength="5"
                                                pattern="\d{5}" inputmode="numeric" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="exteriorNumberUpdate" class="form-label">Número
                                                Exterior(*)</label>
                                            <input type="text" class="form-control" name="exteriorNumberUpdate"
                                                id="exteriorNumberUpdate" value="{{ $employee->exterior_number }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="interiorNumberUpdate" class="form-label">Número
                                                Interior(*)</label>
                                            <input type="text" class="form-control" name="interiorNumberUpdate"
                                                id="interiorNumberUpdate" value="{{ $employee->interior_number }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="emailUpdate" class="form-label">Correo Electrónico(*)</label>
                                            <input type="email" class="form-control" name="emailUpdate" id="emialUpdate"
                                                value="{{ $employee->email }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="phoneNumberUpdate" class="form-label">Número
                                                Telefónico(*)</label>
                                            <input type="text" class="form-control" name="phoneNumberUpdate"
                                                id="phoneNumberUpdate" value="{{ $employee->phone_number }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="salaryUpdate" class="form-label">Salario(*)</label>
                                            <input type="text" class="form-control" name="salaryUpdate"
                                                id="salaryUpdate" value="{{ $employee->salary }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            onclick="resetForm({{ $employee->id }})">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImageEdit(event, id) 
    {
        var input = event.target;
        var file = input.files[0];
        var reader = new FileReader();

        if (!file.type.startsWith('image/')) 
        {
            Swal.fire
            ({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, sube un archivo de imagen',
                confirmButtonText: 'Aceptar'
            });
            input.value = '';
            return;
        }

        reader.onload = function () 
        {
            var dataURL = reader.result;
            var output = document.getElementById('photo-preview-edit-' + id);
            output.src = dataURL;
            output.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }

    function resetForm(id) 
    {
        var form = document.getElementById('edit-employee-form-' + id);
        form.reset();
        var photoPreview = document.getElementById('photo-preview-edit-' + id);
        var employeeGalleryUrl = "{{ $employee->getFirstMediaUrl('employeeGallery') ? $employee->getFirstMediaUrl('employeeGallery') : asset('img/userDefault.png') }}";
        photoPreview.src = employeeGalleryUrl;
    }

    document.addEventListener('DOMContentLoaded', function () 
    {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => 
        {
            modal.addEventListener('show.bs.modal', function () 
            {
                const employeeId = modal.id.replace('edit', '');
                initializeModal(employeeId);
            });
        });
    });
</script>
