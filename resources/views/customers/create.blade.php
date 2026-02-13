
<div class="modal fade" id="createCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Agregar cliente <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('customers.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Ingrese los Datos del Cliente</h3>
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
                                            <div class="image-preview-container" style="display: flex; justify-content: center; margin-top: 10px;">
                                                <img id="photo-preview" src="#" alt="Vista previa de la foto" style="display: none; width: 120px; height: 120px; border-radius: 50%; margin-bottom: 5px;">
                                            </div>
                                            <input type="file" accept="image/*" class="form-control" name="photo" id="photo" aria-describedby="photoHelp" onchange="previewImage(event)" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nombre(*)</label>
                                            <input type="text" pattern="^(?!\s*$)(?!.*\d)[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$" class="form-control" id="name" name="name" placeholder="Ingresa nombre" value="{{ old('name') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="last_name" class="form-label">Apellido(*)</label>
                                            <input type="text" pattern="^(?!\s*$)(?!.*\d)[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$" class="form-control" id="last_name" name="last_name" placeholder="Ingresa apellido" value="{{ old('last_name') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="street" class="form-label">Calle(*)</label>
                                            <input type="text" pattern=".*\S.*" class="form-control" id="street" name="street" placeholder="Ingresa calle" value="{{ old('street') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="block" class="form-label">Colonia(*)</label>
                                            <input type="text" pattern=".*\S.*" class="form-control" id="block" name="block" placeholder="Ingresa colonia" value="{{ old('block') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="locality" class="form-label">Localidad(*)</label>
                                            <input type="text" pattern=".*\S.*" class="form-control" id="locality" name="locality" placeholder="Ingresa localidad" value="{{ old('locality') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="state" class="form-label">Estado(*)</label>
                                            <input type="text" pattern="^(?!\s*$)(?!.*\d)[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$" class="form-control" id="state" name="state" placeholder="Ingresa estado" value="{{ old('state') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Correo electrónico(*)</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Ingresa correo electronico" value="{{ old('email') }}" required />
                                            @error('email')
                                                <span class="error invalid-feedback" style="display:block;">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="zip_code" class="form-label">Código Postal(*)</label>
                                            <input type="text" pattern=".*\S.*" class="form-control" id="zip_code" name="zip_code" placeholder="Ingresa código postal" value="{{ old('zip_code') }}" maxlength="5" pattern="\d{5}" inputmode="numeric" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="exterior_number" class="form-label">Número Exterior(*)</label>
                                            <input type="text" pattern=".*\S.*" class="form-control" id="exterior_number" name="exterior_number" placeholder="Ingresa número exterior" value="{{ old('exterior_number') }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="interior_number" class="form-label">Número Interior(*)</label>
                                            <input type="text" pattern=".*\S.*" class="form-control" id="interior_number" name="interior_number" placeholder="Ingresa número interior" value="{{ old('interior_number') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="marital_status" class="form-label">Estado Civil(*)</label>
                                            <select class="form-control" id="marital_status" name="marital_status" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ old('marital_status') == '1' ? 'selected' : '' }}>Casado</option>
                                                <option value="0" {{ old('marital_status') == '0' ? 'selected' : '' }}>Soltero</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Estado del titular(*)</label>
                                            <select class="form-control" id="status" name="status" required onchange="toggleResponsibleField()">
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Con Vida</option>
                                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Fallecido</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12" id="responsible_field" style="display: none;">
                                        <div class="form-group">
                                            <label for="responsible_name" class="form-label">Nombre de la persona que será responsable de la toma</label>
                                            <input type="text" pattern="^(?!\s*$)(?!.*\d)[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$" class="form-control" id="responsible_name" name="responsible_name" placeholder="Ingresa nombre de la persona responsable, si no hay dejalo vacio" value="{{ old('responsible_name') }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input class="custom-control-input" type="checkbox" id="showPassword" name="showPassword">
                                                <label class="custom-control-label" for="showPassword">
                                                    Habilitar Inicio de Sesión
                                                </label>
                                            </div>
                                            <div class="password-field" id="passwordField" style="display: none;">
                                                <label for="password" class="form-label required">Ingresa una Contraseña</label>
                                                <div class="input-group">
                                                    <input type="password" id="password" name="password" placeholder="Contraseña"  class="form-control">
                                                    <button type="button" id="generatePasswordBtn" class="btn btn-default border">
                                                        Generar Contraseña
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="note" class="form-label">Nota</label>
                                            <textarea class="form-control" id="note" name="note" placeholder="Ingresa una nota"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="save" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
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

    reader.onload = function(){
        var output = document.getElementById('photo-preview');
        output.src = reader.result;
        output.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}

    function toggleResponsibleField() {
        var statusSelect = document.getElementById('status');
        var responsibleField = document.getElementById('responsible_field');

        if (statusSelect.value == '0') {
            responsibleField.style.display = 'block';
        } else {
            responsibleField.style.display = 'none';
        }
    }

    const createCustomerForm = document.getElementById('createCustomer');

        if (createCustomerForm) {
            createCustomerForm.addEventListener('submit', function(e) {
                const submitButtons = createCustomerForm.querySelectorAll('button[type="submit"], input[type="submit"]');
                submitButtons.forEach(button => {
                    button.disabled = true;

                    if (button.innerHTML) {
                        button.innerHTML = 'Guardando...';
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const showPasswordCheckbox = document.getElementById('showPassword');
            const passwordField = document.getElementById('passwordField');
            const passwordInput = document.getElementById('password');
            const generatePasswordBtn = document.getElementById('generatePasswordBtn');

            showPasswordCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    passwordField.style.display = 'block';
                    passwordInput.required = true;
                } else{
                    passwordField.style.display = 'none';
                    passwordInput.required = false;
                    passwordInput.value = '';
                }
        });

        generatePasswordBtn.addEventListener('click', function() {
            const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            let randomPassword = "";
            for (let i = 0; i < 10; i++) {
                randomPassword += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            passwordInput.value = randomPassword;
        });
    });
</script>

