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
                    <div class="Testimonial">
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
                                        {{-- Icono o imagen representativa --}}
                                        <img src="{{ asset('img/customer.jpg') }}"
                                            alt="">
                                    </i>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="Testimonial_box">
                                    <h4>Transparencia y confianza</h4>
                                    <p>
                                        El objetivo principal es fortalecer la confianza entre administradores y
                                        usuarios mediante información clara, accesible y actualizada.
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
                            <form action="{{ route('contact.send') }}" method="POST" class="main_form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <input name="name" class="form_contril" placeholder="Nombre" type="text" required>
                                    </div>
                                    <div class="col-md-12">
                                        <input name="email" class="form_contril" placeholder="Correo electrónico" type="email" required>
                                    </div>
                                    <div class="col-md-12">
                                        <textarea name="message" class="textarea" placeholder="Mensaje" required></textarea>
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
            <footer>
                <div class="footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="cont">
                                    <h3>
                                        <strong class="multi">AquaControl</strong><br>
                                        Gestión inteligente del agua
                                    </h3>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <ul class="social_icon">
                                    <ul class="social_icon">
                                        <li>
                                            <a href="https://www.facebook.com/rootheimcompany/" target="_blank"
                                                rel="noopener">
                                                <i class="fab fa-facebook"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.instagram.com/rootheimcompany/" target="_blank" rel="noopener">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://mx.linkedin.com/company/rootheim" target="_blank"
                                                rel="noopener">
                                                <i class="fab fa-linkedin"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="copyright">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>
                                        © {{ date('Y') }} AquaControl. Todos los derechos reservados.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
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
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var successMessage = "{{ session('success') }}";
                if (successMessage) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: successMessage,
                        confirmButtonText: 'Aceptar'
                    });
                }
                var errorMessage = "{{ session('error') }}";
                if (errorMessage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        </script>
        @endsection
