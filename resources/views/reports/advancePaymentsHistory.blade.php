@php
$locality = Auth::user()->locality ?? null;
$verticalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundVertical')
    ? $locality->getFirstMedia('pdfBackgroundVertical')->getPath()
    : public_path('img/backgroundReport.png');

$horizontalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundHorizontal')
    ? $locality->getFirstMedia('pdfBackgroundHorizontal')->getPath()
    : public_path('img/customersBackground.png');
@endphp
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Reporte de Pagos Anticipados</title>
        <style>
            html, body {
                margin: 0; padding: 0;
            }
            body {
                background-image: url('file://{{ $verticalBgPath }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                height: 100%;
                margin: 0;
                padding: 0;
            }
            #pagePdf {
                margin: 30px;
                font-family: 'Montserrat', sans-serif;
            }
            p, label, span, table, td, th {
                font-family: 'Montserrat', sans-serif;
                font-size: 12pt;
            }
            .h3 {
                font-weight: bold;
                font-size: 15pt;
                color: #0B1C80;
                margin-bottom: 5px;
            }
            .h4 {
                font-weight: bold;
                font-size: 25pt;
                color: #0B1C80;
                margin-bottom: 5px;
            }
            #reportCustomer, .informationReport {
                border: 1px solid #0B1C80;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                margin: 10px 0;
                padding: 15px;
            }
            .dataCustomer label {
                font-weight: bold;
                display: block;
            }
            .dataCustomer p {
                margin: 0;
            }
            #calendarTable {
                width: 50%;
                border-collapse: collapse;
                margin: 20px auto;
            }
            #calendarTable th {
                background: #0B1C80;
                color: #FFF;
                padding: 5px;
            }
            #calendarTable td {
                border: 1px solid #0B1C80;
                text-align: center;
                padding: 5px;
            }
            .paid {
                background-color: #16CB2B;
                color: #fff;
                font-weight: bold;
            }
            .notPaid {
                background-color: #E74C3C;
                color: #fff;
                font-weight: bold;
            }
            .logo img {
                width: 140px;
                height: 140px;
                border-radius: 50%;
            }
            .logo {
                margin-top: 70px;
                margin-left: 10%;
                margin-bottom: 30px;
            }
            .informationFooter {
                position: fixed;
                bottom: 30px;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 10pt;
                color: #ffffff;
            }
            .informationCompany {
                padding-top: 35px; 
                text-align: center;
                font-family: 'Montserrat', sans-serif;
            }
            .aquaTitle {
                font-size: 20pt;
                font-weight: bold;
                color: #0B1C80;
                text-transform: uppercase;
                margin-top: 5px;
                margin-bottom: 10px;
            }
            a.black-link {
                color: #000 !important;
                text-decoration: none;
            }
            .anticipationSection {
                text-align: center;
                margin-top: 10px;
                margin-bottom: 10px;
            }
            .anticipationSection .h3 {
                display: block;
                margin-bottom: 0px;
            }
            .monthsPaid {
                margin-top: 15px;
                text-align: center;
            }
            .badgePaid {
                display: inline-block;
                background-color: #2E86DE;
                color: #fff;
                padding: 8px 15px;
                margin: 5px;
                border-radius: 20px;
                font-weight: bold;
                font-size: 12pt;
            }
        </style>
    </head>
    <body>
    @php use Carbon\Carbon; @endphp
        <div id="pagePdf">
            <div class="logo">
                @if ($authUser->locality->hasMedia('localityGallery'))
                    <img src="{{ $authUser->locality->getFirstMediaPath('localityGallery') }}" alt="Foto de {{ $authUser->locality->name }}">
                @else
                    <img src="{{ public_path('img/localityDefault.png') }}" alt="Foto por defecto">
                @endif
            </div>
            <table id="reportHead" width="100%">
                <tr>
                    <td class="informationCompany" width="50%" style="text-align: center;">
                        <p class="aquaTitle">
                            COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                        </p>
                        <a href="https://wa.me/525619660990" class="black-link">WhatsApp: +52 56 1966 0990</a><br>
                        <a href="mailto:info@rootheim.com" class="black-link">Email: info@rootheim.com</a>
                    </td>
                    <td class="informationReport" width="50%" style="text-align: center;">
                        <span class="h4">Reporte de Pagos Anticipados</span>
                    </td>
                </tr>
            </table>
            <div id="reportCustomer">
                <span class="h3">Datos del Cliente y Toma de Agua</span>
                <table class="dataCustomer" width="100%">
                    <tr>
                        <td><label>Cliente:</label> <p>{{ $customer->name }} {{ $customer->last_name }}</p></td>
                        <td><label>Toma de Agua:</label> <p>{{ $connection->name }}</p></td>
                    </tr>
                    <tr>
                        <td><label>Fecha de Inicio:</label> 
                            <p>{{ isset($debt->startDate) ? Carbon::parse($debt->startDate)->format('d/m/Y') : 'N/A' }}</p>
                        </td>
                        <td><label>Fecha de Fin:</label> 
                            <p>{{ isset($debt->endDate) ? Carbon::parse($debt->endDate)->format('d/m/Y') : 'N/A' }}</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="anticipationSection">
                <span class="h3">Periodo de Anticipación</span>
                <div class="monthsPaid">
                    @foreach($months as $month)
                        <span class="badgePaid">{{ $month['label'] }}</span>
                    @endforeach
                </div>
            </div>
            <div class="informationFooter">
                <a href="https://aquacontrol.rootheim.com/" style="color:#fff;"><strong>AquaControl</strong></a>
                &nbsp;|&nbsp;
                <a href="https://rootheim.com/" style="color:#fff;">powered by <strong>Root Heim Company</strong></a>
            </div>
        </div>
    </body>
</html>
