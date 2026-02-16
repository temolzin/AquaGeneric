<div class="modal fade" id="edit{{ $component->id }}" tabindex="-1" role="dialog" aria-labelledby="editInventoryLabel{{ $component->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Componente <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('inventory.update', $component->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="nameUpdate{{ $component->id }}" class="form-label">Nombre del Componente(*)</label>
                                            <input type="text" class="form-control" name="name" id="nameUpdate{{ $component->id }}" value="{{ $component->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="amountUpdate{{ $component->id }}" class="form-label">Cantidad(*)</label>
                                            <input type="number" min="0" class="form-control" name="amount" id="amountUpdate{{ $component->id }}" value="{{ $component->amount }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="inventory_category_idUpdate{{ $component->id }}" class="form-label">Categoría(*)</label>
                                            <select class="form-control select2" id="inventory_category_idUpdate{{ $component->id }}" name="inventory_category_id" required style="width: 100%;">
                                                <option value="">Selecciona la categoría</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('inventory_category_id', $component->inventory_category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="materialUpdate{{ $component->id }}" class="form-label">Material</label>
                                            <input type="text" class="form-control" name="material" id="materialUpdate{{ $component->id }}" value="{{ $component->material }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="dimensionsUpdate{{ $component->id }}" class="form-label">Dimensiones</label>
                                            <input type="text" class="form-control" name="dimensions" id="dimensionsUpdate{{ $component->id }}" value="{{ $component->dimensions }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="descriptionUpdate{{ $component->id }}" class="form-label">Descripción</label>
                                            <textarea class="form-control" name="description" id="descriptionUpdate{{ $component->id }}">{{ $component->description }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
