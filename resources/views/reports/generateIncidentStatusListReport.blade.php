<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lista de Estatus de Incidencias</title>
        <style>
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
            html {
                margin: 0;
                padding: 15px;
            }

            body {
                height: 100%;
                margin: 0;
                padding: 0;
                background-image: url('img/backgroundReport.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }

            #pdfPage {
                margin-top: 10%;
                margin: 40px;
                text-align: center;
                width: 90%;
                min-height: 80%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .company_info {
                width: 100%;
                margin-top: 60px;
                text-align: center;
                align-content: stretch;
                font-family: 'Montserrat', sans-serif;
            }

            .aqua_title {
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
                text-align: center;
                margin-bottom: 5px;
            }

            .text_table {
                text-align: center;
                font-family: 'Montserrat', sans-serif;
                font-size: 12pt;
                color: #FFF;
            }

            .text_center {
                padding: 5px;
                background-color: #FFF;
                text-align: center;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
                vertical-align: middle;
                line-height: 1.6;
            }

            #detailedReport {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 150px;
                page-break-inside: auto;
            }

            #detailedReport thead {
                display: table-header-group;
            }

            #detailedReport thead th {
                background: #0B1C80;
                color: #FFF;
                padding: 5px;
            }

            #statusDetails tr {
                border-top: 1px solid #bfc9ff;
                min-height: 60px;
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

            .footer_text {
                text-align: center;
                font-size: 12pt;
                font-family: 'Montserrat', sans-serif;
                color: white;
                text-decoration: none;
                display: inline-block;
            }

            #headReport {
                justify-content: center;
                width: 100%;
            }

            #headReport .logo {
                height: auto;
                margin-left: 60px;
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
                </tr>
            </table>
            <div class="company_info">
                <p class="aqua_title">
                    COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                </p>
            </div>
            <div class="title">
                <h3>LISTA DE ESTATUS DE INCIDENCIAS</h3>
            </div>
            <table id="detailedReport">
                <thead>
                    <tr>
                        <th class="text_table">ID</th>
                        <th class="text_table">ESTATUS</th>
                        <th class="text_table">DESCRIPCIÓN</th>
                    </tr>
                </thead>
                <tbody id="statusDetails">
                    @foreach ($incidentStatus as $status)
                        <tr>
                            <td class="text_center">{{ $status->id }}</td>
                            <td class="text_center">{{ $status->status }}</td>
                            <td class="text_center">{{ $status->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="footer_info">
            <a class="footer_text" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <a class="footer_text" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a><img src="img/rootheim.png" width="15px" height="15px">
        </div>
    </body>
</html>
