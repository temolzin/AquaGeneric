<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AquaControl | Login</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    
    @php
        $authType = $authType ?? 'login';
        $dashboardUrl = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home');

        if (config('adminlte.use_route_url', false)) {
            $dashboardUrl = $dashboardUrl ? route($dashboardUrl) : '';
        } else {
            $dashboardUrl = $dashboardUrl ? url($dashboardUrl) : '';
        }

        $bodyClasses = "{$authType}-page";

        if (! empty(config('adminlte.layout_dark_mode', null))) {
            $bodyClasses .= ' dark-mode';
        }
    @endphp

    <style>
        .login-page, .register-page, .verify-page {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .unified-login-container {
            display: flex;
            max-width: 780px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 18px; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.25); 
            overflow: hidden;
            backdrop-filter: blur(10px);
            min-height: 520px;
        }

        .info-section {
            flex: 1;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            padding: 35px 30px; 
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .info-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: cover;
        }

        .info-content {
            position: relative;
            z-index: 2;
        }

        .app-title {
            font-size: 2.1rem; 
            font-weight: 800;
            margin-bottom: 8px; 
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .welcome-title {
            font-size: 1.5rem; 
            font-weight: 600;
            margin-bottom: 15px; 
            opacity: 0.95;
        }

        .app-description {
            font-size: 0.95rem; 
            margin-bottom: 30px; 
            opacity: 0.9;
            line-height: 1.5;
        }

        .features-list {
            margin-top: 25px; 
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px; 
            padding: 12px 15px; 
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px; 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateX(8px); 
        }

        .feature-checkbox {
            width: 18px; 
            height: 18px; 
            margin-right: 12px; 
            accent-color: #fff;
            cursor: default;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .feature-label {
            font-weight: 500;
            font-size: 0.9rem; 
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        .login-section {
            flex: 0 0 380px; 
            padding: 35px 30px; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px; 
        }

        .login-logo {
            font-size: 1.9rem; 
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px; 
        }

        .login-subtitle {
            color: #64748b;
            font-size: 0.95rem; 
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px; 
        }

        .form-label {
            display: block;
            margin-bottom: 6px; 
            color: #374151;
            font-weight: 600;
            font-size: 0.85rem; 
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 12px; 
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            z-index: 2;
            font-size: 0.9rem; 
        }

        .form-control {
            width: 100%;
            padding: 12px 12px 12px 40px; 
            border: 2px solid #e5e7eb;
            border-radius: 10px; 
            background: #f9fafb;
            font-size: 0.9rem; 
            transition: all 0.3s ease;
            color: #1f2937;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-control::placeholder {
            color: #9ca3af;
            font-size: 0.85rem; 
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0; 
        }

        .custom-checkbox {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .custom-checkbox input[type="checkbox"] {
            width: 16px; 
            height: 16px; 
            border: 2px solid #d1d5db;
            border-radius: 4px;
            background: #fff;
            cursor: pointer;
            position: relative;
            margin-right: 8px; 
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            transition: all 0.3s ease;
        }

        .custom-checkbox input[type="checkbox"]:checked {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .custom-checkbox input[type="checkbox"]:checked::after {
            content: '✓';
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 10px; 
            font-weight: bold;
        }

        .checkbox-label {
            color: #374151;
            font-weight: 500;
            font-size: 0.85rem; 
        }

        .forgot-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem; 
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px; 
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 10px; 
            color: white;
            font-size: 0.95rem; 
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4); 
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px; 
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5); 
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .auth-links {
            text-align: center;
            margin-top: 25px; 
            padding-top: 20px; 
            border-top: 1px solid #e5e7eb;
        }

        .auth-text {
            color: #6b7280;
            margin-bottom: 8px; 
            font-weight: 500;
            font-size: 0.85rem; 
        }

        .register-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem; 
            transition: color 0.3s ease;
        }

        .register-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .unified-login-container {
                flex-direction: column;
                max-width: 350px; 
                min-height: auto;
            }
            
            .info-section {
                padding: 30px 25px; 
            }
            
            .login-section {
                flex: none;
                padding: 30px 25px; 
            }
            
            .app-title {
                font-size: 1.8rem; 
            }
            
            .welcome-title {
                font-size: 1.3rem; 
            }
        }

        @media (max-width: 480px) {
            .unified-login-container {
                max-width: 320px;
                border-radius: 15px;
            }
            
            .info-section, .login-section {
                padding: 25px 20px;
            }
        }
        .logo{
            width: 120px;
            height: 120px;
        }
    </style>

    @stack('css')
    @yield('css')
</head>
<body class="hold-transition {{ $bodyClasses }}">

    <div class="unified-login-container">
        
        <div class="info-section">
            <div class="info-content">
                <h1 class="app-title">AquaControl</h1>
                <h2 class="welcome-title">Bienvenido de vuelta</h2>
                <p class="app-description">
                    Gestiona tu sistema de agua de manera eficiente y moderna
                </p>
                
                <div class="features-list">
                    <div class="feature-item">
                        <input type="checkbox" class="feature-checkbox" id="info-feature1" disabled>
                        <label for="info-feature1" class="feature-label">Dashboard en tiempo real</label>
                    </div>
                    <div class="feature-item">
                        <input type="checkbox" class="feature-checkbox" id="info-feature2" disabled>
                        <label for="info-feature2" class="feature-label">Gestión de clientes</label>
                    </div>
                    <div class="feature-item">
                        <input type="checkbox" class="feature-checkbox" id="info-feature3" disabled>
                        <label for="info-feature3" class="feature-label">Control de pagos</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="login-section">
            <div class="login-header">
                <div class="logo-container">
                    <img src="{{ asset('img/logo.png') }}" alt="AquaControl" class="logo">
                </div>
                <div class="login-logo">AquaControl</div>
                <p class="login-subtitle">Inicia sesión para acceder</p>
            </div>

            <div class="login-body">
                @yield('auth_body')
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

    @stack('js')
    @yield('js')
</body>
</html>
