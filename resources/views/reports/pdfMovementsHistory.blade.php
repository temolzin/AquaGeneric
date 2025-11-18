@php
use Illuminate\Support\Facades\Auth;

$verticalBgPath = $authUserLocality && $authUserLocality->getFirstMedia('pdfBackgroundVertical')
    ? $authUserLocality->getFirstMedia('pdfBackgroundVertical')->getPath()
    : public_path('img/backgroundReport.png');

$reportType = 'individual';
if ($module === 'todos' && $showModuleColumn) {
    $reportType = 'grouped-by-module';
} elseif ($module === 'todos' && !$showModuleColumn) {
    $reportType = 'all-without-grouping';
}

$titles = [
    'individual' => 'HISTORIAL DE MOVIMIENTOS ' . strtoupper($selectedModuleName ?? ''),
    'grouped-by-module' => 'HISTORIAL DE MOVIMIENTOS POR MÓDULO',
    'all-without-grouping' => 'HISTORIAL DE MOVIMIENTOS'
];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Movimientos - {{ $authUserLocality->name ?? '-' }}</title>
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

        h3.module-title {
            color: #0B1C80;
            text-transform: uppercase;
            margin-top: 40px;
            text-align: center;
            font-size: 15pt;
            border-bottom: 2px solid #0B1C80;
            padding-bottom: 5px;
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
            color: white;
            text-decoration: none;
            display: inline-block;
            font-family: 'Montserrat', sans-serif;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div id="page_pdf">
        <div id="reporte_head">
            <div class="logo">
                @if ($authUserLocality && $authUserLocality->hasMedia('localityGallery'))
                    <img src="{{ $authUserLocality->getFirstMediaUrl('localityGallery') }}" alt="Logo">
                @else
                    <img src="img/localityDefault.png" alt="Default Photo">
                @endif
            </div>
            <div class="titulo">
                <p class="aqua_titulo">
                    COMITÉ DEL SISTEMA DE AGUA POTABLE DE<br>
                    {{ $authUserLocality->name ?? '-' }}, {{ $authUserLocality->municipality ?? '-' }}, {{ $authUserLocality->state ?? '-' }}
                </p>
            </div>
        </div>        
        <div class="title">
            <h3>{{ $titles[$reportType] }}</h3>
        </div> 
        @if($reportType === 'grouped-by-module' && !empty($groupedByModule))
            @foreach($groupedByModule as $moduleName => $days)
                <h3 class="module-title">{{ strtoupper($moduleName) }}</h3>
                @foreach($days as $day => $entries)
                    <h4>{{ $day }}</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Responsable</th>
                                <th>Hora</th>
                                <th>ID</th>
                                <th>Movimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                @php
                                    $movement = $entry['movement'];
                                    $actionType = !empty($movement->deleted_at) ? 'Eliminación' : 'Edición';
                                @endphp
                                <tr>
                                    <td>{{ trim(optional($movement->creator)->name . ' ' . optional($movement->creator)->last_name . ' ' . optional($movement->creator)->second_last_name) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($movement->updated_at)->format('H:i:s') }}</td>
                                    <td>{{ $movement->id }}</td>
                                    <td>{{ $actionType }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
                @if(!$loop->last)
                    <div class="page-break"></div>
                @endif
            @endforeach
        @elseif($reportType === 'all-without-grouping' && !empty($groupedByDay))
            @foreach($groupedByDay as $day => $entries)
                <h4>{{ $day }}</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Responsable</th>
                            <th>Hora</th>
                            <th>ID</th>
                            <th>Módulo</th>
                            <th>Movimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                            @php
                                $movement = $entry['movement'];
                                $moduleName = $entry['module'];
                                $actionType = !empty($movement->deleted_at) ? 'Eliminación' : 'Edición';
                            @endphp
                            <tr>
                                <td>{{ trim(optional($movement->creator)->name . ' ' . optional($movement->creator)->last_name . ' ' . optional($movement->creator)->second_last_name) }}</td>
                                <td>{{ \Carbon\Carbon::parse($movement->updated_at)->format('H:i:s') }}</td>
                                <td>{{ $movement->id }}</td>
                                <td>{{ $moduleName }}</td>
                                <td>{{ $actionType }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @elseif($reportType === 'individual' && !empty($groupedByDay))
            @foreach($groupedByDay as $day => $entries)
                <h4>{{ $day }}</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Responsable</th>
                            <th>Hora</th>
                            <th>ID</th>
                            <th>Movimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                            @php
                                $movement = $entry['movement'];
                                $actionType = !empty($movement->deleted_at) ? 'Eliminación' : 'Edición';
                            @endphp
                            <tr>
                                <td>{{ trim(optional($movement->creator)->name . ' ' . optional($movement->creator)->last_name . ' ' . optional($movement->creator)->second_last_name) }}</td>
                                <td>{{ \Carbon\Carbon::parse($movement->updated_at)->format('H:i:s') }}</td>
                                <td>{{ $movement->id }}</td>
                                <td>{{ $actionType }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endif
        <div class="footer_info">
            <a class="footer_text" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <a class="footer_text" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
            <img src="img/rootheim.png" width="15px" height="15px">
        </div>
    </div>
</body>
</html>