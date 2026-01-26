@extends('adminlte::page')

@section('content')
<div class="container">
    <h3>Cambio de Propietario (Fallecimiento)</h3>

    <div class="card mt-3">
        <div class="card-body">

            <h5>Información de la toma</h5>
            <p><strong>Toma:</strong> {{ $waterConnection->name }}</p>
            <p><strong>Dirección:</strong> {{ $waterConnection->street }} {{ $waterConnection->exterior_number }} {{ $waterConnection->interior_number }}</p>

            <hr>

            <h5>Titular actual (Fallecido)</h5>
            <p><strong>Nombre:</strong> {{ $waterConnection->customer->name }} {{ $waterConnection->customer->last_name }}</p>

            <hr>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('waterConnections.transfer.store', $waterConnection->id) }}">
                @csrf

                <div class="form-group">
                    <label for="new_customer_id">Nuevo titular</label>
                    <select name="new_customer_id" id="new_customer_id" class="form-control" required>
                        <option value="">Selecciona una opción</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->name }} {{ $customer->last_name }} (ID: {{ $customer->id }})
                            </option>
                        @endforeach
                    </select>
                    @error('new_customer_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label for="note">Nota (opcional)</label>
                    <textarea name="note" id="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                    @error('note')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mt-4">
                    <a href="{{ route('waterConnections.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Transferir Toma
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
