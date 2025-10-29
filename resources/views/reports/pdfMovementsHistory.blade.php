@php
$authUser = Auth::user();
$locality = $locality ?? $authUser->locality ?? null;

$verticalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundVertical')
    ? $locality->getFirstMedia('pdfBackgroundVertical')->getPath()
    : public_path('img/backgroundReport.png');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Movimientos - {{ $locality->name ?? '-' }}</title>
    <style>
        html {
            margin: 0;
            padding: 15px;
        }

        body {
            margin: 0;
            padding: 0;
            background-image: url('file://{{ $verticalBgPath }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'Montserrat', sans-serif;
        }

        #page_pdf {
            margin: 40px;
            margin-top: 10%;
        }

        #reporte_header {
            justify-content: center;
        }

        .logo {
            height: auto;
            margin-left: 60px;
        }

        .logo img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
        }

        .titulo {
            width: 100%;
            margin-top: 60px;
            text-align: center;
        }

        .aqua_titulo {
            font-size: 20pt;
            font-weight: bold;
            margin-right: 5px;
            display: inline-block;
            text-decoration: none;
            color: #0B1C80;
            text-transform: uppercase;
        }

        .title {
            color: #0B1C80;
            font-size: 14pt;
            text-align: center;
            margin-bottom: 15px;
        }

        h4 {
            color: #0B1C80;
            text-align: left;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 25px;
        }

        table thead th {
            background: #0B1C80;
            color: #FFF;
            padding: 5px;
            font-size: 10pt;
        }

        table tbody td {
            background: rgba(255,255,255,0.95);
            text-align: center;
            font-size: 10pt;
            padding: 6px;
            border-top: 1px solid #bfc9ff;
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
            color: white;
            text-decoration: none;
            display: inline-block;
            font-family: 'Montserrat', sans-serif;
        }

    </style>
</head>
<body>
    <div id="page_pdf">
        <div id="reporte_head">
            <div class="logo">
                @if ($locality && $locality->hasMedia('localityGallery'))
                    <img src="{{ $locality->getFirstMediaUrl('localityGallery') }}" alt="Logo de {{ $locality->name }}">
                @else
                    <img src="img/localityDefault.png" alt="Default Photo">
                @endif
            </div>
            <div class="titulo">
                <p class="aqua_titulo">
                    COMITÉ DEL SISTEMA DE AGUA POTABLE DE<br>
                    {{ $locality->name ?? '-' }}, {{ $locality->municipality ?? '-' }}, {{ $locality->state ?? '-' }}
                </p>
            </div>
        </div>
        <div class="title">
            <h3>HISTORIAL DE MOVIMIENTOS</h3>
        </div>
        @foreach($groupedByDay as $day => $movements)
            <h4>{{ $day }}</h4>
            <table>
                <thead>
                    <tr>
                        <th>Responsable</th>
                        <th>Hora</th>
                        <th>Módulo</th>
                        <th>Movimiento</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $entry)
                        @php
                            $movement = $entry['movement'];
                            $moduleName = $entry['module'];
                            $tipo = !empty($movement->deleted_at) ? 'Eliminación' : 'Edición';
                        @endphp
                        <tr>
                            <td>{{ $movement->user->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($movement->updated_at)->format('H:i:s') }}</td>
                            <td>{{ $moduleName }}</td>
                            <td>{{ $tipo }}</td>
                            <td>{{ $movement->note ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    <div class="info_Eabajo">
        <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
        <a class="text_infoE" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
        <img src="img/rootheim.png" width="15px" height="15px">
    </div>
    </div>
</body>
</html>
