<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte de Caja</title>
    <style>
        html{
            margin: 0;
            padding: 15px;
        }

        body{
            height: 100%;
            margin: 0;
            padding: 0;
            background-image: url('img/backgroundReport.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        #page_pdf {
            margin-top: 10%;
            margin: 40px;
        }

        .info_empresa {
            width: 100%;
            margin-top: 60px;
            text-align: center;
            align-content: stretch;
            font-family: 'Montserrat', sans-serif;
        }

        .aqua_titulo {
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

        .title {
            color: #0B1C80;
            font-family: 'Montserrat', sans-serif;
            font-size: 14pt;
            text-align: center;
        }

        #reporte_head {
            justify-content: center;
        }

        #reporte_head .logo {
            height: auto;
            margin-left: 60px;
        }

        #reporte_head .logo img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
        }

        .section {
            margin-top: 30px;
        }

        .section h4 {
            color: #0B1C80;
            text-align: center;
            margin-bottom: 10px;
        }

        .line {
            text-align: center;
            font-size: 12pt;
            padding: 3px 0;
        }

        .total_payment {
            padding: 20px;
            font-size: 15pt;
            text-align: right;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
        }

        .fecha_hora {
            text-align: center;
            margin: 20px 0;
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
    </style>
</head>
<body>
    <div id="page_pdf">
        <table id="reporte_head">
            <tr>
                <td>
                    <div class="logo">
                        @if ($closures->first()->locality && $closures->first()->locality->hasMedia('localityGallery'))
                            <img src="{{ $closures->first()->locality->getFirstMediaUrl('localityGallery') }}" alt="Photo of {{ $closures->first()->locality->name }}">
                        @else
                            <img src="img/localityDefault.png" alt="Default Photo">
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <div class="info_empresa">
            <p class="aqua_titulo">
                COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $closures->first()->locality->name ?? '-' }}, {{ $closures->first()->locality->municipality ?? '-' }}, {{ $closures->first()->locality->state ?? '-' }}
            </p>
        </div>

        <div class="title">
            <h3>REPORTE DE CORTE DE CAJA</h3>
        </div>

        <div class="fecha_hora">
            <strong>Fecha de cierre:</strong> {{ now()->format('d/m/Y') }} <br>
            <strong>Hora de cierre:</strong> {{ now()->format('h:i:s A') }} <br>
            <strong>Generado por:</strong> {{ $authUser->name ?? '-' }}
        </div>

        <div class="section">
            <h4>Ingresos</h4>
            <div class="line">Total Efectivo: ${{ number_format($totalCash, 2) }}</div>
            <div class="line">Total Tarjeta: ${{ number_format($totalCard, 2) }}</div>
            <div class="line">Total Transferencia: ${{ number_format($totalTransfer, 2) }}</div>
            <div class="line"><strong>Total ingresos: ${{ number_format($totalPayments, 2) }}</strong></div>
        </div>

        <div class="section">
            <h4>Egresos</h4>
            @php
                $typeLabels = [
                    'mainteinence' => 'Mantenimiento',
                    'services' => 'Servicios',
                    'supplies' => 'Suministros',
                    'taxes' => 'Impuestos',
                    'staff' => 'Personal',
                ];
                $expensesByType = $expenses->groupBy('type')->map(function($group) {
                    return $group->sum('amount');
                });
            @endphp

            @if($expenses->isEmpty())
                <div class="line">No hay egresos registrados.</div>
            @else
                @foreach($typeLabels as $key => $label)
                    @if(isset($expensesByType[$key]))
                        <div class="line">{{ $label }}: ${{ number_format($expensesByType[$key], 2) }}</div>
                    @endif
                @endforeach
                <div class="line"><strong>Total egresos: ${{ number_format($totalExpenses, 2) }}</strong></div>
            @endif
        </div>

        <h4 style="text-align:right; margin-top:20px; color:#0B1C80;">
            TOTAL NETO DEL CORTE: ${{ number_format(($totalPayments - $totalExpenses), 2) }}
        </h4>
    </div>

    <div class="info_Eabajo">
        <a class="text_infoE" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
        <a class="text_infoE" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a>
        <img src="img/rootheim.png" width="15px" height="15px">
    </div>
</body>
</html>
