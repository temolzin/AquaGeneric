<div class="modal fade" id="editPassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Contraseña <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <form action="{{ route('profile.updatePassword') }}" enctype="multipart/form-data" method="POST" id="edit-user-form">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="col-lg-6 mx-auto">
                            <div class="form-group">
                                <label for="oldPassword" class="form-label">Contraseña anterior(*)</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="input form-control" name="oldPassword" id="oldPassword" placeholder="Ingresa contraseña anterior" required aria-label="oldPassword" aria-describedby="basic-addon1">
                                    <div class="input-group-append" onclick="password_show_hide('oldPassword', 'show_eye_old', 'hide_eye_old');">
                                        <span class="input-group-text">
                                            <i class="fas fa-eye" id="show_eye_old"></i>
                                            <i class="fas fa-eye-slash d-none" id="hide_eye_old"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mx-auto">
                            <div class="form-group">
                                <label for="updatePassword" class="form-label">Nueva contraseña(*)</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="input form-control" name="updatePassword" id="updatePassword" placeholder="Ingresa una nueva contraseña" required aria-label="updatePassword" aria-describedby="basic-addon1">
                                    <div class="input-group-append" onclick="password_show_hide('updatePassword', 'show_eye_new', 'hide_eye_new');">
                                        <span class="input-group-text">
                                            <i class="fas fa-eye" id="show_eye_new"></i>
                                            <i class="fas fa-eye-slash d-none" id="hide_eye_new"></i>
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
                                    <input type="password" class="input form-control" name="passwordConfirmation" id="passwordConfirmation" placeholder="Ingresa nuevamente la contraseña" required aria-label="passwordConfirmation" aria-describedby="basic-addon1">
                                    <div class="input-group-append" onclick="password_show_hide('passwordConfirmation', 'show_eye_confirmation', 'hide_eye_confirmation');">
                                        <span class="input-group-text">
                                            <i class="fas fa-eye" id="show_eye_confirmation"></i>
                                            <i class="fas fa-eye-slash d-none" id="hide_eye_confirmation"></i>
                                        </span>
                                    </div>
                                </div>
                                @if ($errors->has('passwordConfirmation'))
                                    <span class="text-danger">{{ $errors->first('passwordConfirmation') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm()">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

    function resetForm() {
        document.getElementById('edit-user-form').reset();
    }
</script>
