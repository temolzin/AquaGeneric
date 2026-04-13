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
                <form action="{{ route('customerIncidents.update', $incident->id) }}" enctype="multipart/form-data" method="post" id="edit-customer-form-{{ $incident->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
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
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Título(*)</label>
                                            <input type="text" class="form-control" name="nameUpdate" value="{{ $incident->name }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción de la Incidencia(*)</label>
                                            <textarea class="form-control" name="descriptionUpdate" rows="4" required>{{ $incident->getLatestDescription() }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="categoryUpdate" class="form-label">Categoría(*)</label>
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
                                            <label for="date_report">Fecha del Reporte</label>
                                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($incident->start_date)->format('d/m/Y') }}" disabled readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="status">Estado</label>
                                            <input type="text" class="form-control" value="{{ $incident->status->status ?? 'Sin estado' }}" disabled readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Creado por</label>
                                            <input type="text" class="form-control" value="{{ $incident->creator->name }} {{ $incident->creator->last_name ?? '' }}" disabled readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="imagesUpdate" class="form-label">Imágenes de referencia <small class="text-muted">(opcional)</small></label>
                                            <input type="file" class="form-control" name="imagesUpdate[]" multiple accept="image/*">
                                            <small class="form-text text-muted mt-1">
                                                Puedes subir varias imágenes para dar contexto. Formatos permitidos: JPG, PNG.
                                            </small>
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

<script>
    $(document).on('shown.bs.modal', '#edit{{ $incident->id }}', function() {
        $(this).find('.select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            $(this).select2({
                allowClear: false,
                placeholder: 'Selecciona una opción',
                width: '100%',
                dropdownParent: $('#edit{{ $incident->id }}')
            });
        });
    });
</script>
