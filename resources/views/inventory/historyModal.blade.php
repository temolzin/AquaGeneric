<div class="modal fade" id="historyModal{{ $component->id }}" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel{{ $component->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header bg-primary">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Historial de Cantidades</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-header py-2 bg-secondary">
                            <h3 class="card-title">Registro de Cambios</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="padding-left: 0;">
                            <div style="margin-left: 27px; margin-bottom: 10px; font-size: 0.95rem;">
                                <strong>Componente:</strong> {{ $component->name }}
                                <span class="badge bg-info ml-2">Cantidad Actual: {{ $component->amount }}</span>
                            </div>
                            <ul class="timeline timeline-inverse">
                                @forelse($component->logs as $log)
                                    <div class="time-label" style="margin-left: 25px; margin-bottom: 10px;">
                                        <span class="bg-primary" style="padding: 4px 12px; border-radius: 9px; font-weight: 500;">
                                            {{ $log->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="timeline-item d-flex align-items-start" style="margin-left: 17px; margin-bottom: 15px;">
                                        <img src="{{ $log->creator?->getFirstMediaUrl('userGallery') ?: asset('img/userDefault.png') }}"
                                            style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
                                        <div style="border: 1px solid #ccc; border-radius: 10px; padding: 10px; flex: 1;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-1" style="font-size: 0.9rem;">
                                                    <strong>Registrado por:</strong>
                                                    {{ $log->creator?->name ?? 'Usuario no disponible' }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> {{ $log->created_at->format('H:i') }}
                                                </small>
                                            </div>
                                            <hr style="margin: 8px 0; border-top: 1px solid #aaa;">
                                            <div style="font-size: 0.9rem;">
                                                <p class="mb-1">
                                                    <strong>Cambio de cantidad:</strong>
                                                    <span class="badge bg-secondary">{{ $log->previous_amount }}</span>
                                                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                    <span class="badge bg-success">{{ $log->amount }}</span>
                                                    @php
                                                        $difference = $log->amount - $log->previous_amount;
                                                        $badgeClass = $difference > 0 ? 'bg-success' : ($difference < 0 ? 'bg-danger' : 'bg-warning');
                                                        $sign = $difference > 0 ? '+' : '';
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }} ml-2">
                                                        {{ $sign }}{{ $difference }}
                                                    </span>
                                                </p>
                                                @if($log->description)
                                                    <p class="mb-1"><strong>Descripción:</strong> {{ $log->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay historial registrado para este componente.</p>
                                    </div>
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
