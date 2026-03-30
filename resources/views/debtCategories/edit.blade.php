<div class="modal fade" id="edit{{ $category->id }}" tabindex="-1" role="dialog" aria-labelledby="editCategoryLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">
                            Editar categoría de deuda
                            <small>&nbsp;(*) Campos requeridos</small>
                        </h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('debtCategories.update', $category->id) }}" method="POST"
                    id="edit-category-form-{{ $category->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Datos de la categoría</h3>
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
                                            <label for="nameUpdate{{ $category->id }}" class="form-label">
                                                Nombre (*)
                                            </label>
                                            <input type="text" class="form-control" name="name"
                                                id="nameUpdate{{ $category->id }}" value="{{ $category->name }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="descriptionUpdate{{ $category->id }}" class="form-label">
                                                Descripción
                                            </label>
                                            <textarea class="form-control" name="description" id="descriptionUpdate{{ $category->id }}" rows="2">{{ $category->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="color_index">Color (*)</label>
                                            <div class="d-flex align-items-center">
                                                <select name="color_index" class="form-control select2" id="colorSelectCategory{{ $category->id }}" style="flex: 1;" required>
                                                    <option value="">Seleccione un color</option>
                                                    <option value="0"  data-color="#3498db" {{ $category->color == 'bg-blue' ? 'selected' : '' }}>Azul</option>
                                                    <option value="13" data-color="#e74c3c" {{ $category->color == 'bg-danger' ? 'selected' : '' }}>Rojo</option>
                                                    <option value="10" data-color="#2ecc71" {{ $category->color == 'bg-success' ? 'selected' : '' }}>Verde</option>
                                                    <option value="4"  data-color="#f39c12" {{ $category->color == 'bg-orange' ? 'selected' : '' }}>Naranja</option>
                                                    <option value="1"  data-color="#9b59b6" {{ $category->color == 'bg-purple' ? 'selected' : '' }}>Púrpura</option>
                                                    <option value="6"  data-color="#1abc9c" {{ $category->color == 'bg-teal' ? 'selected' : '' }}>Turquesa</option>
                                                    <option value="14" data-color="#34495e" {{ $category->color == 'bg-secondary' ? 'selected' : '' }}>Gris oscuro</option>
                                                    <option value="3"  data-color="#f1c40f" {{ $category->color == 'bg-yellow' ? 'selected' : '' }}>Amarillo</option>
                                                </select>
                                                <span id="colorPreviewCategory{{ $category->id }}"
                                                    style="width: 45px; height: 45px;
                                                        background-color: #6c757d;
                                                        border: 1px solid #ced4da;
                                                        margin-left: 5px;">
                                                </span>
                                            </div>
                                            @error('color_index')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            id="cancel-category-{{ $category->id }}">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[id^="colorSelectCategory"]').forEach(select => {
                const id = select.id.replace('colorSelectCategory', '');
                const preview = document.getElementById('colorPreviewCategory' + id);
                if (!select || !preview) return;
                const updatePreview = () => {
                    const selected = select.options[select.selectedIndex];
                    const color = selected?.dataset.color || '#6c757d';
                    preview.style.backgroundColor = color;
                    preview.style.border = `1px solid ${color}`;
                };
                $(select).on('change', updatePreview);
                updatePreview();
            });
            $(document).on('shown.bs.modal', function() {
                document.querySelectorAll('[id^="colorSelectCategory"]').forEach(select => {
                    const id = select.id.replace('colorSelectCategory', '');
                    const preview = document.getElementById('colorPreviewCategory' + id);
                    if (!select || !preview) return;
                    const selected = select.options[select.selectedIndex];
                    const color = selected?.dataset.color || '#6c757d';
                    preview.style.backgroundColor = color;
                    preview.style.border = `1px solid ${color}`;
                });
            });
        });
    </script>
@endsection
