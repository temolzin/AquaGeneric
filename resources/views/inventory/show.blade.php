<div class="modal fade" id="view{{ $componente->id }}" tabindex="-1" role="dialog" aria-labelledby="viewInventoryLabel{{ $componente->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información del Componente</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>ID</label>
                                        <input type="text" disabled class="form-control" value="{{ $componente->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label>Nombre del Componente</label>
                                        <input type="text" disabled class="form-control" value="{{ $componente->name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label>Creado por</label>
                                        <input type="text" disabled class="form-control" value="{{ $componente->creator->name ?? 'Sin creador' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Cantidad</label>
                                        <input type="text" disabled class="form-control" value="{{ $componente->amount }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Categoría</label>
                                        <input type="text" disabled class="form-control" value="{{ $componente->category }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Material</label>
                                        <input type="text" disabled class="form-control" value="{{ $componente->material ?? 'No especificado' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Dimensiones</label>
                                        <input type="text" disabled class="form-control" value="{{ $componente->dimensions ?? 'No especificado' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <textarea disabled class="form-control">{{ $componente->description ?? 'No especificado' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
