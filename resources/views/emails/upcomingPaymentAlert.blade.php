<!DOCTYPE html>
<html>
<head>
    <title>Recordatorio de Pago</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid #0B1C80;
        }

        .title {
            color: #0B1C80;
            font-size: 20pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .content {
            padding: 30px;
            font-size: 12pt;
        }

        .paymentDetails {
            width: 100%;
            margin: 25px 0;
            border-collapse: collapse;
        }

        .paymentDetails td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .paymentDetails td:first-child {
            font-weight: bold;
            color: #0B1C80;
            width: 40%;
        }

        .urgent {
            color: #d9534f;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            padding: 15px;
            background-color: #fff9f9;
            border: 1px solid #d9534f;
            border-radius: 5px;
        }

        .signature {
            margin-top: 40px;
            text-align: right;
            font-style: italic;
        }

        .footer {
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 12pt;
            background-color: #0B1C80;
        }

        a {
            color: #cce5ff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .footer img {
            vertical-align: middle;
            margin-left: 5px;
        }

        strong {
            color: #0B1C80;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                <tr>
                    <td style="padding-right:20px;">
                        <img src="{{ $logoCid }}" alt="Logo AquaControl" height="50" style="width:auto; display:block;">
                    </td>
                    <td style="color:#0B1C80; font-size:24px; font-weight:bold; font-family: Montserrat, sans-serif;">
                        AquaControl
                    </td>
                </tr>
            </table>
            <h1 class="title">RECORDATORIO DE PAGO</h1>
        </div>
        <div class="content">
            <p>Estimado(a) <strong>{{ $customerName }}</strong>,</p>

            <p>Le informamos que el período de pago para su servicio de agua potable está próximo a vencer. A continuación encontrará los detalles de su adeudo:</p>

            <table class="paymentDetails">
                <tr>
                    <td>Toma de agua:</td>
                    <td>{{ $waterConnectionName }}</td>
                </tr>
                <tr>
                    <td>Fecha de vencimiento:</td>
                    <td>{{ $endDate }}</td>
                </tr>
                <tr>
                    <td>Días restantes:</td>
                    <td>{{ $daysRemaining }} días</td>
                </tr>
            </table>

            <p class="urgent">¡IMPORTANTE! El no realizar el pago oportuno puede resultar en la suspensión del servicio.</p>

            <div class="signature">
                <p>Atentamente,</p>
                <p><strong>El equipo de administración</strong></p>
            </div>
        </div>

        <div class="footer">
            <p>Contacto: <a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a></p>
            <p>Teléfono: {{ $senderPhone }}</p>
            <p>
                <a href="https://aquacontrol.rootheim.com/">AquaControl</a> |
                powered by 
                <a href="https://rootheim.com/">Root Heim Company</a>
                <img src="{{ $footerCid }}" width="20px" height="15px" alt="Root Heim Logo">
            </p>
        </div>
    </div>
</body>
</html>
