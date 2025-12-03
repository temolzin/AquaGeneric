@php
$verticalBgPath = $authUserLocality?->getFirstMedia('pdfBackgroundVertical')?->getPath() ?: public_path('img/backgroundReport.png');
$reportType = $reportType ?? 'single-module';
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
            font-size: 11pt; 
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
            font-size: 9pt; 
        }
        table thead th { 
            background: #0B1C80; 
            color: #FFF; 
            padding: 10px; 
            font-size: 9pt; 
            font-weight: bold; 
        }
        table tbody td { 
            background: rgba(255,255,255,0.95); 
            text-align: center; 
            font-size: 8.5pt; 
            padding: 8px; 
            border-top: 1px solid #bfc9ff; 
            vertical-align: top;
        }
        .changes-content {
            font-family: 'Arial', sans-serif; 
            font-size: 8pt; 
            white-space: pre-wrap; 
            word-wrap: break-word;
            margin: 0; text-align: center;
            line-height: 1.4;
            max-height: 150px; 
            overflow-y: auto;
            background: transparent; 
            padding: 0; 
            border: none; 
            border-radius: 0;
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
        .no-data { 
            text-align: center; 
            color: #666; 
            font-style: italic; 
            padding: 20px; 
            font-size: 10pt; 
        }
        .error-message {
            text-align: center; 
            color: #dc3545; 
            font-weight: bold; 
            padding: 40px; 
            font-size: 12pt; 
            background: rgba(255,255,255,0.9); 
            border-radius: 5px; 
            margin: 20px;
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
                    <img src="{{ public_path('img/localityDefault.png') }}" alt="Default Photo">
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
            <h3>{{ $reportTitles[$reportType] ?? 'HISTORIAL DE MOVIMIENTOS' }}</h3>
            @if($startDate || $endDate)
                <p style="font-size: 10pt;">
                    @if($startDate) Desde: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} @endif
                    @if($endDate) Hasta: {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} @endif
                </p>
            @endif
        </div> 
        @if(isset($error))
            <div class="error-message"><p>{{ $error }}</p></div>
        @elseif(empty($groupedByDay) && empty($groupedByModule))
            <div class="no-data"><p>No se encontraron registros para los filtros seleccionados.</p></div>
        @else
            @switch($reportType)
                @case('all-modules-grouped')
                    @foreach($groupedByModule as $moduleName => $days)
                        <h3 class="module-title">{{ strtoupper($moduleName) }}</h3>
                        @foreach($days as $day => $movements)
                            <h4>{{ $day }}</h4>
                            <table>
                                <thead>
                                    <tr>
                                        <th width="15%">Responsable</th>
                                        <th width="10%">Hora</th>
                                        <th width="10%">Acción</th>
                                        <th width="10%">ID Registro</th>
                                        <th width="27.5%">Datos Anteriores</th>
                                        <th width="27.5%">Datos Actuales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movements as $movement)
                                        <tr>
                                            <td>{{ trim($movement->user?->name . ' ' . $movement->user?->last_name . ' ' . $movement->user?->second_last_name) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($movement->created_at)->format('H:i:s') }}</td>
                                            <td>
                                                @switch($movement->action)
                                                    @case('update') Actualizado @break
                                                    @case('delete') Eliminado @break
                                                    @default {{ $movement->action }}
                                                @endswitch
                                            </td>
                                            <td><strong>{{ $movement->record_id ?? 'N/A' }}</strong></td>
                                            <td><div class="changes-content">{{ $movement->formatted_before }}</div></td>
                                            <td><div class="changes-content">{{ $movement->formatted_current }}</div></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                        @if(!$loop->last)<div class="page-break"></div>@endif
                    @endforeach
                    @break
                @case('all-modules-ungrouped')
                    @foreach($groupedByDay as $day => $movements)
                        <h4>{{ $day }}</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th width="12%">Responsable</th>
                                    <th width="8%">Hora</th>
                                    <th width="10%">Módulo</th>
                                    <th width="8%">Acción</th>
                                    <th width="8%">ID Registro</th>
                                    <th width="27%">Datos Anteriores</th>
                                    <th width="27%">Datos Actuales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $movement)
                                    <tr>
                                        <td>{{ trim($movement->user?->name . ' ' . $movement->user?->last_name . ' ' . $movement->user?->second_last_name) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($movement->created_at)->format('H:i:s') }}</td>
                                        <td>{{ $moduleNames[$movement->module] ?? $movement->module }}</td>
                                        <td>
                                            @switch($movement->action)
                                                @case('update') Actualizado @break
                                                @case('delete') Eliminado @break
                                                @default {{ $movement->action }}
                                            @endswitch
                                        </td>
                                        <td><strong>{{ $movement->record_id ?? 'N/A' }}</strong></td>
                                        <td><div class="changes-content">{{ $movement->formatted_before }}</div></td>
                                        <td><div class="changes-content">{{ $movement->formatted_current }}</div></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                    @break
                @case('single-module')
                @default
                    @foreach($groupedByDay as $day => $movements)
                        <h4>{{ $day }}</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th width="15%">Responsable</th>
                                    <th width="10%">Hora</th>
                                    <th width="10%">Acción</th>
                                    <th width="10%">ID Registro</th>
                                    <th width="27.5%">Datos Anteriores</th>
                                    <th width="27.5%">Datos Actuales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $movement)
                                    <tr>
                                        <td>{{ trim($movement->user?->name . ' ' . $movement->user?->last_name . ' ' . $movement->user?->second_last_name) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($movement->created_at)->format('H:i:s') }}</td>
                                        <td>
                                            @switch($movement->action)
                                                @case('update') Actualizado @break
                                                @case('delete') Eliminado @break
                                                @default {{ $movement->action }}
                                            @endswitch
                                        </td>
                                        <td><strong>{{ $movement->record_id ?? 'N/A' }}</strong></td>
                                        <td><div class="changes-content">{{ $movement->formatted_before }}</div></td>
                                        <td><div class="changes-content">{{ $movement->formatted_current }}</div></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
            @endswitch
        @endif
        <div class="footer_info">
            <a class="footer_text" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <a class="footer_text" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
            <img src="{{ public_path('img/rootheim.png') }}" width="15px" height="15px">
        </div>
    </div>
</body>
</html>
