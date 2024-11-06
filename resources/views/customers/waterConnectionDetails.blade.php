
<div class="modal fade" id="waterConnectionDetails{{ $waterConnection->id }}" tabindex="-1" role="dialog" aria-labelledby="waterConnectionDetailsModalLabel{{ $waterConnection->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="card-info">
                    <div class="card-header card-header-custom bg-purple">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <h4 class="card-title">Información de la Toma</h4>
                            <button type="button" class="close d-sm-inline-block text-white" onclick="closeCurrentModal('#waterConnectionDetails{{ $waterConnection->id }}')" aria-label="Close">
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
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->id }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group">
                                            <label>Nombre</label>
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->name }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="type" class="form-label">Tipo de Toma</label>
                                            @if ($waterConnection->type === 'residencial')
                                                <input type="text" disabled class="form-control" value="Residencial" />
                                            @elseif ($waterConnection->type === 'commercial')
                                                <input type="text" disabled class="form-control" value="Comercial" />
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Calle</label>
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->street }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Colonia</label>
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->block }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Código Postal</label>
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->locality->zip_code ?? 'Desconocido' }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Número Exterior</label>
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->exterior_number ?? 'Desconocido' }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Número Interior</label>
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->interior_number ?? 'Desconocido' }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Número de Ocupantes</label>
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->occupants_number }}" />
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
                                        $waterDays = json_decode($waterConnection->water_days);
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
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->has_water_pressure ? 'Día si noche no' : 'Noche si día no' }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>¿Tiene Cisterna?</label>
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->has_cistern ? 'Sí' : 'No' }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Costo</label>
                                            <input type="text" disabled class="form-control" value="{{ $waterConnection->cost->category ?? 'NULL' }} - {{ $waterConnection->cost->price ?? 'null'}}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeCurrentModal('#waterConnectionDetails{{ $waterConnection->id }}')">Cerrar</button>
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
