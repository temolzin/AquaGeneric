<div class="modal fade" id="edit{{ $connection->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Toma de agua <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <form action="{{ route('waterConnections.update', $connection->id) }}" enctype="multipart/form-data" method="post" id="edit-waterConnection-form-{{ $connection->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="nameUpdate" class="form-label">Nombre(*)</label>
                                            <input type="text" class="form-control" name="nameUpdate" id="nameUpdate" value="{{ $connection->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="customerIdUpdate" class="form-label">Cliente Propietario(*)</label>
                                        <select class="form-control select2" name="customerIdUpdate" id="customerIdUpdate" required>
                                            <option value=""></option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" {{ $customer->id == $connection->customer_id ? 'selected' : '' }}>
                                                    {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                                    </option>
                                                @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="typeUpdate" class="form-label">Tipo de toma(*)</label>
                                            <select class="form-control" id="typeUpdate" name="typeUpdate" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="commercial" {{ old('typeUpdate', $connection->type ?? '') == 'commercial' ? 'selected' : '' }}>Comercial</option>
                                                <option value="residencial" {{ old('typeUpdate', $connection->type ?? '') == 'residencial' ? 'selected' : '' }}>Residencial</option>
                                            </select>
                                        </div>
                                    </div>                                                                                                        
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="occupantsNumberUpdate" class="form-label">Número de Ocupantes(*)</label>
                                            <input type="number" min="1" class="form-control" name="occupantsNumberUpdate" id="occupantsNumberUpdate" value="{{ $connection->occupants_number }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="waterDaysUpdate" class="form-label">Días de Agua(*)</label>
                                            <input type="number" min="0" class="form-control" name="waterDaysUpdate" id="waterDaysUpdate" value="{{ $connection->water_days }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="hasWaterPressureUpdate" class="form-label">¿Tiene presión de agua?</label>
                                            <select class="form-control" id="hasWaterPressureUpdate" name="hasWaterPressureUpdate" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ $connection->has_water_pressure == 1 ? 'selected' : '' }}>Día si noche no</option>
                                                <option value="0" {{ $connection->has_water_pressure == 0 ? 'selected' : '' }}>Noche si día no</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="hasCisternUpdate" class="form-label">¿Tiene cisterna?</label>
                                            <select class="form-control" id="hasCisternUpdate" name="hasCisternUpdate" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ $connection->has_cistern == 1 ? 'selected' : '' }}>Sí</option>
                                                <option value="0" {{ $connection->has_cistern == 0 ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label for="costIdUpdate" class="form-label">Costo(*)</label>
                                            <select class="form-control" id="costIdUpdate" name="costIdUpdate" required>
                                                <option value="">Selecciona una opción</option>
                                                @foreach($costs as $cost)
                                                    <option value="{{ $cost->id }}" {{ $connection->cost_id == $cost->id ? 'selected' : '' }}>
                                                        {{ $cost->category }} - {{ $cost->price }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>                                                                       
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm({{ $connection->id }})">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function resetForm(id) {
        var form = document.getElementById('edit-waterConnection-form-' + id);
        form.reset();
    }
</script>
