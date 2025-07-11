<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lista de Estatus de Incidencias</title>
        <style>
            html {
                margin: 0;
                padding: 15px;
            }

            body {
                height: 100%;
                margin: 0;
                padding: 0;
                background-image: url('img/customersBackground.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }

            #pdfPage {
                margin: 40px;
            }

            .companyInfo {
                width: 75%;
                text-align: center;
                align-content: stretch;
                font-family: 'Montserrat', sans-serif;
            }

            .titleAqua {
                padding-top: 25px;
                font-family: 'Montserrat', sans-serif;
                font-size: 20pt;
                font-weight: bold;
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

            .textable {
                text-align: center;
                font-family: 'Montserrat', sans-serif;
                font-size: 12pt;
                color: #FFF;
            }

            .textcenter {
                background-color: #FFF;
                text-align: center;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
            }

            #detailedReport {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 150px;
                page-break-inside: auto;
            }

            #detailedReport thead th {
                background: #0B1C80;
                color: #FFF;
                padding: 5px;
            }

            #statusDetails tr {
                border-top: 1px solid #bfc9ff;
            }

            .infoBelow {
                text-align: center;
                margin-top: 22px;
                padding: 10px;
                position: absolute;
                bottom: 5px;
                left: 20px;
                right: 20px;
            }

            .textInfo {
                text-align: center;
                font-size: 10pt;
                font-family: 'Montserrat', sans-serif;
                color: white;
                text-decoration: none;
                display: inline-block;
            }

            #headReport {
                justify-content: center;
            }

            #headReport .logo {
                height: auto;
                margin-top: 60px;
            }

            #headReport .logo img {
                border-radius: 50%;
                width: 120px;
                height: 120px;
            }

            .title {
                color: #0B1C80;
                font-family: 'Montserrat', sans-serif;
                font-size: 14pt;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div id="pdfPage">
            <table id="headReport">
                <tr>
                    <td>
                        <div class="logo">
                            @if ($authUser->locality->hasMedia('localityGallery'))
                                <img src="{{ $authUser->locality->getFirstMediaUrl('localityGallery') }}" alt="Photo of {{ $authUser->locality->name }}">
                            @else
                                <img src='img/localityDefault.png' alt="Default Photo">
                            @endif
                        </div>
                    </td>
                    <td class="companyInfo">
                        <div>
                            <p class="titleAqua">
                                COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="title">
                <h3>LISTA DE ESTATUS DE INCIDENCIAS</h3>
            </div>
            <table id="detailedReport">
                <thead>
                    <tr>
                        <th class="textable">ID</th>
                        <th class="textable">DESCRIPCIÓN</th>
                        <th class="textable">ESTATUS</th>
                    </tr>
                </thead>
                <tbody id="statusDetails">
                    @foreach ($incidentStatus as $status)
                        <tr>
                            <td class="textcenter">{{ $status->id }}</td>
                            <td class="textcenter">{{ $status->description }}</td>
                            <td class="textcenter">{{ $status->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="infoBelow">
            <a class="textInfo" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <a class="textInfo" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a><img src="img/rootheim.png" width="20px" height="15px">
        </div>
    </body>
</html>
