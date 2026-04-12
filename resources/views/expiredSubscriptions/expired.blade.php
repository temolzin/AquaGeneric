@extends('layouts.adminlte')

@section('title', 'Suscripción Vencida')

@section('content')
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }

    .subscription-lock-screen {
        height: 100vh;
        width: 100%;
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        color: #0d47a1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 2rem;
    }

    .subscription-lock-screen img {
        width: 120px;
        margin-bottom: 1.5rem;
    }

    .subscription-lock-screen h1 {
        font-size: 2.8rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .subscription-lock-screen p {
        font-size: 1.2rem;
        max-width: 700px;
        margin: 0 auto 2rem;
        color: #1a237e;
    }

    .btn-renew {
        font-size: 1.1rem;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        background-color: #1565c0;
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(21, 101, 192, 0.3);
        transition: all 0.3s ease;
    }

    .btn-renew:hover {
        background-color: #0d47a1;
        transform: scale(1.03);
    }

    .modal-header {
        background-color: #1976d2;
        color: white;
        border-bottom: none;
    }

    .modal-title i {
        font-size: 1.5rem;
        margin-right: 0.5rem;
    }

    .modal-body p {
        color: #333;
        font-size: 1rem;
    }

    .modal-footer .btn {
        font-size: 1rem;
        padding: 0.5rem 1.2rem;
    }

    .modal-content {
        border-radius: 0.75rem;
    }

    .input-group-text {
        background-color: #e3f2fd;
    }
</style>

@php
    use App\Models\User;

    $role = auth()->user()->getRoleNames()->first();
@endphp

@switch($role)
    @case(User::ROLE_SUPERVISOR)
    @case(User::ROLE_SECRETARY)
        <div class="subscription-lock-screen">
            <img src="{{ asset('img/logo.png') }}" alt="Logo del sistema">
            <h1>Suscripción Vencida</h1>
            <p>
                La suscripción de tu localidad ha expirado. Por favor, contacta al administrador para renovar tu membresía y restablecer el acceso al sistema.
            </p>
        </div>
    @break
    @case(User::ROLE_CUSTOMER)
        <div class="subscription-lock-screen">
            <img src="{{ asset('img/logo.png') }}" alt="Logo del sistema">
            <h1>Servicio no disponible</h1>
            <p>
                La membresía de su localidad ha expirado.
                Por favor póngase en contacto con el supervisor para revisar la membresía de su sistema de agua.
            </p>
        </div>
    @break
@endswitch

@endsection

@section('js')
@stop
