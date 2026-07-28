@php
    $locality = $authUser->locality ?? null;
    $verticalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundVertical')
        ? $locality->getFirstMedia('pdfBackgroundVertical')->getPath()
        : public_path('img/backgroundReport.png');
    $currentPage = 0;
@endphp
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Panel de Descuentos</title>
        <style>
            @page {
                size: A4 portrait;
                margin: 0;
            }
            html, body {
                margin: 0;
                padding: 0;
                font-family: 'Montserrat', sans-serif;
            }
            body {
                margin: 0;
                padding: 0;
                background: none;
            }
            .report-page {
                position: relative;
                width: 100%;
                height: 1122px;
                overflow: hidden;
                box-sizing: border-box;
            }
            .page-break {
                page-break-before: always;
            }
            .page-bg {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 1;
            }
            .page-bg img {
                width: 100%;
                height: 100%;
                display: block;
            }
            .page-content {
                position: absolute;
                top: 24px;
                left: 0;
                right: 0;
                bottom: 42px;
                width: 82%;
                margin: 0 auto;
                z-index: 2;
            }
            .page-inner {
                width: 100%;
                margin: 0 auto;
            }
            .report-footer {
                position: absolute;
                left: 0;
                right: 0;
                bottom: 8px;
                text-align: center;
                z-index: 3;
            }
            .text_infoE {
                font-size: 10pt;
                color: white;
                text-decoration: none;
                display: inline-block;
            }
            .page-number {
                font-weight: bold;
                font-size: 10pt;
                letter-spacing: 0.5px;
            }
            .footer-branding {
                color: #0B1C80 !important;
            }
            .footer-branding img {
                filter: brightness(0) saturate(100%) invert(5%) sepia(100%) saturate(10000%) hue-rotate(200deg);
            }
            .first-page-header {
                margin-top: 0;
                margin-bottom: 5px;
            }
            .first-page-logo-row {
                width: 100%;
                position: relative;
                margin-bottom: 4px;
            }
            .first-page-logo {
                text-align: left;
            }
            .first-page-logo img {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                display: inline-block;
            }
            .first-page-title-block {
                margin-top: 70px;
                text-align: center;
            }
            .first-page-title {
                color: #0B1C80;
                font-size: 16pt;
                font-weight: bold;
                text-transform: uppercase;
                margin: 0 0 4px 0;
                line-height: 1.18;
                text-align: center;
            }
            .first-page-subtitle {
                color: #0B1C80;
                font-size: 13pt;
                font-weight: bold;
                margin: 10px 0 0 0;
                text-align: center;
            }
            .first-page-card-title {
                color: #0B1C80;
                font-size: 11.5pt;
                font-weight: bold;
                text-transform: uppercase;
                margin: 15px 0 5px 0;
                text-align: center;
                border-bottom: 2px solid #0B1C80;
                padding-bottom: 4px;
            }
            .inner-page-header {
                width: 100%;
                margin-top: 15px;
                margin-bottom: 10px;
                text-align: center;
            }
            .inner-page-committee {
                color: #ffffff;
                font-size: 8.5pt;
                text-align: center;
                margin: 0 0 4px 0;
                line-height: 1.2;
            }
            .inner-page-title {
                color: #ffffff;
                font-size: 12pt;
                font-weight: bold;
                text-transform: uppercase;
                text-align: center;
                margin: 0;
            }
            .inner-card-title {
                color: #0B1C80;
                font-size: 11.5pt;
                font-weight: bold;
                text-transform: uppercase;
                margin-top: 170px;
                margin-bottom: 4px;
                text-align: center;
                border-bottom: 2px solid #0B1C80;
                padding-bottom: 4px;
            }
            .filter-badge {
                color: #0B1C80;
                font-size: 9pt;
                font-weight: bold;
                text-align: center;
                margin-top: 2px;
                margin-bottom: 4px;
            }
            .chart-container {
                width: 100%;
                text-align: center;
                margin-top: 4px;
                margin-bottom: 12px;
            }
            .chart-container img {
                max-width: 500px;
                max-height: 240px;
                width: auto;
                height: auto;
                display: inline-block;
                margin: 0 auto;
            }
            .report-table {
                width: 100%;
                border-collapse: collapse;
                margin: 8px auto 0 auto;
                table-layout: fixed;
            }
            .report-table thead th {
                background: #0B1C80;
                color: #FFF;
                padding: 7px 5px;
                font-size: 9.5pt;
                text-align: center;
            }
            .report-table tbody tr {
                border-top: 1px solid #bfc9ff;
            }
            .report-table td {
                background-color: transparent;
                text-align: center;
                font-size: 9pt;
                padding: 4px 6px;
                word-wrap: break-word;
                line-height: 1.15;
            }
        </style>
    </head>
    <body>
        @php $currentPage++; @endphp
        <div class="report-page">
            <div class="page-bg">
                <img src="file://{{ $verticalBgPath }}" alt="Background">
            </div>
            <div class="page-content">
                <div class="page-inner">
                    <div class="first-page-header">
                        <div class="first-page-logo-row">
                            <div class="first-page-logo">
                                @if ($locality && $locality->hasMedia('localityGallery'))
                                    <img src="{{ $locality->getFirstMediaUrl('localityGallery') }}" alt="Photo of {{ $locality->name }}">
                                @endif
                                @if (!$locality || !$locality->hasMedia('localityGallery'))
                                    <img src="{{ public_path('img/localityDefault.png') }}" alt="Default Photo">
                                @endif
                            </div>
                        </div>
                        <div class="first-page-title-block">
                            <p class="first-page-title">
                                COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ mb_strtoupper($locality->name ?? '') }}, {{ mb_strtoupper($locality->municipality ?? '') }}, {{ mb_strtoupper($locality->state ?? '') }}
                            </p>
                            <p class="first-page-subtitle">PANEL DE DESCUENTOS</p>
                        </div>
                    </div>
                    <div class="first-page-card-title">1. USO GENERAL DE DESCUENTOS</div>
                    @if(!empty($chartImages['discountChart']))
                        <div class="chart-container">
                            <img src="{{ $chartImages['discountChart'] }}" alt="Uso General de Descuentos">
                        </div>
                    @endif
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th style="width: 15%;">ID</th>
                                <th style="width: 35%;">DESCUENTO</th>
                                <th style="width: 20%;">PORCENTAJE</th>
                                <th style="width: 30%;">TOTAL DE USOS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($firstPageSummary->isNotEmpty())
                                @foreach ($firstPageSummary as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->percentage }}%</td>
                                        <td>{{ $item->total_uses }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if ($firstPageSummary->isEmpty())
                                <tr>
                                    <td colspan="4">No hay datos de descuentos registrados.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="report-footer">
                <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
                <span class="text_infoE"> | </span>
                <span class="text_infoE page-number">Página {{ $currentPage }} de {{ $totalPages }}</span>
                <span class="text_infoE"> | </span>
                <a class="text_infoE footer-branding" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
                <img src="{{ public_path('img/rootheim.png') }}" width="20" height="15" alt="Root Heim" class="footer-branding">
            </div>
        </div>
        @foreach ($otherPagesSummary as $chunk)
            @php $currentPage++; @endphp
            <div class="report-page page-break">
                <div class="page-bg">
                    <img src="file://{{ $verticalBgPath }}" alt="Background">
                </div>
                <div class="page-content">
                    <div class="page-inner">
                        <div class="inner-page-header">
                            <p class="inner-page-committee">
                                COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ mb_strtoupper($locality->name ?? '') }}, {{ mb_strtoupper($locality->municipality ?? '') }}, {{ mb_strtoupper($locality->state ?? '') }}
                            </p>
                            <p class="inner-page-title">PANEL DE DESCUENTOS</p>
                        </div>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">ID</th>
                                    <th style="width: 35%;">DESCUENTO</th>
                                    <th style="width: 20%;">PORCENTAJE</th>
                                    <th style="width: 30%;">TOTAL DE USOS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($chunk as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->percentage }}%</td>
                                        <td>{{ $item->total_uses }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="report-footer">
                    <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
                    <span class="text_infoE"> | </span>
                    <span class="text_infoE page-number">Página {{ $currentPage }} de {{ $totalPages }}</span>
                    <span class="text_infoE"> | </span>
                    <a class="text_infoE footer-branding" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
                    <img src="{{ public_path('img/rootheim.png') }}" width="20" height="15" alt="Root Heim" class="footer-branding">
                </div>
            </div>
        @endforeach
        @php $currentPage++; @endphp
        <div class="report-page page-break">
            <div class="page-bg">
                <img src="file://{{ $verticalBgPath }}" alt="Background">
            </div>
            <div class="page-content">
                <div class="page-inner">
                    <div class="inner-page-header">
                        <p class="inner-page-committee">
                            COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ mb_strtoupper($locality->name ?? '') }}, {{ mb_strtoupper($locality->municipality ?? '') }}, {{ mb_strtoupper($locality->state ?? '') }}
                        </p>
                        <p class="inner-page-title">PANEL DE DESCUENTOS</p>
                    </div>
                    <div class="inner-card-title">2. DESCUENTOS APLICADOS EN PAGOS</div>
                    @if($paymentMonthLabel || $paymentYear)
                        <div class="filter-badge">
                            Filtro aplicado: {{ $paymentMonthLabel ? $paymentMonthLabel : 'Todos los meses' }} {{ $paymentYear ? $paymentYear : '' }}
                        </div>
                    @endif
                    @if(!empty($chartImages['paymentChart']))
                        <div class="chart-container">
                            <img src="{{ $chartImages['paymentChart'] }}" alt="Descuentos aplicados en pagos">
                        </div>
                    @endif
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">DESCUENTO</th>
                                <th style="width: 25%;">PORCENTAJE</th>
                                <th style="width: 35%;">PAGOS APLICADOS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($firstPagePayments->isNotEmpty())
                                @foreach ($firstPagePayments as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->percentage }}%</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if ($firstPagePayments->isEmpty())
                                <tr>
                                    <td colspan="3">No hay pagos registrados con descuentos para el periodo seleccionado.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="report-footer">
                <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
                <span class="text_infoE"> | </span>
                <span class="text_infoE page-number">Página {{ $currentPage }} de {{ $totalPages }}</span>
                <span class="text_infoE"> | </span>
                <a class="text_infoE footer-branding" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
                <img src="{{ public_path('img/rootheim.png') }}" width="20" height="15" alt="Root Heim" class="footer-branding">
            </div>
        </div>
        @foreach ($otherPagesPayments as $chunk)
            @php $currentPage++; @endphp
            <div class="report-page page-break">
                <div class="page-bg">
                    <img src="file://{{ $verticalBgPath }}" alt="Background">
                </div>
                <div class="page-content">
                    <div class="page-inner">
                        <div class="inner-page-header">
                            <p class="inner-page-committee">
                                COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ mb_strtoupper($locality->name ?? '') }}, {{ mb_strtoupper($locality->municipality ?? '') }}, {{ mb_strtoupper($locality->state ?? '') }}
                            </p>
                            <p class="inner-page-title">PANEL DE DESCUENTOS</p>
                        </div>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">DESCUENTO</th>
                                    <th style="width: 25%;">PORCENTAJE</th>
                                    <th style="width: 35%;">PAGOS APLICADOS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($chunk as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->percentage }}%</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="report-footer">
                    <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
                    <span class="text_infoE"> | </span>
                    <span class="text_infoE page-number">Página {{ $currentPage }} de {{ $totalPages }}</span>
                    <span class="text_infoE"> | </span>
                    <a class="text_infoE footer-branding" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
                    <img src="{{ public_path('img/rootheim.png') }}" width="20" height="15" alt="Root Heim" class="footer-branding">
                </div>
            </div>
        @endforeach
        @php $currentPage++; @endphp
        <div class="report-page page-break">
            <div class="page-bg">
                <img src="file://{{ $verticalBgPath }}" alt="Background">
            </div>
            <div class="page-content">
                <div class="page-inner">
                    <div class="inner-page-header">
                        <p class="inner-page-committee">
                            COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ mb_strtoupper($locality->name ?? '') }}, {{ mb_strtoupper($locality->municipality ?? '') }}, {{ mb_strtoupper($locality->state ?? '') }}
                        </p>
                        <p class="inner-page-title">PANEL DE DESCUENTOS</p>
                    </div>
                    <div class="inner-card-title">3. DESCUENTOS APLICADOS EN DEUDAS</div>
                    @if($debtMonthLabel || $debtYear)
                        <div class="filter-badge">
                            Filtro aplicado: {{ $debtMonthLabel ? $debtMonthLabel : 'Todos los meses' }} {{ $debtYear ? $debtYear : '' }}
                        </div>
                    @endif
                    @if(!empty($chartImages['debtChart']))
                        <div class="chart-container">
                            <img src="{{ $chartImages['debtChart'] }}" alt="Descuentos aplicados en deudas">
                        </div>
                    @endif
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">DESCUENTO</th>
                                <th style="width: 25%;">PORCENTAJE</th>
                                <th style="width: 35%;">DEUDAS APLICADAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($firstPageDebts->isNotEmpty())
                                @foreach ($firstPageDebts as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->percentage }}%</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if ($firstPageDebts->isEmpty())
                                <tr>
                                    <td colspan="3">No hay deudas registradas con descuentos para el periodo seleccionado.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="report-footer">
                <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
                <span class="text_infoE"> | </span>
                <span class="text_infoE page-number">Página {{ $currentPage }} de {{ $totalPages }}</span>
                <span class="text_infoE"> | </span>
                <a class="text_infoE footer-branding" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
                <img src="{{ public_path('img/rootheim.png') }}" width="20" height="15" alt="Root Heim" class="footer-branding">
            </div>
        </div>
        @foreach ($otherPagesDebts as $chunk)
            @php $currentPage++; @endphp
            <div class="report-page page-break">
                <div class="page-bg">
                    <img src="file://{{ $verticalBgPath }}" alt="Background">
                </div>
                <div class="page-content">
                    <div class="page-inner">
                        <div class="inner-page-header">
                            <p class="inner-page-committee">
                                COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ mb_strtoupper($locality->name ?? '') }}, {{ mb_strtoupper($locality->municipality ?? '') }}, {{ mb_strtoupper($locality->state ?? '') }}
                            </p>
                            <p class="inner-page-title">PANEL DE DESCUENTOS</p>
                        </div>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">DESCUENTO</th>
                                    <th style="width: 25%;">PORCENTAJE</th>
                                    <th style="width: 35%;">DEUDAS APLICADAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($chunk as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->percentage }}%</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="report-footer">
                    <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
                    <span class="text_infoE"> | </span>
                    <span class="text_infoE page-number">Página {{ $currentPage }} de {{ $totalPages }}</span>
                    <span class="text_infoE"> | </span>
                    <a class="text_infoE footer-branding" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
                    <img src="{{ public_path('img/rootheim.png') }}" width="20" height="15" alt="Root Heim" class="footer-branding">
                </div>
            </div>
        @endforeach
    </body>
</html>
