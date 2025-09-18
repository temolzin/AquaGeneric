<!DOCTYPE html>
<html>
    <head>
        <title>RESUMEN DE TOMAS DE AGUA POR CLIENTE</title>
        <style>
            html {
                margin: 0;
                padding: 15px;
                height: 100%;
            }

            body {
                height: 100%;
                margin: 0;
                padding: 0;
                background-image: url('img/customersBackgroundHorizontal.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            #page_pdf {
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
                width: 85%;
                text-align: center;
                align-content: stretch;
                font-family: 'Montserrat', sans-serif;
            }

            .aqua_title {
                padding-right: 80px;
                padding-top: 60px;
                font-family: 'Montserrat', sans-serif;
                font-size: 18pt;
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
                font-size: 15pt;
            }

            .h3 {
                font-weight: bold;
                font-family: 'Montserrat', sans-serif;
                font-size: 13pt;
                display: block;
                color: #0B1C80;
                text-align: center;
                margin-bottom: 5px;
            }

            .text_table {
                text-align: center;
                font-family: 'Montserrat', sans-serif;
                font-size: 11pt;
                color: #FFF;
            }

            .text_center {
                background-color: #FFF;
                text-align: center;
                font-size: 11pt;
                font-family: 'Montserrat', sans-serif;
                vertical-align: middle;
                padding: 8px 5px;
                line-height: 1.6;
            }

            .connection_separator {
                margin: 8px 0;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-height: 50px;
                font-size: 11pt;
            }

            .address_cell .connection_separator {
                margin: 8px 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 50px; 
                font-size: 11pt;
            }

            #report_detail {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 150px;
                page-break-inside: auto;
            }

            #report_detail thead {
                display: table-header-group;
            }

            #report_detail thead th {
                background: #0B1C80;
                color: #FFF;
                padding: 5px;
            }

            #customer_detail tr {
                border-top: 1px solid #bfc9ff;
                min-height: 60px;
            }

            .footer_info {
                text-align: center;
                margin-top: 22px;
                padding: 10px;
                position: absolute;
                bottom: 1px;
                left: 20px;
                right: 20px;
            }

            .footer_text {
                text-align: center;
                font-size: 10pt;
                font-family: 'Montserrat', sans-serif;
                color: white;
                text-decoration: none;
                display: inline-block;
            }

            #report_head {
                justify-content: center;
                width: 100%;
                text-align: center;
            }

            #report_head .logo {
                height: auto;
                margin-left: 70px;
                margin-top: 60px;
                display: inline-block;
            }

            #report_head .logo img {
                border-radius: 50%;
                width: 100px;
                height: 100px;
            }

            .title {
                color: #0B1C80;
                font-family: 'Montserrat', sans-serif;
                font-size: 12pt;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div id="page_pdf">
            <table id="report_head">
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
                    <td class="company_info">
                        <div>
                            <p class="aqua_title">
                                COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="title">
                <h3>RESUMEN DE TOMAS DE AGUA POR CLIENTE</h3>
            </div>
            <table id="report_detail">
                <thead>
                    <tr>
                        <th class="text_table">ID</th>
                        <th class="text_table">NOMBRE</th>
                        <th class="text_table">NOMBRE DE LA TOMA</th>
                        <th class="text_table">COLONIA</th>
                        <th class="text_table">CALLE</th>
                        <th class="text_table">NUM. EXTERIOR</th>
                        <th class="text_table">NUM. INTERIOR</th>
                    </tr>
                </thead>
                <tbody id="customer_detail">
                    @foreach ($customers as $customer)
                        <tr>
                            <td class="text_center">{{ $customer->id }}</td>
                            <td class="text_center">{{ $customer->name }} {{ $customer->last_name }}</td>
                            <td class="text_center">
                                @foreach ($customer->waterConnections as $connection)
                                    <div class="connection_separator">
                                        <strong>Toma: {{ $connection->name ?: '-' }}</strong><br>
                                        @switch($connection->type)
                                            @case('commercial')
                                                (Comercial)
                                                @break
                                            @case('residencial')
                                                (Residencial)
                                                @break
                                            @default
                                                (No especificado)
                                                @break
                                        @endswitch
                                    </div>
                                @endforeach
                            </td>
                            <td class="text_center address_cell">
                                @foreach ($customer->waterConnections as $connection)
                                    <div class="connection_separator">
                                        {{ $connection->block ?: '-' }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="text_center address_cell">
                                @foreach ($customer->waterConnections as $connection)
                                    <div class="connection_separator">
                                        {{ $connection->street ?: '-' }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="text_center address_cell">
                                @foreach ($customer->waterConnections as $connection)
                                    <div class="connection_separator">
                                        {{ $connection->exterior_number ?: '-' }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="text_center address_cell">
                                @foreach ($customer->waterConnections as $connection)
                                    <div class="connection_separator">
                                        {{ $connection->interior_number ?: '-' }}
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="footer_info">
            <a class="footer_text" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <a class="footer_text" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a><img src="img/rootheim.png" width="20px" height="15px">
        </div>
    </body>
</html>
