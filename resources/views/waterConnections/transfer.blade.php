<div class="modal fade" id="transferOwner{{ $connection->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">
                            Cambio de Propietario (Fallecimiento)
                            <small>&nbsp;(*) Campos requeridos</small>
                        </h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form method="POST" action="{{ route('waterConnections.transfer.store', $connection->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <div class="p-3 mb-3 rounded border border-success" style="background:#e9f7ef;">
                            <h5 class="mb-2">Información de la toma</h5>
                            <p class="mb-1"><strong>Toma:</strong> {{ $connection->name }}</p>
                            <p class="mb-0"><strong>Dirección:</strong> {{ $connection->street }} {{ $connection->exterior_number }} {{ $connection->interior_number }}</p>
                        </div>
                        <div class="p-3 mb-3 rounded border border-success" style="background:#e9f7ef;">
                            <h5 class="mb-2">Titular actual (Fallecido)</h5>
                            <p class="mb-0"><strong>Titular actual (Fallecido):</strong> {{ $connection->customer_name }} {{ $connection->customer_last_name }}</p>
                        </div>
                        <div class="form-group">
                            <label for="new_customer_id_{{ $connection->id }}">Nuevo titular *</label>
                            <select name="new_customer_id" id="new_customer_id_{{ $connection->id }}" class="form-control select2" required>
                                <option value="">Selecciona una opción</option>
                                @foreach($customers as $customer)
                                    @if((int)$customer->id !== (int)$connection->customer_id)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="text-muted">Solo se muestran clientes activos (con vida).</small>
                            @error('new_customer_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="note_{{ $connection->id }}">Nota (opcional)</label>
                            <textarea name="note" id="note_{{ $connection->id }}" class="form-control" rows="3">{{ old('note') }}</textarea>
                            @error('note')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <hr>
                        <h5 class="mb-2">Documentos de verificación (obligatorios)</h5>
                        <small class="text-muted d-block mb-3">
                            La transferencia NO se completará si falta cualquiera de los documentos requeridos.
                        </small>
                        @php
                            $docTypes = \App\Models\LogWaterConnectionTransfer::REQUIRED_DOCUMENT_TYPES;
                            $docLabels = \App\Models\LogWaterConnectionTransfer::documentTypeLabels();
                            $lastIndex = count($docTypes) - 1;
                        @endphp
                        <div class="row">
                            @foreach ($docTypes as $index => $type)
                                <div class="{{ $index === $lastIndex ? 'col-12' : 'col-12 col-md-6' }} mb-3">
                                    @php
                                        $label = $docLabels[$type] ?? $type;
                                        preg_match('/^(.*?)(\s*\((.*)\))?$/u', $label, $m);
                                        $main = trim($m[1] ?? $label);
                                        $extra = isset($m[3]) ? trim($m[3]) : null;
                                    @endphp
                                    <label class="form-label mb-1">{{ $main }} <span class="text-danger">*</span></label>
                                    @if($extra)
                                        <small class="text-muted d-block mb-2">{{ $extra }}</small>
                                    @else
                                        <div style="height: 18px;" class="mb-2"></div>
                                    @endif
                                    <input type="file" name="documents[{{ $type }}]" class="form-control @error("documents.$type") is-invalid @enderror" required accept=".pdf,.jpg,.jpeg,.png">
                                    @error("documents.$type")
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Transferir Toma</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
