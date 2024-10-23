
<div class="modal fade" id="createWaterConnections" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Ingrese los Datos de la Toma de Agua <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('waterConnections.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nombre de la Toma(*)</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Ingresa nombre de la toma" value="{{ old('name') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="customer" class="form-label">Cliente Propietario(*)</label>
                                            <select class="form-control select2" name="customer_id" id="customer_id" required>
                                                <option value="">Selecciona un cliente</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}">
                                                        {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>  
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="type" class="form-label">Tipo de toma(*)</label>
                                            <select class="form-control" id="has_cistern" name="has_cistern" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ old('type') === '1' ? 'selected' : '' }}>Comercial</option>
                                                <option value="0" {{ old('tipe') === '0' ? 'selected' : '' }}>Residencial</option>
                                            </select>
                                        </div>
                                    </div>                               
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="occupants_number" class="form-label">Número de Ocupantes(*)</label>
                                            <input type="number" min="1" class="form-control" id="occupants_number" name="occupants_number" placeholder="Ingresa número de ocupantes" value="{{ old('occupants_number') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="water_days" class="form-label">Días de Agua(*)</label>
                                            <div class="input-group">
                                                <input type="number" min="0" max="7" class="form-control" id="water_days" name="water_days" placeholder="Ingresa días de agua" value="{{ old('water_days') }}" required />
                                                <div class="input-group-append">
                                                    <div class="form-check ml-3">
                                                        <input class="form-check-input" type="checkbox" id="all_days" name="all_days">
                                                        <label class="form-check-label" for="all_days">
                                                            Todos los días
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="has_water_pressure" class="form-label">¿Tiene presión de agua?(*)</label>
                                            <select class="form-control" id="has_water_pressure" name="has_water_pressure" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ old('has_water_pressure') === '1' ? 'selected' : '' }}>Día si noche no</option>
                                                <option value="0" {{ old('has_water_pressure') === '0' ? 'selected' : '' }}>Noche si día no</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="has_cistern" class="form-label">¿Tiene cisterna?(*)</label>
                                            <select class="form-control" id="has_cistern" name="has_cistern" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ old('has_cistern') === '1' ? 'selected' : '' }}>Sí</option>
                                                <option value="0" {{ old('has_cistern') === '0' ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                    </div>  
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="cost" class="form-label">Costo(*)</label>
                                            <select class="form-control" name="cost_id" id="cost" required>
                                                <option value="">Selecciona el costo</option>
                                                @foreach ($costs as $cost)
                                                    <option value="{{ $cost->id }}" {{ old('cost_id') == $cost->id ? 'selected' : '' }}>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="save" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .select2-container .select2-selection--single {
        height: 40px;
        display: flex;
        align-items: center;
    }
</style>

<script>
    document.getElementById('all_days').addEventListener('change', function() {
        const waterDaysInput = document.getElementById('water_days');
        if (this.checked) {
            waterDaysInput.value = 7;
            waterDaysInput.setAttribute('disabled', 'disabled');t
        } else {
            waterDaysInput.removeAttribute('disabled');
            waterDaysInput.value = '';
        }
    });
</script>
