
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
                <form action="{{ route('waterConnections.store') }}" method="post" enctype="multipart/form-data" id="waterConnectionForm">
                    @csrf
                    <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                        @php
                            $currentConnections = auth()->user()->locality->waterConnections()->count() ?? 0;
                            $connectionLimit = auth()->user()->locality->membership->water_connections_number ?? 0;
                            $canCreateMore = $connectionLimit > $currentConnections;
                        @endphp
                        @if(!$canCreateMore)
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h4><i class="icon fas fa-ban"></i> Límite de Tomas de Agua Alcanzado</h4>
                                <p>
                                    Has alcanzado el límite máximo de tomas de agua permitidas por tu membresía.<br>
                                    <strong>Actual: {{ $currentConnections }} / Límite: {{ $connectionLimit }}</strong>
                                </p>
                                <p class="mb-0">
                                    <strong>Contacta al administrador para expandir tu membresía y poder agregar más tomas de agua.</strong>
                                </p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nombre de la Toma(*)</label>
                                            <input type="text" class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                id="name" name="name" placeholder="Ingresa nombre de la toma" 
                                                value="{{ old('name') }}" @if(!$canCreateMore) disabled @endif required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="section_id" class="form-label">Sección(*)</label>
                                            <select class="form-control select2 @if(!$canCreateMore) disabled-input @endif" 
                                                    name="section_id" id="section_id" 
                                                    @if(!$canCreateMore) disabled @endif required>
                                                <option value="">Selecciona una sección</option>
                                                @foreach($sections as $section)
                                                    <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                                        {{ $section->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="customer" class="form-label">Cliente Propietario(*)</label>
                                            <select class="form-control select2 @if(!$canCreateMore) disabled-input @endif" 
                                                    name="customer_id" id="customer_id" 
                                                    @if(!$canCreateMore) disabled @endif required>
                                                <option value="">Selecciona un cliente</option>
                                                @foreach($customers as $customer)
                                                    @if($customers)
                                                        <option value="{{ $customer->id }}">
                                                            {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>  
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="street" class="form-label">Calle(*)</label>
                                            <input type="text" class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                id="street" name="street" placeholder="Ingresa calle" 
                                                value="{{ old('street') }}" @if(!$canCreateMore) disabled @endif required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="block" class="form-label">Colonia(*)</label>
                                            <input type="text" class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                id="block" name="block" placeholder="Ingresa colonia" 
                                                value="{{ old('block') }}" @if(!$canCreateMore) disabled @endif required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="exterior_number" class="form-label">Número Exterior(*)</label>
                                            <input type="text" class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                id="exterior_number" name="exterior_number" placeholder="Ingresa número exterior" 
                                                value="{{ old('exterior_number') }}" @if(!$canCreateMore) disabled @endif required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="interior_number" class="form-label">Número Interior(*)</label>
                                            <input type="text" class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                id="interior_number" name="interior_number" placeholder="Ingresa número interior" 
                                                value="{{ old('interior_number') }}" @if(!$canCreateMore) disabled @endif required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="type" class="form-label">Tipo de toma(*)</label>
                                            <select class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                    id="has_cistern" name="has_cistern" 
                                                    @if(!$canCreateMore) disabled @endif required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ old('type') === '1' ? 'selected' : '' }}>Comercial</option>
                                                <option value="0" {{ old('tipe') === '0' ? 'selected' : '' }}>Residencial</option>
                                            </select>
                                        </div>
                                    </div>                               
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="occupants_number" class="form-label">Número de Ocupantes(*)</label>
                                            <input type="number" min="1" class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                id="occupants_number" name="occupants_number" placeholder="Ingresa número de ocupantes" 
                                                value="{{ old('occupants_number') }}" @if(!$canCreateMore) disabled @endif required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="water_days" class="form-label">Días de Agua(*)</label>
                                            <div class="input-group">
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox"> 
                                                        <input class="custom-control-input @if(!$canCreateMore) disabled-input @endif" 
                                                            type="checkbox" id="monday" name="days[]" value="monday"
                                                            @if(!$canCreateMore) disabled @endif>
                                                        <label class="custom-control-label" for="monday">Lunes</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox">                                                       
                                                       <input class="custom-control-input @if(!$canCreateMore) disabled-input @endif" 
                                                            type="checkbox" id="tuesday" name="days[]" value="tuesday"
                                                            @if(!$canCreateMore) disabled @endif>
                                                        <label class="custom-control-label" for="tuesday">Martes</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox"> 
                                                        <input class="custom-control-input @if(!$canCreateMore) disabled-input @endif" 
                                                            type="checkbox" id="wednesday" name="days[]" value="wednesday"
                                                            @if(!$canCreateMore) disabled @endif>
                                                        <label class="custom-control-label" for="wednesday">Miércoles</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox"> 
                                                        <input class="custom-control-input @if(!$canCreateMore) disabled-input @endif" 
                                                            type="checkbox" id="thursday" name="days[]" value="thursday"
                                                            @if(!$canCreateMore) disabled @endif>
                                                        <label class="custom-control-label" for="thursday">Jueves</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox"> 
                                                        <input class="custom-control-input @if(!$canCreateMore) disabled-input @endif" 
                                                            type="checkbox" id="friday" name="days[]" value="friday"
                                                            @if(!$canCreateMore) disabled @endif>
                                                        <label class="custom-control-label" for="friday">Viernes</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox"> 
                                                        <input class="custom-control-input @if(!$canCreateMore) disabled-input @endif" 
                                                            type="checkbox" id="saturday" name="days[]" value="saturday"
                                                            @if(!$canCreateMore) disabled @endif>
                                                        <label class="custom-control-label" for="saturday">Sábado</label>
                                                    </div>          
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="custom-control custom-checkbox"> 
                                                        <input class="custom-control-input @if(!$canCreateMore) disabled-input @endif" 
                                                            type="checkbox" id="sunday" name="days[]" value="sunday"
                                                            @if(!$canCreateMore) disabled @endif>
                                                        <label class="custom-control-label" for="sunday">Domingo</label>
                                                    </div>    
                                                </div>
                                                <div class="col-lg-5">
                                                    <div class="custom-control custom-checkbox"> 
                                                        <input class="custom-control-input @if(!$canCreateMore) disabled-input @endif" 
                                                            type="checkbox" id="all_days" name="all_days"
                                                            @if(!$canCreateMore) disabled @endif>
                                                        <label class="custom-control-label" for="all_days">Todos los días</label>
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="has_water_pressure" class="form-label">¿Tiene presión de agua?(*)</label>
                                            <select class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                    id="has_water_pressure" name="has_water_pressure" 
                                                    @if(!$canCreateMore) disabled @endif required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ old('has_water_pressure') === '1' ? 'selected' : '' }}>Día si noche no</option>
                                                <option value="0" {{ old('has_water_pressure') === '0' ? 'selected' : '' }}>Noche si día no</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="has_cistern" class="form-label">¿Tiene cisterna?(*)</label>
                                            <select class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                    id="has_cistern" name="has_cistern" 
                                                    @if(!$canCreateMore) disabled @endif required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="1" {{ old('has_cistern') === '1' ? 'selected' : '' }}>Sí</option>
                                                <option value="0" {{ old('has_cistern') === '0' ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                    </div>  
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="cost" class="form-label">Costo(*)</label>
                                            <select class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                    name="cost_id" id="cost" 
                                                    @if(!$canCreateMore) disabled @endif required>
                                                <option value="">Selecciona el costo</option>
                                                @foreach ($costs as $cost)
                                                    <option value="{{ $cost->id }}" {{ old('cost_id') == $cost->id ? 'selected' : '' }}>
                                                        {{ $cost->category }} - ${{ $cost->price }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="note" class="form-label">Nota</label>
                                            <textarea class="form-control @if(!$canCreateMore) disabled-input @endif" 
                                                    id="note" name="note" placeholder="Ingresa una nota"
                                                    @if(!$canCreateMore) disabled @endif></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="save" class="btn btn-success" @if(!$canCreateMore) disabled @endif>
                            @if($canCreateMore)
                                Guardar
                            @else
                                Límite Alcanzado
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .disabled-input {
        background-color: #f8f9fa !important;
        cursor: not-allowed !important;
        opacity: 0.6 !important;
    }
    
    .alert-danger {
        border-left: 4px solid #dc3545;
    }
</style>

<script>
    const allDaysCheckbox = document.getElementById('all_days');
    const dayCheckboxes = document.querySelectorAll('input[name="days[]"]');

    allDaysCheckbox.addEventListener('change', function() {
        if (this.checked) {
            dayCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.disabled = true;
            });
        } else {
            dayCheckboxes.forEach(checkbox => {
                checkbox.disabled = false;
            });
        }
    });

    $(document).ready(function() {
        $('#createWaterConnections').on('shown.bs.modal', function() {
            var modalElement = $(this);
            var dropdownParent = modalElement.find('.modal-body');

            modalElement.find('.select2').each(function() {
                if (!$(this).data('select2')) {
                    $(this).select2({
                        dropdownParent: dropdownParent,
                        allowClear: false,
                        width: '100%'
                    });
                }
            });

            modalElement.on('keydown', function(e) {
                if ($('.select2-container--open').length && e.keyCode === 27) {
                    e.stopPropagation();
                }
            });
        });
    });

    document.getElementById('waterConnectionForm').addEventListener('submit', function(e) {
        @if(!$canCreateMore)
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Límite Alcanzado',
                text: 'No puedes crear más tomas de agua. Contacta al administrador para expandir tu membresía.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#dc3545'
            });
            return false;
        @endif
    });

    $('#createWaterConnections').on('show.bs.modal', function() {
        @if(!$canCreateMore)
            setTimeout(function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Límite de Tomas Alcanzado',
                    html: `
                        <p>Has alcanzado el límite máximo de tomas de agua permitidas por tu membresía.</p>
                        <p><strong>Actual: {{ $currentConnections }} / Límite: {{ $connectionLimit }}</strong></p>
                        <p class="text-danger"><strong>Contacta al administrador para expandir tu membresía.</strong></p>
                    `,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#dc3545'
                });
            }, 500);
        @endif
    });
</script>
