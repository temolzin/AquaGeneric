@extends('adminlte::page')

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

<div class="subscription-lock-screen">
    <img src="{{ asset('img/logo.png') }}" alt="Logo del sistema">
    <h1>Acceso Restringido</h1>
    <p>
        La suscripción al sistema de gestión de agua ha expirado. Si ya realizaste el pago, por favor ingresa el token de renovación para restablecer el acceso.
    </p>
    <button class="btn btn-renew" data-toggle="modal" data-target="#tokenModal">
        Ingresar Token de Renovación
    </button>
</div>

<div class="modal fade" id="tokenModal" tabindex="-1" role="dialog" aria-labelledby="tokenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="tokenModalLabel">
                    <i class="fas fa-key"></i> Validación de Token
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p>
                    Si ya realizaste el pago, por favor ingresa el <strong>token de renovación</strong> proporcionado por el administrador para reactivar tu cuenta.
                </p>
                <div class="input-group mt-4 mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock text-primary"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Pega aquí tu token de renovación...">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Validar Token</button>
            </div>
        </div>
    </div>
</div>
@stop
