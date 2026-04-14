<div class="modal fade" id="createIncidence" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Crear Reporte de Incidencia <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('customerIncidents.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Ingrese Datos del Reporte</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Título(*)</label>
                                            <input type="text" class="form-control" name="name" placeholder="Ingresa título" value="{{ old('name') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción de la Incidencia(*)</label>
                                            <textarea class="form-control" name="description" placeholder="Describe la incidencia" rows="4" required>{{ old('description') }}</textarea>
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
                                            <label for="date_report">Fecha del Reporte</label>
                                            <input type="text" class="form-control" value="{{ date('d/m/Y') }}" disabled readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="status">Estado</label>
                                            <input type="text" class="form-control" value="Pendiente" disabled readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Creado por</label>
                                            <input type="text" class="form-control" value="{{ auth()->user()->name }} {{ auth()->user()->last_name ?? '' }}" disabled readonly>
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
                                    <input type="hidden" name="startDate" value="{{ date('Y-m-d') }}" />
                                    <input type="hidden" name="isClientReport" value="1" />
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
