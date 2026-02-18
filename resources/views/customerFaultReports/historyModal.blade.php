<div class="modal fade" id="historyModal{{ $report->id }}" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel{{ $report->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header bg-maroon">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Historial del Reporte</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-header py-2 bg-secondary">
                            <h3 class="card-title">Cambios de Estatus</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="padding-left: 0;">
                            <div style="margin-left: 27px; margin-bottom: 10px; font-size: 0.95rem;">
                                <strong>Reporte:</strong> {{ $report->title }}
                            </div>
                            <ul class="timeline timeline-inverse">
                                @forelse($report->logs as $log)
                                    <div class="time-label" style="margin-left: 25px; margin-bottom: 10px;">
                                        <span class="bg-maroon" style="padding: 4px 12px; border-radius: 9px; font-weight: 500;">{{ $log->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="timeline-item d-flex align-items-start" style="margin-left: 17px; margin-bottom: 15px;">
                                        <img src="{{ asset('img/userDefault.png') }}"
                                            style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
                                        <div style="border: 1px solid #ccc; border-radius: 10px; padding: 10px; flex: 1;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-1" style="font-size: 0.9rem;">
                                                    <strong>Realizado por:</strong>
                                                    {{ $log->creator?->name ?? 'Usuario desconocido' }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> {{ $log->created_at->format('H:i') }}
                                                </small>
                                            </div>
                                            <hr style="margin: 8px 0; border-top: 1px solid #aaa;">
                                            <div style="font-size: 0.9rem;">
                                                <p class="mb-1"><strong>Estatus:</strong>
                                                    <span class="badge" style="background-color: #007bff; color: white; padding: 6px 10px; font-size: 12px; border-radius: 4px;">{{ $log->status }}</span>
                                                </p>
                                                <p class="mb-1"><strong>Comentario:</strong> {{ $log->comentario ?: 'Sin comentario' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <li style="list-style: none;">
                                        <i class="fas fa-info-circle bg-gray"
                                        style="width: 30px; height: 30px; line-height: 30px; font-size: 14px; margin-left: 10px;"></i>
                                        <div class="timeline-item">
                                            <div class="timeline-body" style="text-align: center;">
                                                No hay historial registrado aún.
                                            </div>
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
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
