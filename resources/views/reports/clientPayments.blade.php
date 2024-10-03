<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

            #reporte_cliente {
                width: 100%;
                padding: 15px;
                margin: 10px 0;
                border: 1px solid #0B1C80;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .info_empresa {
                width: 50%;
                text-align: center;
                align-content: stretch;
                font-family: 'Montserrat', sans-serif;
            }

            .info_reporte {
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

            #reporte_detalle {
                border-collapse: collapse;
                width: 100%;
                margin: 0;
            }

            #reporte_detalle thead th {
                background: #0B1C80;
                color: #FFF;
                padding: 5px;
            }

            #detalle_productos tr {
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
    </head>
    <body>
        <div id="page_pdf">
            <div class="logo">
                @if ($authUser->locality->hasMedia('localityGallery'))
                    <img src="{{ $authUser->locality->getFirstMediaUrl('localityGallery') }}" alt="Photo of {{ $authUser->locality->locality_name }}">
                @else
                    <img src='img/localityDefault.png' alt="Default Photo">
                @endif
            </div>
            <table id="reporte_head">
                <tr>
                    <td class="info_empresa">
                        <div>
                            <p class="aqua_titulo"> COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->locality_name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                            </p><br>
                            <a class="link_Whats" href="https://wa.me/525623640302">WhatsApp: +52 56 2364 0302</a><br>
                            <a class="link_Email" href="mailto:info@rootheim.com">Email: info@rootheim.com</a>
                        </div>
                    </td>
                    <td class="info_reporte">
                        <div class="round">
                            <span class="h3">Reporte de Pagos</span>
                            <p><strong>Fecha: </strong>{{ \Carbon\Carbon::now()->translatedFormat('j \d\e F \d\e Y') }}</p> 
                            <p><strong>Del</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('j \d\e F \d\e Y') }} <strong>Al</strong> {{ \Carbon\Carbon::parse($endDate)->translatedFormat('j \d\e F \d\e Y') }}</p> 
                        </div>
                    </td>
                </tr>
            </table>
            <table id="reporte_cliente">
                <tr>
                    <td class="info_cliente">
                        <div class="round">
                            <span class="h3">Cliente</span>
                            <table class="datos_cliente">
                                <tr>
                                    <td><label>Nombre:</label> <p>{{ $customer->name}} {{$customer->last_name}}</p></td>
                                    <td><label>Dirección:</label> <p>{{ $customer->street }}, #{{ $customer->interior_number }} </p></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
            <table id="reporte_detalle">
                <thead>
                    <tr>
                        <th class="textable">Folio</th>
                        <th class="textable">Fecha del pago</th>
                        <th class="textable">Cantidad</th>
                    </tr>
                </thead>
                <tbody id="detalle_productos">'
                    @foreach ($payments as $payment)
                        <tr>
                            <td class="textcenter">{{ $payment->id }}</td>
                            <td class="textcenter">{{ \Carbon\Carbon::parse($payment->created_at)->translatedFormat('j \d\e F \d\e Y') }}</td>
                            <td class="textcenter">$ {{ $payment->amount }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" class="total_payment"><strong>Total:</strong></td>
                        <td class="textcenter"><strong>$ {{ number_format($totalPayments, 2) }}</strong></td>
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
