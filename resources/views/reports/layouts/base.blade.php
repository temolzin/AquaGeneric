<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle ?? 'Reporte' }}</title>
    @include('reports.partials.styles')
    @stack('styles')
</head>
<body>
    @include('reports.partials.header', [
        'reportTitle'  => $reportTitle ?? null,
        'generatedAt'  => $generatedAt ?? null,
        'generatedBy'  => $generatedBy ?? null,
        'logoPath'     => $logoPath ?? null,
        'localityLine' => $localityLine ?? null,
    ])

    <main>
        <h1 class="title">@yield('title')</h1>
        @hasSection('subtitle')
            <p class="subtitle">@yield('subtitle')</p>
        @endif

        @yield('content')
    </main>

    @include('reports.partials.footer')
</body>
</html>
