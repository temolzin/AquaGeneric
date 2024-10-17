<div class="modal fade" id="updateImage" tabindex="-1" role="dialog" aria-labelledby="updateImageLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="updateImageLabel">Actualizar Foto de Perfil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    @if ($authUser->getFirstMediaUrl('userGallery'))
                        <img id="currentProfileImage" src="{{ $authUser->getFirstMediaUrl('userGallery') }}" style="width: 150px; height: 150px; border-radius: 50%;" alt="Foto actual de {{ $authUser->name }}">
                    @else
                        <img id="currentProfileImage" src="{{ asset('img/userDefault.png') }}" style="width: 150px; height: 150px; border-radius: 50%;" alt="Foto actual de {{ $authUser->name }}">
                    @endif
                </div>
                <form action="{{ route('profile.update.image') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="profileImage">Seleccionar Imagen</label>
                        <input type="file" class="form-control" id="profileImage" name="profileImage" accept="image/*" required onchange="previewAndReplaceImage(event)">
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm({{ $authUser->id }})">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>    
        </div>
    </div>
</div>

<script>
    function previewAndReplaceImage(event) {
        const currentProfileImage = document.getElementById('currentProfileImage');
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
            currentProfileImage.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    function resetForm(id) {
        var form = document.querySelector('form[action="{{ route('profile.update.image') }}"]');
        form.reset();
        var currentProfileImage = document.getElementById('currentProfileImage');
        var defaultSrc = "{{ $authUser->getFirstMediaUrl('userGallery') ? $authUser->getFirstMediaUrl('userGallery') : asset('img/userDefault.png') }}";
        currentProfileImage.src = defaultSrc;
        var fileInput = document.getElementById('profileImage');
        fileInput.value = '';
    }
</script>
