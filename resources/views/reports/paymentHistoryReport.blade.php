@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <style>
        html{
                margin: 0;
                padding: 0;
            }

            body{
                background-image: url('img/backgroundReport.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                height: 100%;
                margin: 0;
                padding: 0;
            }

            #page_pdf {
                margin: 30px;
            }

            #reporte_head {
                margin: 0;
                padding-bottom: 10px;
            }

            .aqua_titulo {
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

            #customer_report {
                width: 100%;
                padding: 15px;
                margin: 10px 0;
                border: 1px solid #0B1C80;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .enterprise_info {
                width: 50%;
                text-align: center;
                align-content: stretch;
                font-family: 'Montserrat', sans-serif;
            }

            .report_info {
                padding: 15px;
                border: 1px solid #0B1C80;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                font-family: 'Montserrat', sans-serif;
            }

            .info_cliente {
                width: 100%;
                height: auto;
                padding-left: 15px;
            }

            .datos_cliente {
                padding: 0;
                width: 100%;
            }

            .datos_cliente label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .datos_cliente p {
                margin: 0;
                font-weight: normal;
            }

            .textable {
                text-align: center;
                font-family: 'Montserrat', sans-serif;
                font-size: 12pt;
                color: #FFF;
            }

            .total_payment{
                padding: 20px;
                font-size: 15pt;
                text-align: right;
                font-family: 'Montserrat', sans-serif;
                font-weight: bold;
            }

            .textcenter {
                text-align: center;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
            }

            .textright {
                text-align: right;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
            }

            .textleft {
                text-align: left;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
            }

            #report_detail {
                border-collapse: collapse;
                width: 100%;
                margin: 0;
            }

            #report_detail thead th {
                background: #0B1C80;
                color: #FFF;
                padding: 5px;
            }

            #payments_details tr {
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

            .link_Whats,
            .link_Email {
                display: inline-block;
                text-decoration: none;
                border-radius: 5px;
                font-family: 'Montserrat', sans-serif;
                color: black;
            }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Historial de Pagos</title>
</head>
<body>
    <div id="page_pdf">
        <div class="logo">
            @if ($authUser->locality->hasMedia('localityGallery'))
                <img src="{{ $authUser->locality->getFirstMediaUrl('localityGallery') }}" alt="Photo of {{ $authUser->locality->name }}">
            @else
                <img src='img/localityDefault.png' alt="Default Photo">
            @endif
        </div>
        <table id="reporte_head">
            <tr>
                <td class="enterprise_info">
                    <div>
                        <p class="aqua_titulo"> COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                        </p><br>
                        <a class="link_Whats" href="https://wa.me/525623640302">WhatsApp: +52 56 2364 0302</a><br>
                        <a class="link_Email" href="mailto:info@rootheim.com">Email: info@rootheim.com</a>
                    </div>
                </td>
                <td class="report_info">
                    <div class="round">
                        <span class="h3">Historial de Pagos</span>
                        @if ($customer->responsible_name)
                            <p><strong>Cliente:</strong> {{ $customer->responsible_name }}</p>
                        @else
                            <p><strong>Cliente:</strong> {{ $customer->name }} {{ $customer->last_name }}</p> 
                        @endif
                        <p><strong>Dirección:</strong> {{ $customer->street }}, #{{ $customer->exterior_number }}, {{ $customer->interior_number }}, {{ $customer->block }}, {{$customer->zip_code }}, {{ $customer->locality }}, {{ $customer->state }}</p>
                    </div>
                </td>
            </tr>
        </table>
        <table id="customer_report">
            <tr>
                <td class="info_cliente">
                    <div class="round">
                        <span class="h3">Toma</span>
                        <table class="datos_cliente">
                            <tr>
                                <td><label>Nombre:</label> <p>{{ $debt->waterConnection->name }}</p></td>
                                <td><label>Dirección:</label> <p>{{ $debt->waterConnection->street }}, #{{ $debt->waterConnection->exterior_number }}, {{ $debt->waterConnection->interior_number }}, {{ $debt->waterConnection->block }}</p></td>
                                <td><label>Deuda:</label> <p>${{ number_format($totalDebt, 2) }}</p></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        <table id="report_detail">
            <thead>
                <tr>
                    <th class="textable">Fecha</th>
                    <th class="textable">Monto</th>
                </tr>
            </thead>
            <tbody id="payments_details">
                @if ($payments->isEmpty())
                    <tr>
                        <td class="textcenter
                        " colspan="2">Aún no hay pagos registrados para esta deuda</td>
                    </tr>
                @endif
                @foreach ($payments as $payment)
                    <tr>
                        <td class="textcenter">{{ \Carbon\Carbon::parse($payment->created_at)->translatedFormat('j \d\e F \d\e Y') }}</td>
                        <td class="textcenter">${{ $payment->amount }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="1" class="total_payment"><strong>Total:</strong></td>
                    <td class="textcenter"><strong>${{ number_format($totalPayments, 2) }} </strong></td>
                </tr>
                <tr>
                    <td colspan="1" class="total_payment"><strong>Pendiente:</strong></td>
                    <td class="textcenter"><strong>${{ number_format($pendingBalance, 2) }} </strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="info_Eabajo">
        <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
        <a class="text_infoE" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a><img src="img/rootheim.png" width="20px" height="15px">
    </div>
</body>
</html>
