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
                                        <label>Tipo</label>
                                        @php
                                            $tipe = 'Desconocido';
                                            if ($connection->type === 'residencial') {
                                                $tipe = 'Residencial';
                                            } elseif ($connection->type === 'commercial') {
                                                $tipe = 'Comercial';
                                            } else {
                                                $tipe = $connection->type;
                                            }
                                        @endphp
                                        <input type="text" disabled class="form-control" value="{{ $tipe }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fecha de Registro</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->created_at->format('d/m/Y') }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Calle</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->street ?? 'No especificada' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Colonia</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->locality->name ?? 'No especificada' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Número Exterior</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->exterior_number ?? 'No especificado' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Número Interior</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->interior_number ?? 'No especificado' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Código Postal</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->block ?? 'No especificado' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Número de Ocupantes</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->occupants_number ?? 'No especificado' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Días de Agua</label>
                                        <input type="text" disabled class="form-control" value="{{ $connection->formatted_water_days }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>¿Tiene Presión de Agua?</label>
                                        @php
                                            $presionAgua = $connection->has_water_pressure ? 'Sí' : 'No';
                                        @endphp
                                        <input type="text" disabled class="form-control" value="{{ $presionAgua }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>¿Tiene Cisterna?</label>
                                        @php
                                            $cistern = $connection->has_cistern ? 'Sí' : 'No';
                                        @endphp
                                        <input type="text" disabled class="form-control" value="{{ $cistern }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Costo</label>
                                        <input type="text" disabled class="form-control" value="${{ number_format($connection->cost->price, 2) }}" />
                                    </div>
                                </div>
                                @if($connection->note)
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Notas Adicionales</label>
                                        <textarea disabled class="form-control">{{ $connection->note }}</textarea>
                                    </div>
                                </div>
                                @endif
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
