<!DOCTYPE html>
<html>
    <head>
        <title>Lista de Clientes</title>
        <style>
            html {
                margin: 0;
                padding: 15px;
            }

            body {
                height: 100%;
                margin: 0;
                padding: 0;
                background-image: url('img/customersBackgroundHorizontal.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }

            #page_pdf {
                margin: 40px;
            }

            .info_empresa {
                width: 85%;
                text-align: center;
                align-content: stretch;
                font-family: 'Montserrat', sans-serif;
            }

            .aqua_titulo {
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
                text-align: left;
                margin-bottom: 5px;
            }

            .textable {
                text-align: center;
                font-family: 'Montserrat', sans-serif;
                font-size: 10pt;
                color: #FFF;
            }

            .textcenter {
                background-color: #FFF;
                text-align: center;
                font-size: 9pt;
                font-family: 'Montserrat', sans-serif;
            }

            #reporte_detalle {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 150px;
                page-break-inside: auto;
            }

            #reporte_detalle thead th {
                background: #0B1C80;
                color: #FFF;
                padding: 5px;
            }

            #detalle_clientes tr {
                border-top: 1px solid #bfc9ff;
            }

            .info_Eabajo {
                text-align: center;
                margin-top: 22px;
                padding: 10px;
                position: absolute;
                bottom: 1px;
                left: 20px;
                right: 20px;
            }

            .text_infoE {
                text-align: center;
                font-size: 10pt;
                font-family: 'Montserrat', sans-serif;
                color: white;
                text-decoration: none;
                display: inline-block;
            }

            #reporte_head {
                justify-content: center;
            }

            #reporte_head .logo {
                height: auto;
                margin-left: 70px;
                margin-top: 60px;
            }

            #reporte_head .logo img {
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
            <table id="reporte_head">
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
                    <td class="info_empresa">
                        <div>
                            <p class="aqua_titulo">
                                COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="title">
                <h3>LISTA DE CLIENTES</h3>
            </div>
            <table id="reporte_detalle">
                <thead>
                    <tr>
                        <th class="textable">ID</th>
                        <th class="textable">NOMBRE</th>
                        <th class="textable">MANZANA</th>
                        <th class="textable">CALLE</th>
                        <th class="textable">NUM. INTERIOR</th>
                        <th class="textable">ESTADO CIVIL</th>
                        <th class="textable">NUM. DE TOMAS</th>
                        <th class="textable">TOMAS</th>
                        <th class="textable">DÍAS DE AGUA</th>
                        <th class="textable">¿CUENTA CON PRESION?</th>
                        <th class="textable">¿TIENE CISTERNA?</th>
                    </tr>
                </thead>
                <tbody id="detalle_clientes">
                    @foreach ($customers as $customer)
                        <tr>
                            <td class="textcenter">{{ $customer->id }}</td>
                            <td class="textcenter">{{ $customer->name }} {{ $customer->last_name }}</td>
                            <td class="textcenter">{{ $customer->block }}</td>
                            <td class="textcenter">{{ $customer->street }}</td>
                            <td class="textcenter">{{ $customer->interior_number }}</td>
                            <td class="textcenter">{{ $customer->marital_status ? 'Casado' : 'Soltero'}}</td>
                            <td class="textcenter">{{ $customer->waterConnections->count() }}</td>
                            <td class="textcenter">
                                @foreach ($customer->waterConnections as $connection)
                                    <strong>Toma: {{ $connection->name }}</strong>
                                    @switch($connection->type)
                                        @case('commercial')
                                            (Comercial)
                                            @break
                                        @case('residencial')
                                            (Residencial)
                                            @break
                                        @default
                                            No especificado
                                            @break
                                    @endswitch
                                    <br>
                                @endforeach
                            </td>
                            <td class="textcenter">
                                @foreach ($customer->waterConnections as $connection)
                                    <br>
                                    @php
                                        $days = json_decode($connection->water_days);
                                    @endphp
                                    @if (is_array($days))
                                        @foreach ($days as $day)
                                            @switch($day)
                                                @case('monday')
                                                    Lunes
                                                    @break
                                                @case('tuesday')
                                                    Martes
                                                    @break
                                                @case('wednesday')
                                                    Miércoles
                                                    @break
                                                @case('thursday')
                                                    Jueves
                                                    @break
                                                @case('friday')
                                                    Viernes
                                                    @break
                                                @case('saturday')
                                                    Sábado
                                                    @break
                                                @case('sunday')
                                                    Domingo
                                                    @break
                                                @default
                                                    No especificado
                                                    @break
                                            @endswitch
                                            <br>
                                        @endforeach
                                    @else
                                        @switch($connection->water_days)
                                            @case('"all"')
                                                Diario
                                                @break
                                            @default
                                                No especificado
                                                @break
                                        @endswitch
                                    @endif
                                    <br>
                                @endforeach
                            </td>
                            <td class="textcenter">
                                @foreach ($customer->waterConnections as $connection)
                                    <br>
                                    {{ $connection->has_water_pressure ? 'Sí' : 'No' }} <br><br>
                                @endforeach
                            </td>
                            <td class="textcenter">
                                @foreach ($customer->waterConnections as $connection)
                                    <br>
                                    {{ $connection->has_cistern ? 'Sí' : 'No' }} <br><br>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="info_Eabajo">
            <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <a class="text_infoE" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a><img src="img/rootheim.png" width="20px" height="15px">
        </div>
    </body>
</html>
