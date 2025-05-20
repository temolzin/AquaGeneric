<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reporte Anual de Gastos {{ $year }}</title>
        <style>
            html{
                margin: 0;
                padding: 15px;
            }

            body{
                height: 100%;
                margin: 0;
                padding: 0;
                background-image: url('img/backgroundReport.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }

            #page_pdf {
                margin-top: 10%;
                margin: 40px;
            }

            .info_empresa {
                width: 100%;
                margin-top: 60px;
                text-align: center;
                align-content: stretch;
                font-family: 'Montserrat', sans-serif;
            }

            .aqua_titulo {
                font-family: 'Montserrat', sans-serif;
                font-size: 20pt;
                font-weight: bold;
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

            .textable {
                text-align: center;
                font-family: 'Montserrat', sans-serif;
                font-size: 12pt;
                color: #FFF;
            }

            .textcenter {
                padding: 5px;
                background-color: #FFF;
                text-align: center;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
            }

            #reporte_detalle {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 150px;
                page-break-inside: auto;
            }

            #reporte_detalle thead th {
                background: #0B1C80;
                color: #FFF;
                padding: 5px;
            }

            #detalle_gastos tr {
                border-top: 1px solid #bfc9ff;
            }

            .info_Eabajo {
                text-align: center;
                margin-top: 20px;
                padding: 10px;
                position: absolute;
                bottom: 5px;
                left: 20px;
                right: 20px;
            }

            .text_infoE {
                text-align: center;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
                color: white;
                text-decoration: none;
                display: inline-block;
            }

            #reporte_head {
                justify-content: center;
            }

            #reporte_head .logo {
                height: auto;
                margin-left: 60px;
            }

            #reporte_head .logo img {
                border-radius: 50%;
                width: 120px;
                height: 120px;
            }

            .total_expense{
                    padding: 20px;
                    font-size: 15pt;
                    text-align: right;
                    font-family: 'Montserrat', sans-serif;
                    font-weight: bold;
                }

            .title {
                color: #0B1C80;
                font-family: 'Montserrat', sans-serif;
                font-size: 14pt;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div id="page_pdf">
            <table id="reporte_head">
                <tr>
                    <td>
                        <div class="logo">
                            @if ($authUser->locality->hasMedia('localityGallery'))
                                <img src="{{ $authUser->locality->getFirstMediaUrl('localityGallery') }}" alt="Photo of {{ $authUser->locality->name }}">
                            @else
                                <img src='img/localityDefault.png' alt="Default Photo">
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
            <div class="info_empresa">
                <p class="aqua_titulo">
                    COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                </p>
            </div>
            <div class="title">
                <h3>GASTOS DEL {{ $year }}</h3>
            </div>
            <table id="reporte_detalle">
                <thead>
                    <tr>
                        <th class="textable">MES</th>
                        <th class="textable">GASTOS</th>
                    </tr>
                </thead>
                <tbody id="detalle_gastos">'
                    @php
                        $months = [
                            1 => 'Enero',
                            2 => 'Febrero',
                            3 => 'Marzo',
                            4 => 'Abril',
                            5 => 'Mayo',
                            6 => 'Junio',
                            7 => 'Julio',
                            8 => 'Agosto',
                            9 => 'Septiembre',
                            10 => 'Octubre',
                            11 => 'Noviembre',
                            12 => 'Diciembre'
                        ];
                    @endphp
                    @foreach ($months as $monthNumber => $monthName)
                        <tr>
                            <td class="textcenter">{{ $monthName }}</td>
                            <td class="textcenter">${{ number_format($monthlyExpenses[$monthNumber] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="1" class="total_expense"><strong>Total:</strong></td>
                        <td class="textcenter"><strong>${{ number_format($totalExpenses, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="info_Eabajo">
            <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <a class="text_infoE" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a><img src="img/rootheim.png" width="15px" height="15px">
        </div>
    </body>
</html>
