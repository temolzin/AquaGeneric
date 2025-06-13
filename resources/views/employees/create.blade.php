<div class="modal fade" id="createEmployee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <form action="{{ route('employees.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Ingrese los Datos del Empleado</h3>
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
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="zip_code" class="form-label">Código Postal(*)</label>
                                            <input type="text" pattern=".*\S.*" class="form-control" id="zip_code" name="zip_code" placeholder="Ingresa código postal" value="{{ old('zip_code') }}" maxlength="5" pattern="\d{5}" inputmode="numeric" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="exterior_number" class="form-label">Número Exterior(*)</label>
                                            <input type="text" pattern=".*\S.*" class="form-control" id="exterior_number" name="exterior_number" placeholder="Ingresa número exterior" value="{{ old('exterior_number') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="interior_number" class="form-label">Número Interior(*)</label>
                                            <input type="text" pattern=".*\S.*" class="form-control" id="interior_number" name="interior_number" placeholder="Ingresa número interior" value="{{ old('interior_number') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Correo Electronico(*)</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa correo electronico" value="{{ old('email') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="phone_number" class="form-label">Número telefónico(*)</label>
                                            <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Ingresa número telefónico" value="{{ old('phone_number') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="salary" class="form-label">Salario(*)</label>
                                            <input type="number" class="form-control" id="salary" name="salary" placeholder="Ingresa salario" value="{{ old('salary') }}" required />
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
        reader.onload = function () {
            var output = document.getElementById('photo-preview');
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    function handleFormSubmit(form) {
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitButtons.forEach(button => {
            button.disabled = true;
            if (button.innerHTML) {
                button.innerHTML = 'Guardando...';
            }
        });
    }

    const createEmployeeForm = document.getElementById('createEmployee');
    if (createEmployeeForm) {
        createEmployeeForm.addEventListener('submit', function (e) {
            handleFormSubmit(createEmployeeForm);
        });
    }
</script>