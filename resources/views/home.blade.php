@extends('layouts.landing')

@section('title', 'Inicio')

@section('content')
    <header>
        <div class="head_top">
            <div class="header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <nav class="navigation navbar navbar-expand-md navbar-dark">
                                <button class="navbar-toggler" type="button" data-toggle="collapse"
                                    data-target="#navbarsExample04" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon" aria-hidden="true">
                                        <svg viewBox="0 0 30 30" width="26" height="26" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                            <path d="M4 7h22M4 15h22M4 23h22" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"></path>
                                        </svg>
                                    </span>
                                </button>
                                <div class="collapse navbar-collapse" id="navbarsExample04">
                                    <ul class="navbar-nav mr-auto">
                                        <li class="nav-item active">
                                            <a class="nav-link" href="{{ url('/') }}">Inicio</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#about">Sobre el sistema</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#features">Funcionalidades</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#approach">Enfoque</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#contact">Contacto</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('login') }}">Acceder</a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <section class="banner_main">
                <div class="container-fluid">
                    <div class="row d_flex align-items-center">
                        <div class="col-md-6">
                            <div class="text-bg">
                                <img src="{{ asset('img/logo_b.png') }}" alt="AquaControl Logo"
                                    class="hero-logo">
                                <h1>AquaControl</h1>
                                <p>
                                    Plataforma digital para la gestión eficiente del servicio de agua en comunidades,
                                    juntas y comités locales.
                                    Centraliza pagos, usuarios, reportes y cargos en un solo lugar, mejorando la
                                    transparencia y reduciendo errores administrativos.
                                </p>
                                <a href="#features" class="read_more">Conoce más</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-img">
                                <figure>
                                    <img src="{{ asset('img/box_img.png') }}" alt="Vista del sistema">
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </header>
    <div id="about" class="business">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="titlepage">
                        <span>¿Qué es AquaControl?</span>
                        <h2>Gestión moderna del servicio de agua</h2>
                        <p>
                            AquaControl nace para facilitar la administración del agua en comunidades que
                            aún dependen de procesos manuales, hojas de cálculo o registros dispersos.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <figure>
                        <img src="{{ asset('img/control_panel.jpg') }}" alt="Gestión comunitaria">
                    </figure>
                </div>
                <div class="col-md-6">
                    <div class="business_box">
                        <p>
                            El sistema permite llevar un control claro de usuarios, pagos mensuales,
                            adeudos, reportes y movimientos financieros, todo desde una interfaz sencilla
                            y accesible.
                        </p>
                        <p>
                            Está diseñado pensando en comités, juntas de agua y administradores locales,
                            sin necesidad de conocimientos técnicos avanzados.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="features" class="projects">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="titlepage">
                        <span>Funcionalidades principales</span>
                        <h2>Todo lo que necesitas en un solo sistema</h2>
                        <p>
                            AquaControl reúne las herramientas esenciales para una administración clara,
                            ordenada y transparente.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="projects_box feature-box">
                        <i class="fas fa-users feature-icon"></i>
                        <h4>Control de usuarios</h4>
                        <p>
                            Registro y gestión de usuarios, tomas de agua y datos de contacto,
                            todo centralizado en un solo sistema.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="projects_box feature-box">
                        <i class="fas fa-credit-card feature-icon"></i>
                        <h4>Pagos y adeudos</h4>
                        <p>
                            Seguimiento detallado de pagos, historial de movimientos y control
                            claro de adeudos pendientes.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="projects_box feature-box">
                        <i class="fas fa-chart-bar feature-icon"></i>
                        <h4>Reportes</h4>
                        <p>
                            Generación de reportes financieros y operativos para facilitar
                            la toma de decisiones.
                        </p>
                    </div>
                </div>
            </div>
            <div class="section">
                <div class="container">
                    <div class="Testimonial" id="approach">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="titlepage">
                                    <h2>Enfoque comunitario</h2>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="Testimonial_box">
                                    <i>
                                        <img src="{{ asset('img/customer.jpg') }}" alt="">
                                    </i>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="Testimonial_box">
                                    <h4>Transparencia y confianza</h4>
                                    <p>
                                        AquaControl hace más fácil la relación entre usuarios y administración al brindar información clara 
                                        y siempre disponible sobre pagos, adeudos y estado del servicio. Desde un solo lugar, cada persona 
                                        puede consultar su situación, reportar inconvenientes y dar seguimiento a sus solicitudes, logrando 
                                        una comunicación más ágil y una gestión más ordenada y cercana para toda la comunidad.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="contact" class="contact">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="titlepage">
                                <h2>Contacto</h2>
                                <span>
                                    ¿Te interesa implementar AquaControl en tu comunidad?
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <form class="main_form" action="{{ route('contact.send') }}" method="POST">
                                @csrf

                                @if(session('contact_success'))
                                    <div class="alert alert-success" style="margin-bottom: 15px;">
                                        {{ session('contact_success') }}
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger" style="margin-bottom: 15px;">
                                        <ul style="margin: 0; padding-left: 20px;">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form_contril" name="name" placeholder="Nombre" type="text" value="{{ old('name') }}" required>
                                    </div>
                                    <div class="col-md-12">
                                        <input class="form_contril" name="email" placeholder="Correo electrónico" type="email" value="{{ old('email') }}" required>
                                    </div>
                                    <div class="col-md-12">
                                        <textarea class="textarea" name="message" placeholder="Mensaje" required>{{ old('message') }}</textarea>
                                    </div>
                                    <div class="col-sm-12">
                                        <button type="submit" class="send_btn">Enviar mensaje</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="site-footer">
                <div class="footer-content">
                    <div class="cont">
                        <h3>
                            <strong class="multi">AquaControl</strong><br>
                            Gestión inteligente del agua
                        </h3>
                    </div>
                    <ul class="social_icon">
                        <li>
                            <a href="https://www.facebook.com/rootheimcompany/" target="_blank" rel="noopener">
                                <i class="fab fa-facebook"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/rootheimcompany/" target="_blank" rel="noopener">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://mx.linkedin.com/company/rootheim" target="_blank" rel="noopener">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="copyright">
                    <p>© {{ date('Y') }} AquaControl. Todos los derechos reservados.</p>
                </div>
            </footer>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                try {
                    var $t = $('.navigation .navbar-toggler');
                    var $c = $('#navbarsExample04');
                    if ($t.length && $c.length) {
                        $c.css({ 'display': $c.hasClass('show') ? 'block' : 'none' });
                        $t.removeAttr('data-toggle').removeAttr('data-target');
                            $t.off('click._customToggle').on('click._customToggle', function (e) {
                                e.preventDefault();
                                if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                                const isOpen = $c.hasClass('show');
                                $c
                                    .toggleClass('show', !isOpen)
                                    .stop(true, true)
                                    ;
                                $t.attr('aria-expanded', String(!isOpen));
                                return false;
                            });
                    }
                } catch (err) {
                }
            });
        </script>
@endsection

<a href="https://wa.me/{{ env('WHATSAPP_NUMBER') }}?text={{ urlencode(env('WHATSAPP_MESSAGE', 'Hola, estoy interesado en AquaControl. ¿Me pueden ayudar?')) }}"
    class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Chat por WhatsApp">
    <i class="fab fa-whatsapp" aria-hidden="true"></i>
</a>
