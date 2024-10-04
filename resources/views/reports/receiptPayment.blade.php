<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Agua</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            background-image: url('img/receiptBackground.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
        }

        .receipt {
            margin: auto;
            padding: 20mm;
            position: relative;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .receipt-header {
            margin-top: 20px;
            text-align: left;
            margin-bottom: 20px;
        }

        .receipt-header img {
            max-width: 120px;
            max-height: 120px;
            border-radius: 50%;
        }

        .folio {
            color: black;
            position: absolute;
            top: 30mm;
            right: 20mm;
            font-weight: bold;
        }

        .folio p {
            font-size: 18px;
        }

        .title {
            text-transform: uppercase;
            text-align: center;
            color: #107cfc;
            font-size: 18px;
            margin: 10px 0;
        }

        .title p {
            font-size: 15px;
        }

        .date {
            text-align: center;
            margin-bottom: 20px;
        }

        .date p {
            font-size: 15px;
        }

        .info-container {
            margin-top: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .customer-info, .debt-info, .payment-info {
            padding-left: 150px;
            margin: 11px;
            text-align: left;
            width: fit-content;
            word-wrap: break-word;
        }

        .signature {
            text-align: center;
            font-size: 12px;
            page-break-before: avoid;
        }

        .company-info {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            margin-top: 180px;
            page-break-inside: avoid;
        }

        a.text_infoE, img.text_infoE {
            display: inline-block;
        }

        a {
            font-weight: normal;
            color: white;
            text-decoration: none;
        }

        h4 {
            margin-bottom: 5px;
            font-size: 16px;
            text-decoration: underline;
        }

        p {
            margin: 5px 0;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <header class="receipt-header">           
            @if ($payment->locality->hasMedia('localityGallery'))
                <img src="{{ $payment->locality->getFirstMediaUrl('localityGallery') }}" alt="Logo de {{ $payment->locality->name }}">
            @else
                <img src='img/localityDefault.png' alt="Logo de {{ $payment->locality->name }}">
            @endif
            <div class="folio">
                <p>FOLIO. {{ $payment->id }}</p>
            </div>
        </header>
        <div class="title">
            <h2>COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $payment->locality->name }} A.C.</h2>
            <p>{{ $payment->locality->municipality }}, {{ $payment->locality->state }}</p>
        </div>
        <div class="date">
            <p>{{ \Carbon\Carbon::parse($payment->created_at)->setTimezone('America/Mexico_City')->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }} a las {{ \Carbon\Carbon::parse($payment->created_at)->setTimezone('America/Mexico_City')->locale('es')->format('H:i') }}</p>
        </div>
        <div class="info-container">
            <div class="customer-info">
                <h4>Datos del cliente</h4>
                <p>{{ $payment->debt->customer->name }} {{ $payment->debt->customer->last_name }}</p>
                <p>{{ $payment->debt->customer->street }} #{{ $payment->debt->customer->interior_number }}, {{ $payment->debt->customer->block }}, {{$payment->locality->zip_code }}</p>
                <p>{{ $payment->locality->name }}, {{ $payment->locality->state }}</p>
            </div>
            <div class="debt-info">
                <h4>Datos de la deuda</h4>
                <p>FOLIO. {{ $payment->debt->id }}</p>
                <p>Fecha de la deuda: {{ \Carbon\Carbon::parse($payment->debt->start_date)->locale('es')->isoFormat('D [de ]MMMM [del] YYYY') }}</p>
                <p>Fecha de vencimiento: {{ \Carbon\Carbon::parse($payment->debt->end_date)->locale('es')->isoFormat('D [de ]MMMM [del] YYYY') }}</p>
            </div>
            <div class="payment-info">
                <br>
                <p><strong>Monto del pago: </strong>${{ $payment->amount }}</p>
                <p>
                    @switch($payment->method)
                        @case('cash')
                        <strong>Método de pago: </strong> Efectivo
                            @break
                        @case('card')
                        <strong>Método de pago: </strong>Tarjeta
                            @break
                        @case('transfer')
                        <strong>Método de pago: </strong>Transferencia
                            @break
                    @endswitch
                </p>
            </div>
        </div>
    </div>
    <div class="signature">
        _______________________________________________
        <p>{{ $payment->creator->name }} {{ $payment->creator->last_name }}</p>
    </div>
    <footer class="company-info">
        <a class="text_infoE" href="https://www.rootheim.com/">
            <strong>AquaControl</strong> powered by <strong>Root Heim Company</strong>
            <img src="img/rootheim.png" width="18px">
        </a>
    </footer>
</body>
</html>
