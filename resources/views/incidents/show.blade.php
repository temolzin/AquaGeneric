<div class="modal fade" id="view{{ $incident->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $incident->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información del Incidencia</h4>
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
                                        <label>ID Incidencia</label>
                                        <input type="text" disabled class="form-control" value="{{ $incident->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" disabled class="form-control" value="{{ $incident->name }} " />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Fecha de Inicio</label>
                                        <input type="text" disabled class="form-control" value="{{ \Carbon\Carbon::parse($incident->start_date)->translatedFormat('d/F/Y') }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="CategoryUpdate" class="form-label">Categoria(*)</label>
                                        <input type="text" disabled class="form-control" value="{{ $incident->incidentCategory->name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status" class="form-label">Estatus(*)</label>
                                        <input type="text" disabled class="form-control" value="{{ $incident->status }}" />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <input type="text" disabled class="form-control" value="{{ $incident->description }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 text-center mt-4">
                                @if($incident->getMedia('incidentImages')->count())
                                    @foreach ($incident->getMedia('incidentImages') as $media)
                                        <img src="{{ $media->getUrl() }}" alt="Imagen incidencia" class="img-thumbnail m-1" style="max-width: 150px; cursor:pointer;"
                                            data-toggle="modal" data-target="#imagePreviewModal{{ $media->id }}">
                                        <!-- Modal para vista previa -->
                                        <div class="modal fade" id="imagePreviewModal{{ $media->id }}" tabindex="-1" role="dialog" aria-labelledby="imagePreviewModalLabel{{ $media->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-secondary text-white">
                                                        <h5 class="modal-title" id="imagePreviewModalLabel{{ $media->id }}">Vista previa</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ $media->getUrl() }}" alt="Imagen incidencia" class="img-fluid rounded" style="max-height: 500px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p>No hay imágenes disponibles.</p>
                                @endif
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
