<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Datos de Usuario</title>
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

        #report_header {
            margin: 0;
            padding-bottom: 10px;
        }

        .aqua_title {
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

        .subtitle {
            font-weight: bold;
            font-family: 'Montserrat', sans-serif;
            font-size: 13pt;
            display: block;
            color: #0B1C80;
            text-align: left;
            margin-bottom: 5px;
        }

        #client_report {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #0B1C80;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .company_info {
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

        .client_info {
            width: 100%;
            height: auto;
            padding-left: 15px;
        }

        .client_data {
            padding: 0;
            width: 100%;
        }

        .client_data label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .client_data p {
            margin: 0;
            font-weight: normal;
        }

        .table-text {
            text-align: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 12pt;
            color: #FFF;
        }

        .text-center {
            text-align: center;
            font-size: 12pt;
            font-family: 'Montserrat', sans-serif;
        }

        .footer_info {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            position: absolute;
            bottom: 5px;
            left: 20px;
            right: 20px;
        }

        .footer-text {
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

        .whatsapp-link,
        .email-link {
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
            <?php
            ?>
            <img src="{{ public_path('img/localityDefault.png') }}" alt="Logo">
        </div>

        <table id="report_header">
            <tr>
                <td class="company_info">
                    <div>
                        <p class="aqua_title">COMITÉ DEL SISTEMA DE AGUA POTABLE</p><br>
                        <p><strong>Datos del Usuario</strong></p>
                    </div>
                </td>
                <td class="report_info">
                    <div class="rounded">
                        <span class="subtitle">Información de Cuenta</span>
                        <?php
                        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_MX.UTF-8', 'spanish');
                        if (isset($_GET['date']) && !empty($_GET['date'])) {
                            $fecha = strftime('%e de %B de %Y', strtotime($_GET['date']));
                        } else {
                            $fecha = strftime('%e de %B de %Y');
                        }
                        ?>
                        <p><strong>Fecha: </strong><?php echo $fecha; ?></p>
                    </div>
                </td>
            </tr>
        </table>

        <table id="client_report">
            <tr>
                <td class="client_info">
                    <div class="rounded">
                        <span class="subtitle">Datos Personales del Usuario</span>
                        <table class="client_data">
                            <tr>
                                @if(!empty($showCustomerId))
                                    <td><label>ID Cliente:</label> <p>{{ $customer->id }}</p></td>
                                    <td><label>Fecha de Registro:</label> <p>{{ $customer->updated_at }}</p></td>
                                @else
                                    <td colspan="2">
                                        <label>Fecha de Registro:</label> <p>{{ $customer->updated_at }}</p>
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                <td><label>Nombre:</label> <p>{{ $user->name }} {{ $user->last_name }}</p></td>
                                <td><label>Email:</label> <p>{{ $user->email }}</p></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label>Contraseña:</label>
                                    <p>
                                        {{ $temporaryPassword }}
                                    </p>
                                    <p style="color: red; font-size: 12px; font-style: italic;">
                                        Credenciales para tu inicio de sesion. Te recomendamos guardarlos bien para poder iniciar sesión.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <br>
                        <span class="subtitle">Información Adicional</span>
                        <table class="client_data">
                            <tr>
                                <td><label>Estado:</label> <p>{{ $customer->state ?? 'N/A' }}</p></td>
                                <td><label>Localidad:</label> <p>{{ $customer->locality ?? 'N/A' }}</p></td>
                                {{-- <td><label>Localidad:</label> <p>{{ $customer->locality->name ?? 'N/A' }}</p></td> --}}
                            </tr>
                            <tr>
                                <td><label>Calle:</label> <p>{{ $customer->street ?? 'N/A' }}</p></td>
                                <td><label>Número Interior:</label> <p>{{ $customer->interior_number ?? 'N/A' }}</p></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer_info">
        <a class="footer-text" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
        <a class="footer-text" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
        <img src="img/rootheim.png" width="15px" height="15px">
    </div>
</body>
</html>
