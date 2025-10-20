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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Clientes con Deudas</title>
        <style>
            html{
                margin: 0;
                padding: 15px;
            }

            body{
                height: 100%;
                margin: 0;
                padding: 0;
                background-image: url('file://{{ $verticalBgPath }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }

            #page_pdf {
                margin-top: 10%;
                margin: 40px;
            }

            .info_empresa {
                width: 75%;
                text-align: center;
                align-content: stretch;
                font-family: 'Montserrat', sans-serif;
            }

            .aqua_titulo {
                padding-top: 25px;
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

            #detalle_clientes tr {
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
                    <td class="info_empresa">
                        <div>
                            <p class="aqua_titulo">
                                COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="title">
                <h3>CLIENTES CON DEUDAS</h3>
            </div>
            <table id="reporte_detalle">
                <thead>
                    <tr>
                        <th class="textable">ID</th>
                        <th class="textable">CLIENTE</th>
                        <th class="textable">TOTAL DE LA DEUDA</th>
                    </tr>
                </thead>
                <tbody id="detalle_clientes">
                    @foreach ($customers as $customer)
                        <tr>
                            <td class="textcenter">{{ $customer->id }}</td>
                            <td class="textcenter">{{ $customer->name }} {{ $customer->last_name }}</td>
                            <td class="textcenter">
                                @php
                                    $unpaidDebts = $customer->waterConnections->flatMap(function ($waterConnection) {
                                        return $waterConnection->debts->where('status', '!=', 'paid');
                                    });
                                    $totalDebt = $unpaidDebts->sum('amount');
                                    $totalPaid = $unpaidDebts->sum('debt_current');
                                    $pendingBalance = $totalDebt - $totalPaid;
                                @endphp
                                ${{ number_format($pendingBalance, 2, '.', ',') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="info_Eabajo">
            <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <a class="text_infoE" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a><img src="img/rootheim.png" width="20px" height="15px">
        </div>
    </body>
</html>
