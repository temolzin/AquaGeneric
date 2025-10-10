<div class="modal fade" id="view{{ $connection->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $connection->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información de la Toma</h4>
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
                                        <label>ID</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <label>Nombre de la Toma</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Sección</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->section ? $connection->section->name : 'Sin sección' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Propietario</label>
                                        @if($connection->customer && $connection->customer->user)
                                            <input type="text" disabled class="form-control"
                                                value="{{ $connection->customer->user->name }} {{ $connection->customer->user->last_name }}" />
                                        @else
                                            <div class="alert alert-warning p-2 mb-2 small">
                                                Esta toma de agua no tiene un cliente asignado.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="type" class="form-label">Tipo de Toma</label>
                                        @if ($connection->type === 'residencial')
                                            <input type="text" disabled class="form-control" value="Residencial" />
                                        @elseif ($connection->type === 'commercial')
                                            <input type="text" disabled class="form-control" value="Comercial" />
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Calle</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->street }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Colonia</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->block }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Código Postal</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->locality->zip_code ?? 'Desconocido' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Número Exterior</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->exterior_number ?? 'Desconocido' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Número Interior</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->interior_number ?? 'Desconocido' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Número de Ocupantes</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->occupants_number }}" />
                                    </div>
                                </div>
                                @php
                                    $daysMap = [
                                        'monday' => 'Lunes',
                                        'tuesday' => 'Martes',
                                        'wednesday' => 'Miércoles',
                                        'thursday' => 'Jueves',
                                        'friday' => 'Viernes',
                                        'saturday' => 'Sábado',
                                        'sunday' => 'Domingo'
                                    ];
                                    $waterDays = json_decode($connection->water_days);
                                    $displayDays = match (true) {
                                        is_array($waterDays) => implode(', ', array_map(fn($day) => $daysMap[$day], $waterDays)),
                                        $waterDays === 'all' => 'Todos los días',
                                        default => 'No definido',
                                    };
                                @endphp
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Días de Agua</label>
                                        <input type="text" disabled class="form-control" value="{{ $displayDays }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>¿Tiene Presión de Agua?</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->has_water_pressure ? 'Día si noche no' : 'Noche si día no' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>¿Tiene Cisterna?</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->has_cistern ? 'Sí' : 'No' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Costo</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->cost->category ?? 'NULL' }} - {{ $connection->cost->price ?? 'null'}}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Registrado por</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->creator->name ?? 'Desconocido' }} {{ $connection->creator->last_name ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Nota de la Toma de Agua</label>
                                        <textarea disabled class="form-control">{{ $connection->note }}</textarea>
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
