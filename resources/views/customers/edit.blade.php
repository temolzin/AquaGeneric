<div class="modal fade" id="edit{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Cliente <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <form action="{{ route('customers.update', $customer->id) }}" enctype="multipart/form-data" method="post" id="edit-customer-form-{{ $customer->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Datos del Cliente</h3>
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
                                            <label for="photo-{{ $customer->id }}" class="form-label"></label>
                                            <div class="image-preview-container" style="display: flex; justify-content: center; margin-bottom: 10px;">
                                                <img id="photo-preview-edit-{{ $customer->id }}" src="{{ $customer->getFirstMediaUrl('customerGallery') ? $customer->getFirstMediaUrl('customerGallery') : asset('img/userDefault.png') }}" 
                                                    alt="Foto Actual" style="width: 120px; height: 120px; border-radius: 50%; margin-bottom: 5px;">
                                            </div>
                                            <input type="file" accept="image/*" class="form-control" name="photo" id="photo-{{ $customer->id }}" onchange="previewImageEdit(event, {{ $customer->id }})">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="nameUpdate" class="form-label">Nombre(*)</label>
                                            <input type="text" class="form-control" name="nameUpdate" id="nameUpdate" value="{{ $customer->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="lastNameUpdate" class="form-label">Apellido(*)</label>
                                            <input type="text" class="form-control" name="lastNameUpdate" id="lastNameUpdate" value="{{ $customer->last_name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="blockUpdate" class="form-label">Bloque(*)</label>
                                            <input type="text" class="form-control" name="blockUpdate" id="blockUpdate" value="{{ $customer->block }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="streetUpdate" class="form-label">Calle(*)</label>
                                            <input type="text" class="form-control" name="streetUpdate" id="streetUpdate" value="{{ $customer->street }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="interiorNumberUpdate" class="form-label">Número Interior(*)</label>
                                            <input type="text" class="form-control" name="interiorNumberUpdate" id="interiorNumberUpdate" value="{{ $customer->interior_number }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="maritalStatusUpdate" class="form-label">Estado Civil(*)</label>
                                            <select class="form-control" id="maritalStatusUpdate" name="maritalStatusUpdate" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ $customer->marital_status == 1 ? 'selected' : '' }}>Casado</option>
                                                <option value="0" {{ $customer->marital_status == 0 ? 'selected' : '' }}>Soltero</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Estado del titular(*)</label>
                                            <select class="form-control" id="statusUpdate" name="statusUpdate">
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ $customer->status == 1 ? 'selected' : '' }}>Con Vida</option>
                                                <option value="0" {{ $customer->status == 0 ? 'selected' : '' }}>Fallecido</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12" id="responsibleNameUpdate">
                                        <div class="form-group">
                                            <label for="responsibleNameUpdate" class="form-label">Nombre de la persona que será responsable</label>
                                            <input type="text" class="form-control" name="responsibleNameUpdate" 
                                            placeholder="Nombre de la persona responsable si el titular fallecio, si no hay dejalo vacio"  id="responsibleNameUpdate" value="{{ $customer->responsible_name }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm({{ $customer->id }})">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImageEdit(event, id) {
        var input = event.target;
        var file = input.files[0];
        var reader = new FileReader();

        if (!file.type.startsWith('image/')) {
            Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, sube un archivo de imagen',
            confirmButtonText: 'Aceptar'
            });
            input.value = '';
            return;
        }

        reader.onload = function() {
            var dataURL = reader.result;
            var output = document.getElementById('photo-preview-edit-' + id);
            output.src = dataURL;
            output.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }

    function resetForm(id) {
        var form = document.getElementById('edit-customer-form-' + id);
        form.reset();
        var photoPreview = document.getElementById('photo-preview-edit-' + id);
        var customerGalleryUrl = "{{ $customer->getFirstMediaUrl('customerGallery') ? $customer->getFirstMediaUrl('customerGallery') : asset('img/userDefault.png') }}";
        photoPreview.src = customerGalleryUrl;
    }
</script>
