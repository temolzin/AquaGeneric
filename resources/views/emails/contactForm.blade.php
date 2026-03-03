<!DOCTYPE html>
<html>
<head>
    <title>Nuevo mensaje de contacto</title>
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

        .contactDetails {
            width: 100%;
            margin: 25px 0;
            border-collapse: collapse;
        }

        .contactDetails td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .contactDetails td:first-child {
            font-weight: bold;
            color: #0B1C80;
            width: 30%;
        }

        .messageBox {
            background-color: #f4f6fb;
            border-left: 4px solid #0B1C80;
            padding: 15px 20px;
            margin: 20px 0;
            font-size: 12pt;
            line-height: 1.6;
            white-space: pre-line;
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
            <h1 class="title">NUEVO MENSAJE DE CONTACTO</h1>
        </div>
        <div class="content">
            <p>Se ha recibido un nuevo mensaje desde el formulario de contacto de <strong>AquaControl</strong>.</p>

            <table class="contactDetails">
                <tr>
                    <td>Nombre:</td>
                    <td>{{ $name }}</td>
                </tr>
                <tr>
                    <td>Correo:</td>
                    <td>{{ $email }}</td>
                </tr>
            </table>

            <p><strong>Mensaje:</strong></p>
            <div class="messageBox">{{ $contactMessage }}</div>
        </div>

        <div class="footer">
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
