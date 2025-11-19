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
                        <small>
                            <strong>Límites:</strong> {{ $locality->membership->users_number }} usuarios / {{ $locality->membership->water_connections_number }} tomas<br>
                            <strong>Duración:</strong> {{ $locality->membership->term_months }} meses<br>
                            <strong>Precio:</strong> ${{ number_format($locality->membership->price, 2) }}<br>
                            @if($locality->membership_assigned_at)
                                @php
                                    $assignedDate = \Carbon\Carbon::parse($locality->membership_assigned_at);
                                    $endDate = $assignedDate->copy()->addMonths($locality->membership->term_months);
                                @endphp
                                <strong>Asignada el:</strong> {{ $assignedDate->format('d/m/Y H:i') }}<br>
                                <strong>Termina el:</strong> {{ $endDate->format('d/m/Y H:i') }}
                            @else
                                <strong>Asignada el:</strong> <span class="text-muted">Fecha no registrada</span><br>
                                <strong>Termina el:</strong> <span class="text-muted">No calculable</span>
                            @endif
                        </small>
                    </div>
                    @endif
                    <div class="form-group">
                        <label>Fecha de Inicio</label>
                        <input type="date" name="startDate" class="form-control"
                            value="{{ date('Y-m-d') }}"
                            required readonly
                            style="background-color: #f8f9fa; cursor: not-allowed;">
                        <small class="form-text text-muted">
                            Fecha actual (no modificable)
                        </small>
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
                                    {{ $membership->water_connections_number }} tomas -
                                    ${{ number_format($membership->price, 2) }}
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
