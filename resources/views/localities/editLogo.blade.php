<div class="modal fade" id="editLogo{{ $locality->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Logo de Localidad <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <form action="{{ route('localities.updateLogo', $locality->id) }}" enctype="multipart/form-data" method="POST" id="edit-locality-form-{{ $locality->id }}">
                    @csrf
                    @method('POST')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Logo de la Localidad</h3>
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
                                            <label for="photo-{{ $locality->id }}" class="form-label"></label>
                                            <div class="image-preview-container" style="display: flex; justify-content: center; margin-bottom: 10px;">
                                                <img id="photo-preview-edit-{{ $locality->id }}" src="{{ $locality->getFirstMediaUrl('localityGallery') ? $locality->getFirstMediaUrl('localityGallery') : asset('img/localityDefault.png') }}"
                                                alt="Foto Actual" style="width: 120px; height: 120px; border-radius: 50%; margin-bottom: 5px;">
                                            </div>
                                            <input type="file" accept="image/*" class="form-control" name="photo" id="photo-{{ $locality->id }}" onchange="previewImageEdit(event, {{ $locality->id }})">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
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
</script>
