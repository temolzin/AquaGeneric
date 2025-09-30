<div class="modal fade" id="view{{$notice->id}}" tabindex="-1" role="dialog" aria-labelledby="viewLabel{{$notice->id}}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información del Aviso</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Título</label>
                                        <input type="text" disabled class="form-control" value="{{ $notice->title }}" />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <textarea disabled class="form-control" rows="3">{{ $notice->description }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fecha de Inicio</label>
                                        <input type="text" disabled class="form-control" value="{{ $notice->start_date->format('d/m/Y') }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fecha de Fin</label>
                                        <input type="text" disabled class="form-control" value="{{ $notice->end_date->format('d/m/Y') }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Registrado por</label>
                                        <input type="text" disabled class="form-control" value="{{ $notice->creator->name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fecha de registro</label>
                                        <input type="text" disabled class="form-control" value="{{ $notice->created_at->format('d/m/Y') }}" />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Archivo Adjunto</label>
                                        @if ($notice->hasMedia('notice_attachments'))
                                            <a href="{{ route('localityNotices.download', $notice->id) }}" class="btn btn-primary" target="_blank">
                                                <i class="fas fa-eye"></i> Ver archivo
                                            </a>
                                        @else
                                            <button class="btn btn-secondary" disabled>
                                                <i class="fas fa-eye-slash"></i> Sin archivo adjunto
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-3">
                                    @php
                                        $isCurrentlyActive = $notice->is_active && 
                                                           $notice->start_date <= now() && 
                                                           $notice->end_date >= now();
                                    @endphp
                                    <div class="alert 
                                        @if($isCurrentlyActive) alert-success
                                        @elseif($notice->start_date > now()) alert-info
                                        @else alert-secondary @endif" 
                                        role="alert">
                                        <strong>Estado actual:</strong> 
                                        @if($isCurrentlyActive)
                                            Este aviso está actualmente activo.
                                        @elseif($notice->start_date > now())
                                            Este aviso está programado para el futuro.
                                        @else
                                            Este aviso ha expirado.
                                        @endif
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
