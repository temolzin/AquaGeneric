<div class="modal fade" id="historyModal{{ $incident->id }}" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel{{ $incident->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header bg-maroon">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Historial de la Incidencia</h4>
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
                            <ul class="timeline timeline-inverse">
                                @forelse($incident->logs as $log)
                                    <div class="time-label">
                                        <span class="bg-maroon">
                                            {{ $log->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="timeline timeline-inverse">
                                        <img src="{{ $log->employee?->getFirstMediaUrl('employeeGallery') ?: asset('img/userDefault.png') }}"
                                            style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%; margin-left: 17px;">
                                    </div>
                                    <div class="timeline-item" style="margin-left: 40px;">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $log->created_at->format('H:i') }}</span>
                                        <div style="font-size: 0.95rem; margin-top: 5px;">
                                            <p><strong>Nombre:</strong> {{ $log->employee->name ?? 'Responsable eliminado' }}</p>
                                            <p><strong>Descripción:</strong> {{ $log->description ?: 'Sin descripción' }}</p>
                                            <p><strong>Estatus:</strong>
                                                <span class="badge badge-info">{{ $log->status }}</span>
                                            </p>
                                            <p><strong>Incidencia:</strong> {{ $incident->name }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <li>
                                        <i class="fas fa-info-circle bg-gray"
                                            style="width: 30px; height: 30px; line-height: 30px; font-size: 14px;"></i>
                                        <div class="timeline-item">
                                            <div class="timeline-body">No hay historial registrado aún.</div>
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
