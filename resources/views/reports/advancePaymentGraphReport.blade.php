@php
$locality = Auth::user()->locality ?? null;
$verticalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundVertical')
    ? $locality->getFirstMedia('pdfBackgroundVertical')->getPath()
    : public_path('img/backgroundReport.png');

$horizontalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundHorizontal')
    ? $locality->getFirstMedia('pdfBackgroundHorizontal')->getPath()
    : public_path('img/customersBackgroundHorizontal.png');
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Gráfica de pagos</title>
        <style>
            html{
                margin: 0;
                padding: 0;
            }

            body{
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
            }

            #reportHead {
                margin: 0;
                padding-bottom: 10px;
            }

            .aquaTitle {
                font-family: 'Montserrat', sans-serif;
                font-size: 20pt;
                font-weight: bold;
                margin-bottom: 5px;
                margin-right: 5px;
                display: inline-block;
                text-decoration: none;
                color: #0B1C80;
                text-transform: uppercase;
            }

            p, label, span, table {
                font-family: 'Montserrat', sans-serif;
                font-size: 12pt;
            }

            .h2 {
                font-family: 'Montserrat', sans-serif;
                font-size: 17pt;
            }

            .h3 {
                font-weight: bold;
                font-family: 'Montserrat', sans-serif;
                font-size: 15pt;
                display: block;
                color: #0B1C80;
                text-align: left;
                margin-bottom: 5px;
            }
            .h4 {
                font-weight: bold;
                font-family: 'Montserrat', sans-serif;
                font-size: 25pt;
                display: block;
                color: #0B1C80;
                text-align: left;
                margin-bottom: 5px;
            }

            #reportCustomer {
                width: 100%;
                padding: 15px;
                margin: 10px 0;
                border: 1px solid #0B1C80;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .informationCompany {
                width: 50%;
                text-align: center;
                align-content: stretch;
                font-family: 'Montserrat', sans-serif;
            }

            .informationReport {
                padding: 15px;
                border: 1px solid #0B1C80;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                font-family: 'Montserrat', sans-serif;
            }

            .informationCustomer {
                width: 100%;
                height: auto;
                padding-left: 15px;
            }

            .dataCustomer {
                padding: 0;
                width: 100%;
            }

            .dataCustomer label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .dataCustomer p {
                margin: 0;
                font-weight: normal;
            }

            .texTable {
                text-align: center;
                font-family: 'Montserrat', sans-serif;
                font-size: 12pt;
                color: #FFF;
            }

            .totalPayment{
                padding: 20px;
                font-size: 15pt;
                text-align: right;
                font-family: 'Montserrat', sans-serif;
                font-weight: bold;
            }

            .textCenter {
                text-align: center;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
            }

            .textRight {
                text-align: right;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
            }

            .textLeft {
                text-align: left;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
            }

            #reportDetail {
                border-collapse: collapse;
                width: 100%;
                margin: 0;
            }

            #reportDetail thead th {
                background: #0B1C80;
                color: #FFF;
                padding: 5px;
            }

            .informationFooter {
                text-align: center;
                margin-top: 20px;
                padding: 10px;
                position: absolute;
                bottom: 5px;
                left: 20px; 
                right: 20px; 
            }

            .textInformationFooter {
                text-align: center;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
                color: white;
                text-decoration: none;
                display: inline-block;
            }

            .logo {
                width: 100px;
                height: auto;
                margin-bottom: 30px;
                margin-top: 70px;
                margin-left: 10%;
            }

            .logo img {
                border-radius: 50%;
                width: 140px;
                height: 140px;
            }

            .linkWhats,
            .linkEmail {
                display: inline-block;
                text-decoration: none;
                border-radius: 5px;
                font-family: 'Montserrat', sans-serif;
                color: black;
            }
        </style>
    </head>
    <body>
        <div id="pagePdf">
           <div class="logo">
            @if ($authUser->locality->hasMedia('localityGallery'))
                <img src="{{ $authUser->locality->getFirstMediaUrl('localityGallery') }}" alt="Photo of {{ $authUser->locality->name }}">
            @else
                <img src='img/localityDefault.png' alt="Default Photo">
            @endif
            </div>
            <table id="reportHead">
                <tr>
                    <td class="informationCompany">
                        <div>
                            <p class="aquaTitle"> COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                            </p><br>
                            <a class="linkWhats" href="https://wa.me/525619660990">WhatsApp: +52 56 1966 0990</a><br>
                            <a class="linkEmail" href="mailto:info@rootheim.com">Email: info@rootheim.com</a>
                        </div>
                    </td>
                    <td class="informationReport">
                        <div class="round">
                            <span class="h4">Gráfica de pagos por Adelantados</span>
                        </div>
                    </td>
                </tr>
            </table>
            <table id="reportCustomer">
                <tr>
                    <td class="informationCustomer">
                        <div class="round">
                            <span class="h3">Análisis de Pagos Adelantados</span>
                            <table class="dataCustomer">
                                <tr>
                                    <td><label>Mes de Inicio:</label> <p>{{ $debt->startMonthName }}</p></td>
                                    <td><label>Mes de Término:</label> <p>{{ $debt->endMonthName }}</p></td>
                                    <td><label>Año:</label> <p>{{ $debt->end_year }}</p></td>
                                    <td><label>Total Adelantado:</label> <p>${{ number_format($debt->amount, 2) }}</p></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>  
            <table style="height:30% ; width: 100%;">
                <tbody>
                    <tr>
                        <td style="padding: 8px; text-align: center; width: 50%;">
                            <p style="font-weight: bold; color: #000000ff; margin: 5px 0;">Gráfica de Barras</p>
                            <img src="{{ $chartImages[0]}}" style="max-width: 800px; height: 170px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; text-align: center; width: 50%;">
                            <p style="font-weight: bold; color: #000000ff; margin: 10px 0; margin-top:10px;">Gráfica de Líneas</p>
                            <img src="{{ $chartImages[1]}}" style="max-width: 800px; height: 170px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; text-align: center; width: 50%;">
                            <p style="font-weight: bold; color: #000000ff; margin: 5px 0; margin-top: 240px;">Gráfica de Pastel</p>
                            <img src="{{ $chartImages[2] }}" style="max-width: 800px; height: 170px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; text-align: center; width: 50%;">
                            <p style="font-weight: bold; color: #000000ff; margin: 10px 0; margin-top: 50px;">Gráfica de Dona</p>
                            <img src="{{ $chartImages[3] }}" style="max-width: 800px; height: 170px;">
                       </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <center>
            <div class="informationFooter">
                <a class="textInformationFooter" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
                <a class="textInformationFooter" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a><img src="img/rootheim.png" width="15px" height="15px">
            </div>
        </center>
    </body>
</html>
