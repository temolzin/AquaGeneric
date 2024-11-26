<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reporte Semanal de Ingresos</title>
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
                page-break-inside: auto;
                margin-bottom: 10px;
            }

            #reporte_detalle thead th {
                background: #0B1C80;
                color: #FFF;
                padding: 5px;
                page-break-inside: avoid;
                page-break-after: auto;
            }

            #detalle_ingresos tr {
                border-top: 1px solid #bfc9ff;
                page-break-inside: avoid;
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

            .total_payment{
                    padding: 15px;
                    font-size: 13pt;
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

            .week_section {
                margin-top: 0px; 
                font-family: 'Montserrat', sans-serif;
                page-break-inside: avoid;
            }

            .total_earnings {
                position: absolute;
                font-family: 'Montserrat', sans-serif;
                font-size: 16pt;
                color: #0B1C80;
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
                <h3>EGRESOS SEMANALES<h3>
            </div>
            @php
                $daysInSpanish = [
                    'Monday' => 'Lunes',
                    'Tuesday' => 'Martes',
                    'Wednesday' => 'Miércoles',
                    'Thursday' => 'Jueves',
                    'Friday' => 'Viernes',
                    'Saturday' => 'Sábado',
                    'Sunday' => 'Domingo',
                ];
            @endphp
            @foreach ($weeks as $week)
                <div class="week_section">
                    <p><strong>Semana del {{ \Carbon\Carbon::parse($week['start'])->translatedFormat('j \\d\\e F') }} al {{ \Carbon\Carbon::parse($week['end'])->translatedFormat('j \\d\\e F') }}</strong></p>
                    <table id="reporte_detalle">
                        <thead>
                            <tr>
                                @foreach ($daysInSpanish as $dayName)
                                    <th class="textcenter">{{ $dayName }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="detalle_ingresos">
                            <tr>
                                @foreach ($daysInSpanish as $dayEnglish => $dayName)
                                    @php
                                        $dayExpenses = $week['dailyExpenses'][$dayEnglish] ?? 'N/A';
                                    @endphp
                                    <td class="textcenter">
                                        @if ($dayExpenses === 'N/A')
                                            {{ $dayExpenses }}
                                        @else
                                            ${{ number_format($dayExpenses, 2) }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td colspan="7" class="total_payment"><strong>Total de la semana:</strong> ${{ number_format(array_sum(array_filter($week['dailyExpenses'], fn($value) => $value !== 'N/A')), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
            <div class="total_earnings">
                <strong>Total del Periodo: ${{ number_format($totalPeriodExpenses, 2) }}</strong>
            </div>
        </div>
        <div class="info_Eabajo">
            <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <a class="text_infoE" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
            <img src="img/rootheim.png" width="15px" height="15px">
        </div>
    </body>
</html>
