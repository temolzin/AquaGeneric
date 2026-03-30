@php
    $locality = Auth::user()->locality ?? null;
    $verticalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundVertical')
        ? $locality->getFirstMedia('pdfBackgroundVertical')->getPath()
        : public_path('img/backgroundReport.png');
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Lista de Clientes</title>
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

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            table-layout: fixed;
        }

        .report-table thead th {
            background: #0B1C80;
            color: #FFF;
            padding: 6px 5px;
            font-size: 10pt;
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

        .report-table th:nth-child(1),
        .report-table td:nth-child(1) {
            width: 12%;
        }

        .report-table th:nth-child(2),
        .report-table td:nth-child(2) {
            width: 28%;
        }

        .report-table th:nth-child(3),
        .report-table td:nth-child(3) {
            width: 40%;
        }

        .report-table th:nth-child(4),
        .report-table td:nth-child(4) {
            width: 20%;
        }
    </style>
</head>
<body>
    <div class="report-page">
        <div class="page-bg">
            <img src="file://{{ $verticalBgPath }}" alt="Background">
        </div>
        <div class="page-content">
            <div class="page-inner">
                <div class="first-page-header">
                    <div class="first-page-logo-row">
                        <div class="first-page-logo">
                            @if ($authUser->locality->hasMedia('localityGallery'))
                                <img src="{{ $authUser->locality->getFirstMediaUrl('localityGallery') }}"
                                     alt="Photo of {{ $authUser->locality->name }}">
                            @else
                                <img src="{{ public_path('img/localityDefault.png') }}" alt="Default Photo">
                            @endif
                        </div>
                    </div>
                    <div class="first-page-title-block">
                        <p class="first-page-title">
                            COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                        </p>
                        <p class="first-page-subtitle">LISTA DE CLIENTES</p>
                    </div>
                </div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOMBRE</th>
                            <th>DIRECCIÓN</th>
                            <th>NUM. DE TOMAS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($firstPageCustomers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>{{ $customer->name }} {{ $customer->last_name }}</td>
                                <td>
                                    {{ $customer->street }}
                                    No. Ext.{{ $customer->exterior_number ?? '' }}
                                    No. Int.{{ $customer->interior_number ?? '' }}
                                </td>
                                <td>{{ $customer->waterConnections->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="report-footer">
            <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <span class="text_infoE"> | </span>
            <span class="text_infoE page-number">Página 1 de {{ $totalPages }}</span>
            <span class="text_infoE"> | </span>
            <a class="text_infoE footer-branding" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
            <img src="{{ public_path('img/rootheim.png') }}" width="20" height="15" alt="Root Heim" class="footer-branding">
        </div>
    </div>

    @foreach ($otherPagesCustomers as $pageCustomers)
        <div class="report-page page-break">
            <div class="page-bg">
                <img src="file://{{ $verticalBgPath }}" alt="Background">
            </div>

            <div class="page-content">
                <div class="page-inner">
                    <div class="inner-page-header">
                        <p class="inner-page-committee">
                            COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                        </p>
                        <p class="inner-page-title">LISTA DE CLIENTES</p>
                    </div>

                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NOMBRE</th>
                                <th>DIRECCIÓN</th>
                                <th>NUM. DE TOMAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pageCustomers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->name }} {{ $customer->last_name }}</td>
                                    <td>
                                        {{ $customer->street }}
                                        No. Ext.{{ $customer->exterior_number ?? '' }}
                                        No. Int.{{ $customer->interior_number ?? '' }}
                                    </td>
                                    <td>{{ $customer->waterConnections->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="report-footer">
                <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
                <span class="text_infoE"> | </span>
                <span class="text_infoE page-number">Página {{ $loop->iteration + 1 }} de {{ $totalPages }}</span>
                <span class="text_infoE"> | </span>
                <a class="text_infoE footer-branding" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
                <img src="{{ public_path('img/rootheim.png') }}" width="20" height="15" alt="Root Heim" class="footer-branding">
            </div>
        </div>
    @endforeach
</body>
</html>
