@php
$locality = Auth::user()->locality ?? null;
$verticalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundVertical')
    ? $locality->getFirstMedia('pdfBackgroundVertical')->getPath()
    : public_path('img/backgroundReport.png');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tomas de Agua - Sección {{ $section->name }}</title>
    <style>
        html { margin: 0; padding: 15px; }
        body {
            margin: 0;
            padding: 0;
            background-image: url('file://{{ $verticalBgPath }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'Montserrat', sans-serif;
        }
        #page_pdf { margin: 40px; }
        .info_empresa { width: 100%; margin-top: 60px; text-align: center; }
        .aqua_titulo {
            font-size: 20pt;
            font-weight: bold;
            color: #0B1C80;
            text-transform: uppercase;
        }
        .title { color: #0B1C80; font-size: 14pt; text-align: center; margin-bottom: 15px; }
        #reporte_head { justify-content: center; margin-bottom: 20px; }
        #reporte_head .logo img { border-radius: 50%; width: 120px; height: 120px; }
        .section_info_header { margin-top: 20px; text-align: left; }
        .section_info_header h4, .section_info_header p { margin: 0; }
        .oval_color {
            display: inline-block;
            background-color: {{ $section->color }};
            color: #fff;
            font-weight: bold;
            font-size: 11pt;
            border-radius: 18px;
            padding: 2px 10px;
            margin-left: 5px;
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
        #reporte_detalle tbody td {
            background: #FFF;
            text-align: center;
            font-size: 10pt;
            padding: 5px;
            border-top: 1px solid #bfc9ff;
        }
        .info_Eabajo {
            text-align: center;
            margin-top: 22px;
            padding: 10px;
            position: absolute;
            bottom: 5px;
            left: 20px;
            right: 20px;
        }
        .text_infoE { font-size: 10pt; color: white; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <div id="page_pdf">
        <table id="reporte_head">
            <tr>
                <td>
                    <div class="logo">
                        @if ($authUser->locality && $authUser->locality->hasMedia('localityGallery'))
                            <img src="{{ $authUser->locality->getFirstMediaUrl('localityGallery') }}" alt="Logo">
                        @else
                            <img src="img/localityDefault.png" alt="Default Photo">
                        @endif
                    </div>
                </td>
            </tr>
        </table>
        <div class="info_empresa">
            <p class="aqua_titulo">
                COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name ?? '-' }}, {{ $authUser->locality->municipality ?? '-' }}, {{ $authUser->locality->state ?? '-' }}
            </p>
        </div>
        <div class="section_info_header">
            <h4>Sección: <span class="oval_color">{{ $section->name }}</span></h4>
            <p><strong>Total de Tomas:</strong> {{ $section->waterConnections->count() }}</p>
        </div>
        <div class="title">
            <h3>TOMAS DE AGUA DE LA {{ $section->name }}</h3>
        </div>
        <table id="reporte_detalle">
            <thead>
                <tr>
                    <th>NOMBRE DE LA TOMA</th>
                    <th>PROPIETARIO</th>
                    <th>TIPO</th>
                    <th>COSTO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($section->waterConnections as $connection)
                    <tr>
                        <td>{{ $connection->name ?? 'Sin nombre' }}</td>
                        <td>{{ $connection->customer->name ?? 'Sin propietario' }} {{ $connection->customer->last_name ?? '' }}</td>
                        <td>{{ ucfirst($connection->type) }}</td>
                        <td>${{ number_format($connection->cost->price ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="info_Eabajo">
        <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
        <a class="text_infoE" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
        <img src="img/rootheim.png" width="15px" height="15px">
    </div>
</body>
</html>
