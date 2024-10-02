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
        }

        .receipt-header {
            margin-top: 20px;
            text-align: left;
            margin-bottom: 20px;
        }

        .receipt-header img {
            max-width: 115px;
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
            margin-top: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .customer-info, .debt-info, .payment-info {
            padding-left: 150px;
            margin: 11px auto;
            text-align: left;
            width: fit-content;
            word-wrap: break-word;
        }

        .signature {
            text-align: center;
            font-size: 12px;
            margin-top: 10px;
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

        .company-info {
            text-align: center;
            margin-top: 215px;
            font-weight: bold;
            font-size: 15px;
        }

        a {
            font-weight: normal;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <header class="receipt-header">           
            @if ($payment->locality->hasMedia('localityGallery'))
                <img src="{{ $payment->locality->getFirstMediaUrl('localityGallery') }}" alt="Logo de {{ $payment->locality->locality_name }}">
            @else
                <img src='img/userDefault.png' alt="Logo por defecto">
            @endif
            <div class="folio">
                <p>FOLIO. {{ $payment->id }}</p>
            </div>
        </header>
        <div class="title">
            <h2>COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $payment->locality->locality_name }} A.C.</h2>
            <p>{{ $payment->locality->municipality }}, {{ $payment->locality->state }}</p>
        </div>
        <div class="date">
            <p>{{ \Carbon\Carbon::parse($payment->created_at)->locale('es')->isoFormat('D [de ]MMMM [del] YYYY') }} a las {{ \Carbon\Carbon::parse($payment->created_at)->locale('es')->format('H:i') }}</p>
        </div>
        <div class="info-container">
            <div class="customer-info">
                <h4>Datos del cliente</h4>
                <p>{{ $payment->debt->customer->name }} {{ $payment->debt->customer->last_name }}</p>
                <p>{{ $payment->debt->customer->street }} #{{ $payment->debt->customer->interior_number }}, {{ $payment->debt->customer->block }}, {{$payment->locality->zip_code }}</p>
                <p>{{ $payment->locality->locality_name }}, {{ $payment->locality->state }}</p>
            </div>
            <div class="debt-info">
                <h4>Datos de la deuda</h4>
                <p>FOLIO. {{ $payment->debt->id }}</p>
                <p>Fecha de la deuda: {{ \Carbon\Carbon::parse($payment->debt->start_date)->locale('es')->isoFormat('D [de ]MMMM [del] YYYY') }}</p>
                <p>Fecha de vencimiento: {{ \Carbon\Carbon::parse($payment->debt->end_date)->locale('es')->isoFormat('D [de ]MMMM [del] YYYY') }}</p>
            </div>
            <div class="payment-info">
                <p><strong>Monto total del pago: </strong>${{ $payment->amount }}</p>
            </div>
        </div>
    </div>
    <div class="signature">
        _______________________________________________
        <p>{{ $payment->creator->name }} {{ $payment->creator->last_name }}</p>
    </div>
    <footer class="company-info">
        <a class="text_infoE" href="https://www.rootheim.com/"><strong>AquaControl</strong> powered by <strong>Root Heim Company</strong></a>
        <img src="img/rootheim.png" width="18px">
    </footer>
</body>
</html>
