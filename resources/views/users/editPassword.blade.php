<div class="modal fade" id="editPassword{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-primary">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Contraseña <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <form action="{{ route('users.updatePassword', $user->id) }}" enctype="multipart/form-data" method="POST" id="edit-user-form-{{ $user->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Editar contraseña de {{ $user->name }} {{ $user->last_name }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-6 mx-auto">
                                <div class="form-group">
                                    <label for="updatePassword" class="form-label">Nueva contraseña(*)</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="input form-control" name="updatePassword" id="updatePassword{{ $user->id }}" placeholder="Ingresa una nueva contraseña" required aria-label="updatePassword" aria-describedby="basic-addon1">
                                        <div class="input-group-append" onclick="password_show_hide('updatePassword{{ $user->id }}', 'show_eye_update{{ $user->id }}', 'hide_eye_update{{ $user->id }}');">
                                            <span class="input-group-text">
                                                <i class="fas fa-eye" id="show_eye_update{{ $user->id }}"></i>
                                                <i class="fas fa-eye-slash d-none" id="hide_eye_update{{ $user->id }}"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mx-auto">
                                <div class="form-group">
                                    <label for="passwordConfirmation" class="form-label">Confirmar contraseña(*)</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="input form-control" name="passwordConfirmation" id="passwordConfirmation{{ $user->id }}" placeholder="Ingresa nuevamente la contraseña" required aria-label="passwordConfirmation" aria-describedby="basic-addon1">
                                        <div class="input-group-append" onclick="password_show_hide('passwordConfirmation{{ $user->id }}', 'show_eye_confirmation{{ $user->id }}', 'hide_eye_confirmation{{ $user->id }}');">
                                            <span class="input-group-text">
                                                <i class="fas fa-eye" id="show_eye_confirmation{{ $user->id }}"></i>
                                                <i class="fas fa-eye-slash d-none" id="hide_eye_confirmation{{ $user->id }}"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="clearInputs('{{ $user->id }}')">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .form-group label {
        margin-bottom: 5px; 
        display: block; 
    }
    .input-group {
        margin-bottom: 15px; 
    }
</style>

<script>
    function password_show_hide(inputId, showEyeId, hideEyeId) {
        var input = document.getElementById(inputId);
        var showEye = document.getElementById(showEyeId);
        var hideEye = document.getElementById(hideEyeId);

        if (input.type === "password") {
            input.type = "text";
            showEye.classList.add("d-none");
            hideEye.classList.remove("d-none");
        } else {
            input.type = "password";
            showEye.classList.remove("d-none");
            hideEye.classList.add("d-none");
        }
    }

    function clearInputs(userId) {
        document.getElementById('updatePassword' + userId).value = '';
        document.getElementById('passwordConfirmation' + userId).value = '';
    }
</script>
