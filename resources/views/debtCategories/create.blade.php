<div class="modal fade" id="create" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">
                            Registrar Categoría
                            <small>&nbsp;(*) Campos requeridos</small>
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                </div>
                <form id="createCategoryForm" action="{{ route('debtCategories.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Datos de la categoría</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Nombre (*)</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Color (*)</label>
                                            <div class="d-flex align-items-center">
                                                <select name="color_index" class="form-control" id="colorSelectCategory"
                                                    required>
                                                    <option value="">Seleccione un color</option>
                                                    <option value="0" data-color="#3498db">Azul</option>
                                                    <option value="13" data-color="#e74c3c">Rojo</option>
                                                    <option value="10" data-color="#2ecc71">Verde</option>
                                                    <option value="4" data-color="#f39c12">Naranja</option>
                                                    <option value="1" data-color="#9b59b6">Púrpura</option>
                                                    <option value="6" data-color="#1abc9c">Turquesa</option>
                                                    <option value="14" data-color="#34495e">Gris oscuro</option>
                                                    <option value="3" data-color="#f1c40f">Amarillo</option>
                                                </select>
                                                <span id="colorPreviewCategory"
                                                    style="width: 45px; height: 45px;
                                                           margin-left: 5px;
                                                           border-radius: 5px;
                                                           background-color: #6c757d;">
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Descripción</label>
                                            <textarea name="description" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cerrar
                        </button>
                        <button type="submit" class="btn btn-success">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('createCategoryForm');
        const select = document.getElementById('colorSelectCategory');
        const preview = document.getElementById('colorPreviewCategory');
        const updatePreview = () =>
            preview.style.backgroundColor =
            select.options[select.selectedIndex]?.dataset.color || '#6c757d';
        select.addEventListener('change', updatePreview);
        updatePreview();
        const resolveErrorMessage = (data) =>
            data?.errors ?
            Object.values(data.errors).flat().join('\n') :
            data?.error || 'Error al registrar';
        const showAlert = (icon, title, text) =>
            Swal.fire({
                icon,
                title,
                text
            });
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    }
                });
                const data = await response.json();
                if (!response.ok) {
                    return showAlert(
                        'error',
                        'Error',
                        resolveErrorMessage(data)
                    );
                }
                showAlert(
                    'success',
                    'Éxito',
                    data.success || 'Categoría registrada correctamente'
                ).then(() => window.location.reload());
            } catch (error) {
                console.error(error);
                showAlert(
                    'error',
                    'Error inesperado',
                    'Algo salió mal'
                );
            }
        });
    });
</script>
