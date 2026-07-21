@php
    $locality = $authUserLocality ?? Auth::user()->locality ?? null;
    $verticalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundVertical')
        ? $locality->getFirstMedia('pdfBackgroundVertical')->getPath()
        : public_path('img/backgroundReport.png');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Descuentos - {{ $authUserLocality->name ?? '-' }}</title>
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
            left: 18px;
            right: 18px;
            bottom: 42px;
            z-index: 2;
        }

        .page-inner {
            width: 89%;
            margin-left: 3%;
            margin-right: 11%;
        }

        .report-footer {
            position: absolute;
            left: 16px;
            right: 16px;
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

        /* Primera página */
        .first-page-header {
            margin-top: 0;
            margin-bottom: 12px;
        }

        .first-page-logo-row {
            width: 100%;
            position: relative;
            margin-bottom: 8px;
        }

        .first-page-logo {
            text-align: left;
            padding-left: 74px;
        }

        .first-page-logo img {
            width: 112px;
            height: 112px;
            border-radius: 50%;
            display: block;
        }

        .first-page-title-block {
            margin-top: 114px;
            text-align: center;
        }

        .first-page-title {
            color: #0B1C80;
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 10px 0;
            line-height: 1.18;
            text-align: center;
        }

        .first-page-subtitle {
            color: #0B1C80;
            font-size: 14pt;
            font-weight: bold;
            margin: 28px 0 6px 0;
            text-align: center;
        }

        .inner-page-header {
            width: 100%;
            margin-top: 18px;
            margin-bottom: 14px;
            padding-bottom: 0;
            text-align: center;
        }

        .inner-page-committee {
            color: #ffffff;
            font-size: 8.5pt;
            text-align: center;
            margin: 0 0 4px 0;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .inner-page-title {
            color: #ffffff;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin: 0;
        }

        .date-range {
            color: #0B1C80;
            font-size: 10pt;
            text-align: center;
            margin-bottom: 10px;
        }

        h4.day-title {
            color: #0B1C80;
            text-align: left;
            margin-top: 15px;
            margin-bottom: 8px;
            font-size: 10pt;
        }

        h3.module-title {
            color: #0B1C80;
            text-transform: uppercase;
            margin-top: 20px;
            text-align: center;
            font-size: 13pt;
            border-bottom: 2px solid #0B1C80;
            padding-bottom: 4px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 15px;
            table-layout: fixed;
        }

        .report-table thead th {
            background: #0B1C80;
            color: #FFF;
            padding: 6px 4px;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
        }

        .report-table tbody tr {
            border-top: 1px solid #bfc9ff;
        }

        .report-table td {
            background-color: transparent;
            text-align: center;
            font-size: 7.5pt;
            padding: 4px;
            word-wrap: break-word;
            line-height: 1.15;
            vertical-align: top;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
            font-size: 10pt;
        }

        .error-message {
            text-align: center;
            color: #dc3545;
            font-weight: bold;
            padding: 40px;
            font-size: 12pt;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 5px;
            margin: 20px;
        }
    </style>
</head>
<body>
    @if(isset($error))
        <div class="report-page">
            <div class="page-bg">
                <img src="file://{{ $verticalBgPath }}" alt="Background">
            </div>
            <div class="page-content">
                <div class="page-inner">
                    <div class="error-message">
                        <p>{{ $error }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!isset($error) && empty($groupedByDay) && empty($groupedByModule))
        <div class="report-page">
            <div class="page-bg">
                <img src="file://{{ $verticalBgPath }}" alt="Background">
            </div>
            <div class="page-content">
                <div class="page-inner">
                    <div class="no-data">
                        <p>No se encontraron registros para los filtros seleccionados.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!isset($error) && $reportType === 'all-modules-grouped' && (!empty($groupedByDay) || !empty($groupedByModule)))
        @foreach($groupedByModule as $moduleName => $days)
            <div class="report-page {{ !$loop->first ? 'page-break' : '' }}">
                <div class="page-bg">
                    <img src="file://{{ $verticalBgPath }}" alt="Background">
                </div>
                <div class="page-content">
                    <div class="page-inner">
                        @if($loop->first)
                            <div class="first-page-header">
                                <div class="first-page-logo-row">
                                    <div class="first-page-logo">
                                        @if ($authUserLocality && $authUserLocality->hasMedia('localityGallery'))
                                            <img src="{{ $authUserLocality->getFirstMediaUrl('localityGallery') }}" alt="Logo">
                                        @endif
                                        @if (!$authUserLocality || !$authUserLocality->hasMedia('localityGallery'))
                                            <img src="{{ public_path('img/localityDefault.png') }}" alt="Default Photo">
                                        @endif
                                    </div>
                                </div>
                                <div class="first-page-title-block">
                                    <p class="first-page-title">
                                        COMITÉ DEL SISTEMA DE AGUA POTABLE DE<br>
                                        {{ $authUserLocality->name ?? '-' }}, {{ $authUserLocality->municipality ?? '-' }}, {{ $authUserLocality->state ?? '-' }}
                                    </p>
                                    <p class="first-page-subtitle">{{ $reportTitles[$reportType] ?? 'HISTORIAL DE DESCUENTOS' }}</p>
                                    @if($startDate || $endDate)
                                        <p class="date-range">
                                            @if($startDate) Desde: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} @endif
                                            @if($endDate) Hasta: {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(!$loop->first)
                            <div class="inner-page-header">
                                <p class="inner-page-committee">
                                    COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUserLocality->name ?? '-' }}, {{ $authUserLocality->municipality ?? '-' }}, {{ $authUserLocality->state ?? '-' }}
                                </p>
                                <p class="inner-page-title">{{ $reportTitles[$reportType] ?? 'HISTORIAL DE DESCUENTOS' }}</p>
                            </div>
                        @endif

                        <div class="report-section">
                            <h3 class="module-title">{{ strtoupper($moduleName) }}</h3>
                            @foreach($days as $day => $histories)
                                <h4 class="day-title">{{ $day }}</h4>
                                <table class="report-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Módulo</th>
                                            <th>Descuento</th>
                                            <th>Cliente</th>
                                            <th>ID Registro</th>
                                            <th>Monto Original</th>
                                            <th>Monto Descontado</th>
                                            <th>Monto Final</th>
                                            <th>Creado Por</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($histories as $history)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}</td>
                                                <td>{{ $moduleNames[$history->module] ?? $history->module }}</td>
                                                <td>{{ $history->discount?->name ?? '-' }}</td>
                                                <td>{{ $history->customer ? trim($history->customer->name . ' ' . $history->customer->last_name) : '-' }}</td>
                                                <td>{{ $history->record_id }}</td>
                                                <td>${{ number_format($history->original_amount, 2, '.', ',') }}</td>
                                                <td>${{ number_format($history->discount_amount, 2, '.', ',') }}</td>
                                                <td>${{ number_format($history->final_amount, 2, '.', ',') }}</td>
                                                <td>{{ $history->creator ? trim($history->creator->name . ' ' . $history->creator->last_name) : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="report-footer">
                    <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
                    <span class="text_infoE"> | </span>
                    <span class="text_infoE page-number">Página {{ $loop->iteration }}</span>
                    <span class="text_infoE"> | </span>
                    <a class="text_infoE footer-branding" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
                    <img src="{{ public_path('img/rootheim.png') }}" width="20" height="15" alt="Root Heim" class="footer-branding">
                </div>
            </div>
        @endforeach
    @endif

    @if(!isset($error) && $reportType === 'single-module' && (!empty($groupedByDay) || !empty($groupedByModule)))
        <div class="report-page">
            <div class="page-bg">
                <img src="file://{{ $verticalBgPath }}" alt="Background">
            </div>
            <div class="page-content">
                <div class="page-inner">
                    <div class="first-page-header">
                        <div class="first-page-logo-row">
                            <div class="first-page-logo">
                                @if ($authUserLocality && $authUserLocality->hasMedia('localityGallery'))
                                    <img src="{{ $authUserLocality->getFirstMediaUrl('localityGallery') }}" alt="Logo">
                                @endif
                                @if (!$authUserLocality || !$authUserLocality->hasMedia('localityGallery'))
                                    <img src="{{ public_path('img/localityDefault.png') }}" alt="Default Photo">
                                @endif
                            </div>
                        </div>
                        <div class="first-page-title-block">
                            <p class="first-page-title">
                                COMITÉ DEL SISTEMA DE AGUA POTABLE DE<br>
                                {{ $authUserLocality->name ?? '-' }}, {{ $authUserLocality->municipality ?? '-' }}, {{ $authUserLocality->state ?? '-' }}
                            </p>
                            <p class="first-page-subtitle">{{ $reportTitles[$reportType] ?? 'HISTORIAL DE DESCUENTOS' }}</p>
                            @if($startDate || $endDate)
                                <p class="date-range">
                                    @if($startDate) Desde: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} @endif
                                    @if($endDate) Hasta: {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    @foreach($groupedByDay as $day => $histories)
                        <div class="report-section">
                            <h4 class="day-title">{{ $day }}</h4>
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Módulo</th>
                                        <th>Descuento</th>
                                        <th>Cliente</th>
                                        <th>ID Registro</th>
                                        <th>Monto Original</th>
                                        <th>Monto Descontado</th>
                                        <th>Monto Final</th>
                                        <th>Creado Por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($histories as $history)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $moduleNames[$history->module] ?? $history->module }}</td>
                                            <td>{{ $history->discount?->name ?? '-' }}</td>
                                            <td>{{ $history->customer ? trim($history->customer->name . ' ' . $history->customer->last_name) : '-' }}</td>
                                            <td>{{ $history->record_id }}</td>
                                            <td>${{ number_format($history->original_amount, 2, '.', ',') }}</td>
                                            <td>${{ number_format($history->discount_amount, 2, '.', ',') }}</td>
                                            <td>${{ number_format($history->final_amount, 2, '.', ',') }}</td>
                                            <td>{{ $history->creator ? trim($history->creator->name . ' ' . $history->creator->last_name) : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="report-footer">
                <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
                <span class="text_infoE"> | </span>
                <span class="text_infoE page-number">Página 1</span>
                <span class="text_infoE"> | </span>
                <a class="text_infoE footer-branding" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
                <img src="{{ public_path('img/rootheim.png') }}" width="20" height="15" alt="Root Heim" class="footer-branding">
            </div>
        </div>
    @endif
</body>
</html>