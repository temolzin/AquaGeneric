<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pagos Adelantados</title>
    <style>
        html {
            margin: 0;
            padding: 0;
        }

        body {
            background-image: url('img/backgroundReport.png');
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

        p,
        label,
        span,
        table {
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
            font-size: 13pt;
            display: block;
            color: #0B1C80;
            text-align: left;
            margin-bottom: 5px;
        }

        #reportClient {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #0B1C80;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .companyInfo {
            width: 50%;
            text-align: center;
            align-content: stretch;
            font-family: 'Montserrat', sans-serif;
        }

        .dateNow {
            padding: 15px;
            border: 1px solid #0B1C80;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-family: 'Montserrat', sans-serif;
        }

        .infoClient {
            width: 100%;
            height: auto;
            padding-left: 15px;
        }

        .dataClient {
            padding: 0;
            width: 100%;
        }

        .dataClient label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .dataClient p {
            margin: 0;
            font-weight: normal;
        }

        .textable {
            text-align: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 12pt;
            color: #FFF;
        }

        .totalPayment {
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

        #detailReport {
            border-collapse: collapse;
            width: 100%;
            margin: 0;
        }

        #detailReport thead th {
            background: #0B1C80;
            color: #FFF;
            padding: 5px;
        }

        #detailAdvancedPayment tr {
            border-top: 1px solid #bfc9ff;
        }

        .infoEabajo {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            position: absolute;
            bottom: 5px;
            left: 20px;
            right: 20px;
        }

        .textInfoE {
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
            @if (auth()->user()->locality->hasMedia('localityGallery'))
                <img src="{{ $authUser->locality->getFirstMediaUrl('localityGallery') }}"
                    alt="Photo of {{ $authUser->locality->name }}">
            @else
                <img src="img/localityDefault.png" alt="Default Photo">
            @endif
        </div>

        <table id="reportHead">
            <tr>
                <td class="companyInfo">
                    <div>
                        <p class="aquaTitle">
                            COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }},
                            {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                        </p>
                        <br>
                        <a class="linkWhats" href="https://wa.me/525623640302">
                            WhatsApp: +52 56 1966 0990

                        </a>
                        <br>
                        <a class="linkEmail" href="mailto:info@rootheim.com">
                            Email: info@rootheim.com
                        </a>
                    </div>
                </td>
                <td class="dateNow">
                    <div class="round">
                        <span class="h3">Historial de Pagos Adelantados</span>
                        <p>
                            <strong>Fecha: </strong>
                            {{ \Carbon\Carbon::now()->translatedFormat('j \d\e F \d\e Y') }}
                        </p>
                    </div>
                </td>
            </tr>
        </table>

        <table id="reportClient">
            <tr>
                <td class="infoClient">
                    <div class="round">
                        <span class="h3">Cliente</span>
                        <table class="dataClient">
                            <tr>
                                <td>
                                    <label>Nombre:</label>
                                    <p>{{ $customer->name }} {{ $customer->last_name }}</p>
                                </td>
                                <td>
                                    <label>Dirección:</label>
                                    <p>{{ $customer->street }}, #{{ $customer->interior_number }}</p>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <span class="h3">Toma de Agua</span>
                        <table class="dataClient">
                            <tr>
                                <td>
                                    <label>Nombre:</label>
                                    <p>{{ $waterConnection->name }}</p>
                                </td>
                                @php
                                    $types = [
                                        'commercial' => 'Comercial',
                                        'residencial' => 'Residencial',
                                    ];
                                @endphp
                                <td style="width: 57%;">
                                    <label>Tipo:</label>
                                    <p>{{ $types[$waterConnection->type] ?? ucfirst($waterConnection->type) }}</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <table id="detailReport">
            <thead>
                <tr>
                    <th class="textable">Folio Pago</th>
                    <th class="textable">Fecha del Pago</th>
                    <th class="textable">Período Adelantado</th>
                    <th class="textable">Monto</th>
                </tr>
            </thead>
            <tbody id="detailAdvancedPayment">
                @foreach ($payments as $debtId => $debtPayments)
                    @foreach ($debtPayments as $payment)
                        <tr class="{{ $payment->debt->end_date > now() ? 'highlight' : '' }}">
                            <td class="textCenter">{{ $payment->id }}</td>
                            <td class="textCenter">
                                {{ \Carbon\Carbon::parse($payment->created_at)->translatedFormat('j \d\e F \d\e Y') }}
                            </td>
                            <td class="textCenter">
                                {{ \Carbon\Carbon::parse($payment->debt->start_date)->translatedFormat('F Y') }} -
                                {{ \Carbon\Carbon::parse($payment->debt->end_date)->translatedFormat('F Y') }}
                            </td>
                            <td class="textCenter">$ {{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td colspan="3" class="totalPayment">
                        <strong>Total Pagos Adelantados:</strong>
                    </td>
                    <td class="textCenter">
                        <strong>$ {{ number_format($totalPayments, 2) }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="infoEabajo">
        <a class="textInfoE" href="https://aquacontrol.rootheim.com/">
            <strong>AquaControl</strong>
        </a>
        <a class="textInfoE" href="https://rootheim.com/">
            powered by<strong> Root Heim Company </strong>
        </a>
        <img src="img/rootheim.png" width="15px" height="15px">
    </div>
</body>

</html>
