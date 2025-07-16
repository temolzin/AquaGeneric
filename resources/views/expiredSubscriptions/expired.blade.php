@extends('adminlte::page')

@section('title', 'Suscripción Vencida')

@section('content')
<style>
    .full-height {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="full-height">
    <div class="card shadow-lg border-danger" style="max-width: 600px; width: 100%;">
        <div class="card-header bg-danger text-white text-center">
            <h3><i class="fas fa-exclamation-triangle"></i> Suscripción Vencida</h3>
        </div>
        <div class="card-body text-center">
            <p class="mb-4 fs-5">
                <strong>Tu suscripción ha vencido.</strong><br>
                Por favor, contacta al administrador o renueva tu plan para seguir utilizando el sistema.
            </p>
            <a href="#" class="btn btn-warning btn-lg">Renovar Suscripción</a>
        </div>
    </div>
</div>
@stop
