<div class="modal fade" id="generateTokenModal{{ $locality->id }}" tabindex="-1" role="dialog" aria-labelledby="tokenModalLabel{{ $locality->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('localities.generateToken') }}" method="POST">
            @csrf
            <input type="hidden" name="idLocality" value="{{ $locality->id }}">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #fd7e14; color: white;">
                    <h5 class="modal-title">Generar Token - {{ $locality->name }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($locality->membership)
                    <div class="alert alert-info">
                        <strong>Membresía actual:</strong> {{ $locality->membership->name }}<br>
                        <small>Límites: {{ $locality->membership->users_number }} usuarios / {{ $locality->membership->water_connections_number }} tomas</small>
                    </div>
                    @endif
                    <div class="form-group">
                        <label>Fecha de Inicio</label>
                        <input type="date" name="startDate" class="form-control" value="{{ date('Y-m-d') }}" required readonly>
                    </div>
                    <div class="form-group">
                        <label>Plan de Membresía</label>
                        <select name="membership_id" class="form-control" required>
                            <option value="">Seleccionar plan</option>
                            @foreach($memberships as $membership)
                                <option value="{{ $membership->id }}" 
                                    {{ $locality->membership_id == $membership->id ? 'selected' : '' }}>
                                    {{ $membership->name }} - 
                                    {{ $membership->term_months }} meses - 
                                    {{ $membership->users_number }} usuarios - 
                                    {{ $membership->water_connections_number }} tomas
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Esta membresía se asignará a la localidad y definirá los límites de usuarios y tomas.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning" style="background-color: #fd7e14; color: white;">Generar Token y Asignar Membresía</button>
                </div>
            </div>
        </form>
    </div>
</div>
