@php
$locality = Auth::user()->locality ?? null;
$verticalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundVertical')
    ? $locality->getFirstMedia('pdfBackgroundVertical')->getPath()
    : public_path('img/backgroundReport.png');

$horizontalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundHorizontal')
    ? $locality->getFirstMedia('pdfBackgroundHorizontal')->getPath()
    : public_path('img/customersBackground.png');
@endphp
<!DOCTYPE html>
<html>
    <head>
        <title>Lista de Empleados</title>
        <style>
            html {
                margin: 0;
                padding: 15px;
            }

            body {
                height: 100%;
                margin: 0;
                padding: 0;
                background-image: url("{{ public_path('img/customersBackgroundHorizontal.png') }}");
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }

            #pdfPage {
                margin: 40px;
            }

            .companyInfo {
                width: 85%;
                text-align: center;
                align-content: stretch;
                font-family: 'Montserrat', sans-serif;
            }

            .titleAqua {
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

            #employeeDetails tr {
                border-top: 1px solid #bfc9ff;
            }

            .infoBelow {
                text-align: center;
                margin-top: 22px;
                padding: 10px;
                position: absolute;
                bottom: 1px;
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
                margin-left: 70px;
                margin-top: 60px;
            }

            #headReport .logo img {
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
                <h3>LISTA DE EMPLEADOS</h3>
            </div>
            <table id="detailedReport">
                <thead>
                    <tr>
                        <th class="textable">ID</th>
                        <th class="textable">NOMBRE</th>
                        <th class="textable">LOCALIDAD</th>
                        <th class="textable">C.P.</th>
                        <th class="textable">ESTADO</th>
                        <th class="textable">COLONIA</th>
                        <th class="textable">CALLE</th>
                        <th class="textable">NO. EXT.</th>
                        <th class="textable">NO. INT.</th>
                        <th class="textable">CORREO E.</th>
                        <th class="textable">NUM .TELEFÓNO</th>
                        <th class="textable">ROL</th>
                    </tr>
                </thead>
                <tbody id="employeeDetails">
                    @foreach ($employees as $employee)
                        <tr>
                            <td class="textcenter">{{ $employee->id }}</td>
                            <td class="textcenter">{{ $employee->name }} {{ $employee->last_name }}</td>
                            <td class="textcenter">{{ $employee->locality }}</td>
                            <td class="textcenter">{{ $employee->zip_code }}</td>
                            <td class="textcenter">{{ $employee->state }}</td>
                            <td class="textcenter">{{ $employee->block}}</td>
                            <td class="textcenter">{{ $employee->street }}</td>
                            <td class="textcenter">{{ $employee->exterior_number }}</td>
                            <td class="textcenter">{{ $employee->interior_number }}</td>
                            <td class="textcenter">{{ $employee->email }}</td>
                            <td class="textcenter">{{ $employee->phone_number }}</td>
                            <td class="textcenter">{{ $employee->rol}}</td>

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
