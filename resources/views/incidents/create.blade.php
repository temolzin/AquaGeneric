<div class="modal fade" id="createIncidence" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Agregar Incidencias <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('incidents.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Registro de Incidencias</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nombre(*)</label>
                                            <input type="text" pattern="^(?!\s*$)(?!.*\d)[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$" class="form-control" name="name" placeholder="Ingresa nombre" value="{{ old('name') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="startDate" class="form-label">Fecha de Inicio(*)</label>
                                            <input type="date" class="form-control" name="startDate" value="{{ old('startDate') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="category" class="form-label">Categoría(*)</label>
                                            <select class="form-control select2" name="category" required>
                                                <option value="">Selecciona una opción</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Estatus(*)</label>
                                            <select class="form-control select2" name="status" required>
                                                <option value="">Selecciona una opción</option>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}">{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción(*)</label>
                                            <textarea class="form-control" name="description" placeholder="Ingrese la Descripción" rows="3" required>{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="imagesInput" class="form-label">Imágenes de referencia <small class="text-muted">(opcional)</small></label>
                                            <input type="file" class="form-control" id="imagesInput" name="images[]" multiple accept="image/*">
                                            <small class="form-text text-muted mt-1">
                                                Puedes subir varias imágenes para dar contexto. Formatos permitidos: JPG, PNG.
                                            </small>
                                            <div id="imageButtonsContainer" class="mt-3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="multiImagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="multiImagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="multiImagePreviewModalLabel">Vista previa</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="multiModalImagePreview" src="#" alt="Vista previa" class="img-fluid rounded" style="max-height: 400px;">
            </div>
        </div>
    </div>
</div>

<style>
    .select2-container .select2-selection--single {
        height: 40px;
        display: flex;
        align-items: center;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('imagesInput');
        const label = input.nextElementSibling;
        const container = document.getElementById('imageButtonsContainer');
        const modalImg = document.getElementById('multiModalImagePreview');
        const form = input.closest('form');

        const MAX_FILES = 5;
        const MAX_FILE_SIZE_MB = 1;
        const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;

        input.addEventListener('change', function () {
            const files = Array.from(this.files);

            if (files.length > MAX_FILES) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Demasiadas imágenes',
                    text: `Solo puedes subir hasta ${MAX_FILES} imágenes.`,
                });
                resetInput();
                return;
            }

            for (const file of files) {
                if (file.size > MAX_FILE_SIZE_BYTES) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Imagen demasiado pesada',
                        text: `La imagen "${file.name}" supera el límite de ${MAX_FILE_SIZE_MB} MB.`,
                    });
                    resetInput();
                    return;
                }
            }

            label.textContent = files.length > 1 ? `${files.length} imágenes seleccionadas` : (files[0]?.name || 'Selecciona una imagen');
            container.innerHTML = '';

            files.forEach((file) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-sm btn-info mr-2 mb-2';
                        btn.textContent = 'Ver imagen';
                        btn.dataset.toggle = 'modal';
                        btn.dataset.target = '#multiImagePreviewModal';
                        btn.dataset.imageSrc = e.target.result;

                        btn.addEventListener('click', function () {
                            modalImg.src = this.dataset.imageSrc;
                        });

                        container.appendChild(btn);
                    };

                    reader.readAsDataURL(file);
                }
            });
        });

        form.addEventListener('submit', function (e) {
            const files = Array.from(input.files);

            if (files.length > MAX_FILES) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Demasiadas imágenes',
                    text: `Solo puedes subir hasta ${MAX_FILES} imágenes.`,
                });
                e.preventDefault();
                return;
            }

            for (const file of files) {
                if (file.size > MAX_FILE_SIZE_BYTES) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Imagen demasiado pesada',
                        text: `La imagen "${file.name}" supera el límite de ${MAX_FILE_SIZE_MB} MB.`,
                    });
                    e.preventDefault();
                    return;
                }
            }
        });

        function resetInput() {
            input.value = '';
            container.innerHTML = '';
            label.textContent = 'Selecciona una imagen';
        }
    });
</script>
