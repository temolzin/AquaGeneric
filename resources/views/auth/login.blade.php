@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_body')
    <form action="{{ route('login') }}" method="post">
        @csrf

        <div class="form-group">
            <label class="form-label">Correo electrónico</label>
            <div class="input-group">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="tui@email.com" autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Ingresa tu contraseña">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="remember-forgot">
            <label class="custom-checkbox">
                <input type="checkbox" name="remember" id="remember">
                <span class="checkbox-label">Recordar sesión</span>
            </label>
            <div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i>
            Iniciar sesión
        </button>
    </form>

    <div class="auth-links">
        <p class="auth-text">¿No tienes una cuenta?</p>
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="register-link">Solicitar acceso</a>
        @endif
    </div>
@stop
