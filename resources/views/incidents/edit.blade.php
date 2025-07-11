<div class="modal fade" id="edit{{ $incident->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Incidencia <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <form action="{{ route('incidents.update', $incident->id) }}" enctype="multipart/form-data" method="post" id="edit-customer-form-{{ $incident->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Datos de Incidencia</h3>
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
                                            <input type="text" class="form-control" name="nameUpdate" value="{{ $incident->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="startDate" class="form-label">Fecha de Inicio(*)</label>
                                            <input type="date" class="form-control" name="startDateUpdate" value="{{ \Carbon\Carbon::parse($incident->start_date)->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="categoryUpdate" class="form-label">Categoria(*)</label>
                                            <select class="form-control select2" name="categoryUpdate" required>
                                                <option value="">Selecciona una opción</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ $incident->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Estatus(*)</label>
                                            <select class="form-control select2" name="statusUpdate" required>
                                                <option value="">Selecciona una opción</option>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}" {{ $incident->latestStatus() == $status ? 'selected' : '' }}> {{ $status }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="descriptionUpdate" class="form-label">Descripción(*)</label>
                                            <textarea class="form-control" name="descriptionUpdate" rows="3" required>{{ $incident->latestDescription() }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="imagesUpdate" class="form-label">Actualizar Imágenes de Referencia</label>
                                            <input type="file" class="form-control" name="imagesUpdate[]" multiple accept="image/*">
                                            <small class="form-text text-muted">Puedes seleccionar una o más imágenes para agregar.</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-center mt-3">
                                        @if($incident->getMedia('incidentImages')->count())
                                            <label>Imágenes actuales:</label>
                                            <div>
                                                @foreach($incident->getMedia('incidentImages') as $media)
                                                    <img src="{{ $media->getUrl() }}" alt="Imagen incidencia" class="img-thumbnail m-1" style="max-width: 100px;">
                                                @endforeach
                                            </div>
                                        @else
                                            <p>No hay imágenes disponibles.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm({{ $incident->id }})">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
