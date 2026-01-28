<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
    <meta charset="UTF-8">
    <title>@yield('title', 'Landing')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/index/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/index/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/index/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/index/css/jquery.mCustomScrollbar.min.css') }}">
    @stack('styles')
</head>
<body class="main-layout"> 
    <div class="loader_bg">
        <div class="loader">
            <img src="{{ asset('img/loading_waterdrop.gif') }}" alt="">
        </div>
    </div>

    @yield('content')

    <script src="{{ asset('assets/index/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/index/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/index/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/index/js/plugin.js') }}"></script>
    <script src="{{ asset('assets/index/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script src="{{ asset('assets/index/js/custom.js') }}"></script>

    @stack('scripts')
</body>
</html>
