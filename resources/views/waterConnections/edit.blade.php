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
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="nameUpdate" class="form-label">Nombre(*)</label>
                                            <input type="text" class="form-control" name="nameUpdate" id="nameUpdate" value="{{ $connection->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="section_id" class="form-label">Sección(*)</label>
                                            <select class="form-control select2" name="section_id" id="section_id" required>
                                                <option value="">Selecciona una sección</option>
                                                @foreach($sections as $section)
                                                    <option value="{{ $section->id }}" {{ $connection->section_id == $section->id ? 'selected' : '' }}>
                                                        {{ $section->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="customerIdUpdate" class="form-label">Cliente Propietario(*)</label>
                                        <select class="form-control select2" name="customerIdUpdate" id="customerIdUpdate" required>
                                            <option value=""></option>
                                                @foreach($customers as $customer)
                                                    @if($customer)
                                                        <option value="{{ $customer->id }}" {{ $customer->id == $connection->customer_id ? 'selected' : '' }}>
                                                            {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="streetUpdate" class="form-label">Calle(*)</label>
                                            <input type="text" class="form-control" id="streetUpdate" name="streetUpdate" value="{{ $connection->street }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="blockUpdate" class="form-label">Colonia(*)</label>
                                            <input type="text" class="form-control" id="blockUpdate" name="blockUpdate" value="{{$connection->block }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="exteriorNumberUpdate" class="form-label">Número Exterior(*)</label>
                                            <input type="text" class="form-control" id="exteriorNumberUpdate" name="exteriorNumberUpdate" value="{{ $connection->exterior_number }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="interiorNumberUpdate" class="form-label">Número Interior(*)</label>
                                            <input type="text" class="form-control" id="interiorNumberUpdate" name="interiorNumberUpdate" value="{{ $connection->interior_number }}" required />
                                        </div>
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
                                            <div class="input-group">
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="monday_update" name="days_update[]" value="monday"
                                                            {{ is_array(json_decode($connection->water_days)) && in_array('monday', json_decode($connection->water_days)) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="monday_update">Lunes</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="tuesday_update" name="days_update[]" value="tuesday"
                                                        {{ is_array(json_decode($connection->water_days)) && in_array('tuesday', json_decode($connection->water_days)) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="tuesday_update">Martes</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="wednesday_update" name="days_update[]" value="wednesday"
                                                            {{ is_array(json_decode($connection->water_days)) && in_array('wednesday', json_decode($connection->water_days)) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="wednesday_update">Miércoles</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="thursday_update" name="days_update[]" value="thursday"
                                                            {{ is_array(json_decode($connection->water_days)) && in_array('thursday', json_decode($connection->water_days)) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="thursday_update">Jueves</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="friday_update" name="days_update[]" value="friday"
                                                            {{ is_array(json_decode($connection->water_days)) && in_array('friday', json_decode($connection->water_days)) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="friday_update">Viernes</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="saturday_update" name="days_update[]" value="saturday"
                                                            {{ is_array(json_decode($connection->water_days)) && in_array('saturday', json_decode($connection->water_days)) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="saturday_update">Sábado</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="sunday_update" name="days_update[]" value="sunday"
                                                            {{ is_array(json_decode($connection->water_days)) && in_array('sunday', json_decode($connection->water_days)) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="sunday_update">Domingo</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-5">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="all_days_update" name="all_days_update"
                                                            {{ $connection->water_days == json_encode('all') ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="all_days_update">Todos los días</label>
                                                    </div>    
                                                </div>
                                            </div>
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
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="hasCisternUpdate" class="form-label">¿Tiene cisterna?</label>
                                            <select class="form-control" id="hasCisternUpdate" name="hasCisternUpdate" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ $connection->has_cistern == 1 ? 'selected' : '' }}>Sí</option>
                                                <option value="0" {{ $connection->has_cistern == 0 ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="costIdUpdate" class="form-label">Costo(*)</label>
                                            <select class="form-control" id="costIdUpdate" name="costIdUpdate" required>
                                                <option value="">Selecciona una opción</option>
                                                @foreach($costs as $cost)
                                                    <option value="{{ $cost->id }}" {{ $connection->cost_id == $cost->id ? 'selected' : '' }}>
                                                        {{ $cost->category }} - ${{ $cost->price }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="noteUpdate" class="form-label">Nota</label>
                                            <textarea class="form-control" id="noteUpdate" name="noteUpdate">{{ $connection->note }}</textarea>
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
    const allDaysUpdateCheckbox = document.getElementById('all_days_update');
    const dayUpdateCheckboxes = document.querySelectorAll('input[name="days_update[]"]');

    function toggleDaysUpdate() {
        if (allDaysUpdateCheckbox.checked) {
            dayUpdateCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.disabled = true;
            });
        } else {
            dayUpdateCheckboxes.forEach(checkbox => {
                checkbox.disabled = false;
            });
        }
    }

    toggleDaysUpdate();

    allDaysUpdateCheckbox.addEventListener('change', toggleDaysUpdate);
</script>
