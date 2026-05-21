<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaControl | Error</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
      theme: {
        extend: {
          colors: {
            aqua: {
              dark: '#1d4ed8',
              blue: '#60a5fa',
              light: '#f4f7f9'
            }
          }
        }
      }
    }
    </script>
</head>

<body class="bg-aqua-light min-h-screen flex items-center justify-center font-sans antialiased">
    <div class="max-w-xl w-full text-center px-6 py-12 flex flex-col items-center">
        <div class="w-auto h-40 mb-4 flex items-center justify-center">
            <img src="{{ asset('img/logo.png') }}" alt="logo aquacontrol" class="h-full object-contain">
        </div>
        <span class="text-2xl font-bold text-slate-900 tracking-tight">AquaControl</span>
        <span class="mt-4 text-5xl font-extrabold tracking-tight text-slate-700">419</span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-wider text-aqua-dark sm:text-4xl uppercase">
            sesión expirada
        </h1>
        <h1 class="mt-4 text-base font-medium text-slate-600 max-w-md uppercase leading-relaxed text-center">
            Lo sentimos, tu sesión ha expirado por inactividad. Por favor, recarga la
            página o intenta iniciar sesión nuevamente.
        </h1>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 w-full justify-center items-center">
            <a href="{{ url('/') }}"
                class="w-56 rounded-full bg-transparent border-2 border-aqua-dark py-3 px-6 text-sm font-bold text-aqua-dark hover:bg-aqua-dark hover:text-white transition-all uppercase tracking-wider text-center">
                Rolver al inicio
            </a>
            <a href="{{ url()->current() }}"
                class="w-56 rounded-full bg-aqua-dark py-3 px-6 text-sm font-bold text-white shadow-md hover:bg-opacity-90 transition-all uppercase tracking-wider text-center">
                Recargar Página
            </a>
            
        </div>
    </div>
</body>
</html>
