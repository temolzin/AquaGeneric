<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Agua</title>
    <style>
        @page {
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
            max-width: 300px; /* Mantiene el ancho del recibo */
            height: auto; /* Permite que la altura sea automática */
            margin: auto;
            padding: 10px;
        }

        .receipt-header {
            margin-top: 20px;
            text-align: left;
            margin-bottom: 10px; 
        }

        .receipt-header img {
            max-width: 50px;
        }

        .folio {
            color: black;
            position: absolute;
            top: 0;
            right: 0;
            font-size: 10px;
            font-weight: lighter;
            padding: 30px;
        }

        .title {
            text-transform: uppercase;
            text-align: center;
            color: #107cfc;
            font-size: 12px;
            margin: 10px 0;
        }

        .date {
            text-align: center; 
            font-size: 10px;
            margin-bottom: 10px;
        }
        
        .info-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .customer-info, .debt-info, .payment-info {
            padding-left: 55px;
            margin: 11px auto;
            display: inline-block;
            text-align: left;
            font-size: 10px;
            width: fit-content;
            word-wrap: break-word;
        }

        .signature {
            text-align: center;
            font-size: 8px;
            margin-top: 20px; /* Aumentar espacio arriba */
        }

        h4 {
            margin-bottom: 5px;
            font-size: 10px;
            text-decoration: underline; /* Añadir subrayado a los títulos */
        }

        p {
            margin: 5px 0;
            font-size: 10px;
        }

        .info_Eabajo {
            text-align: center;
            margin-top: 90px;
            font-weight: bold;
            font-size: 8px;
        }

        a {
            font-weight: normal;
            color: white; /* Color del texto */
            text-decoration: none; /* Sin subrayado */
        }
    </style>
</head>
<body>
    <div class="receipt">
        <header class="receipt-header">           
            @if ($payment->locality->hasMedia('localityGallery'))
                <img src="{{ $payment->locality->getFirstMediaUrl('localityGallery') }}" alt="Logo de {{ $payment->locality->locality_name }}">
            @else
                <img src='img/localityDefault.png' alt="Logo por defecto">
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
        <br><br><br>
        _______________________________________________
        <p>{{ $payment->creator->name }} {{ $payment->creator->last_name }}</p>
    </div>
    <footer class="info_Eabajo">
        <a class="text_infoE" href="https://www.rootheim.com/"><strong>AquaControl</strong> powered by <strong>Root Heim Company</strong></a>
        <img src="img/rootheim.png" width="12px">
    </footer>
</body>
</html>
