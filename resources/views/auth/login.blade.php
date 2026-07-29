@extends('vendor.adminlte.auth.auth-page', ['auth_type' => 'login'])
@section('auth_header', 'Iniciar Sesión')

@section('auth_body')

    @php
        $lockoutSeconds = session('lockout_seconds', 0);
        $isLocked = $lockoutSeconds > 0;
    @endphp

    <form action="{{ route('login') }}" method="post" id="login-form">
        @csrf
        <div class="input-group mb-3">
            <input type="email"
                   name="email"
                   id="input-email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="Email"
                   value="{{ old('email') }}"
                   {{ $isLocked ? 'readonly' : '' }}
                   autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback d-block w-100" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="input-group mb-3">
            <input type="password"
                   name="password"
                   id="input-password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Contraseña"
                   {{ $isLocked ? 'readonly' : '' }}>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback d-block w-100" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        @if(config('services.recaptcha.site_key'))
        <div class="d-flex justify-content-center mb-3 flex-column align-items-center">
            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
            @error('g-recaptcha-response')
                <span class="invalid-feedback d-block text-center" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            @error('captcha')
                <span class="invalid-feedback d-block text-center" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        @endif
        <button type="submit" id="btn-submit" class="btn-login" {{ $isLocked ? 'disabled' : '' }}>
            <i class="fas fa-sign-in-alt"></i> Acceder
        </button>
    </form>
    @if(config('services.recaptcha.site_key'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    @if ($isLocked)
        <div id="lockout-timer" class="alert alert-danger text-center mt-3">
            <i class="fas fa-user-lock mr-2"></i>
            <strong>Acceso Bloqueado</strong><br>
            Reintento en:
            <span id="timer-display"
                  style="display:inline-block;background:#1e293b;color:#fff;border-radius:6px;
                         padding:2px 10px;font-size:1.1rem;font-weight:700;letter-spacing:2px;">
                {{ sprintf('%02d:%02d', floor($lockoutSeconds / 60), $lockoutSeconds % 60) }}
            </span>
        </div>

        <script>
            (function () {
                let secondsLeft = {{ $lockoutSeconds }};

                const btn      = document.getElementById('btn-submit');
                const emailIn  = document.getElementById('input-email');
                const passIn   = document.getElementById('input-password');
                const timerBox = document.getElementById('lockout-timer');
                const display  = document.getElementById('timer-display');

                function pad(n) { return n < 10 ? '0' + n : String(n); }

                display.textContent = pad(Math.floor(secondsLeft / 60)) + ':' + pad(secondsLeft % 60);

                const interval = setInterval(function () {
                    secondsLeft--;

                    if (secondsLeft <= 0) {
                        clearInterval(interval);
                        timerBox.className = 'alert alert-success text-center mt-3';
                        timerBox.innerHTML = '<i class="fas fa-unlock mr-2"></i> <strong>Puedes intentarlo de nuevo.</strong>';
                        btn.disabled      = false;
                        emailIn.readOnly  = false;
                        passIn.readOnly   = false;
                        return;
                    }

                    display.textContent = pad(Math.floor(secondsLeft / 60)) + ':' + pad(secondsLeft % 60);
                }, 1000);
            })();
        </script>
    @endif
@stop