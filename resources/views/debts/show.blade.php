<div class="modal fade" id="view{{ $waterConnectionDebt->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $waterConnectionDebt->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información de la Deuda</h4>
                        <button type="button" class="close d-sm-inline-block text-white" onclick="closeCurrentModal('#view{{ $waterConnectionDebt->id }}')"aria-label="Close">
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
                                        <label>ID</label>
                                        <input type="text" disabled class="form-control" value="{{ $waterConnectionDebt->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Monto de la Deuda</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" disabled class="form-control" value="{{ number_format($waterConnectionDebt->amount, 2) }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Cantidad Pagada</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" disabled class="form-control" value="{{ number_format($waterConnectionDebt->debt_current, 2) }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Saldo pendiente</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                            </div>
                                            @php
                                                $remainingAmount = $waterConnectionDebt->amount - $waterConnectionDebt->debt_current;
                                            @endphp
                                            <input type="text" disabled class="form-control" value="{{ number_format($remainingAmount , 2) }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <p class="form-control">
                                            @if ($waterConnectionDebt->status === 'pending')
                                                <button class="badge badge-danger">No pagada</button>
                                            @elseif ($waterConnectionDebt->status === 'partial')
                                                <button class="badge badge-warning">Abonada</button>
                                            @elseif ($waterConnectionDebt->status === 'paid')
                                                <button class="badge badge-success">Pagada</button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fecha de Inicio</label>
                                        <input type="text" disabled class="form-control" value="{{ \Carbon\Carbon::parse($waterConnectionDebt->start_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fecha de Fin</label>
                                        <input type="text" disabled class="form-control" value="{{ \Carbon\Carbon::parse($waterConnectionDebt->end_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}" />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Observación</label>
                                        <textarea disabled class="form-control">{{ $waterConnectionDebt->note }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Registrada por</label>
                                        <input type="text" disabled class="form-control" value="{{ $waterConnectionDebt->creator->name ?? 'Desconocido' }} {{ $waterConnectionDebt->creator->last_name ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-4">
                                    <label>Historial de Pagos</label>
                                    <div class="payment-history" style="max-height: 200px; overflow-y: auto;">                                        <ul class="list-group">
                                            @forelse ($waterConnectionDebt->payments as $payment)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>Monto:</strong> ${{ number_format($payment->amount, 2) }} <br>
                                                        @switch($payment->method)
                                                            @case('cash')
                                                                <strong>Método:</strong> Efectivo <br>
                                                                @break
                                                            @case('card')
                                                            <strong>Método:</strong> Tarjeta <br>
                                                                @break
                                                            @case('transfer')
                                                            <strong>Método:</strong> Transferencia <br>
                                                                @break
                                                            @default
                                                                <strong>Método:</strong> Desconocido <br>
                                                        @endswitch
                                                        @if ($payment->note)
                                                            <strong>Nota:</strong> {{ $payment->note }} <br>
                                                        @endif
                                                        <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($payment->created_at)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item">No hay pagos registrados para esta deuda.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCurrentModal('#view{{ $waterConnectionDebt->id }}')">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function closeCurrentModal(modalId) {
        $(modalId).modal('hide');
    }
</script>
