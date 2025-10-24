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
            font-size: 13pt;
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

        .textcenter {
            text-align: center;
            font-size: 12pt;
            font-family: 'Montserrat', sans-serif;
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
            <?php
            ?>
            <img src="{{ public_path('img/localityDefault.png') }}" alt="Logo">
        </div>

        <table id="reporte_head">
            <tr>
                <td class="info_empresa">
                    <div>
                        <p class="aqua_titulo">COMITÉ DEL SISTEMA DE AGUA POTABLE</p><br>
                        <p><strong>Datos de Usuario</strong></p>
                    </div>
                </td>
                <td class="info_reporte">
                    <div class="round">
                        <span class="h3">Información de Cuenta</span>
                        <p><strong>Fecha: </strong><?php echo $_GET['date'] ?? date('j \d\e F \d\e Y'); ?></p> 
                    </div>
                </td>
            </tr>
        </table>

        <table id="reporte_cliente">
            <tr>
                <td class="info_cliente">
                    <div class="round">
                        <span class="h3">Datos Personales del Usuario</span>
                        <table class="datos_cliente">
                            <tr>
                                <td><label>ID Cliente:</label> <p>{{ $customer->id }}</p></td>
                                <td><label>Fecha de Registro:</label> <p>{{ $customer->created_at }}</p></td>
                            </tr>
                            <tr>
                                <td><label>Nombre:</label> <p>{{ $user->name }} {{ $user->last_name }}</p></td>
                                <td><label>Email:</label> <p>{{ $user->email }}</p></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label>Contraseña Temporal:</label>
                                    <p>
                                        {{ $temporaryPassword }}
                                    </p>
                                    <p style="color: red; font-size: 12px; font-style: italic;">
                                        Esta contraseña es temporal y solo se mostrara una vez. Te recomendamos guardarla y actualizarla inmediatamente después de iniciar sesión.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <br>
                        <span class="h3">Información Adicional</span>
                        <table class="datos_cliente">
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
    
    <div class="info_Eabajo">
        <a class="text_infoE" href="#"><strong>AquaControl</strong></a>
        <a class="text_infoE" href="#">powered by<strong> Root Heim Company </strong></a>
        <img src="img/rootheim.png" width="15px" height="15px">
    </div>
</body>
</html>