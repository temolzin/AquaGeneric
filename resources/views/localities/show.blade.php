<div class="modal fade" id="view{{ $locality->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $locality->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información de la Localidad</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 text-center">
                                    @if ($locality->getFirstMediaUrl('localityGallery'))
                                        <img src="{{ $locality->getFirstMediaUrl('localityGallery') }}" alt="Foto de la localidad" class="img-fluid" 
                                        style="width: 120px; height: 120px; border-radius: 50%; margin-bottom: 5px;">
                                    @else
                                        <img src="{{ asset('img/userDefault.png') }}" alt="Foto de la localidad" class="img-fluid" 
                                        style="width: 120px; height: 120px; border-radius: 50%; margin-bottom: 5px;">
                                    @endif
                                </div>
                                <div class="col-lg-12 mt-3">
                                    <div class="form-group">
                                        <label>ID</label>
                                        <input type="text" disabled class="form-control form-control-lg" value="{{ $locality->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Nombre de la localidad</label>
                                        <input type="text" disabled class="form-control" value="{{ $locality->locality_name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Municipio al que pertenece</label>
                                        <input type="text" disabled class="form-control" value="{{ $locality->municipality }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Estado al que pertenece</label>
                                        <input type="text" disabled class="form-control" value="{{ $locality->state }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Código Postal</label>
                                        <input type="text" disabled class="form-control" value="{{ $locality->zip_code }}" />
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
