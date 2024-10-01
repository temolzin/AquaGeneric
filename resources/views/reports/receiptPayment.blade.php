<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Agua</title>
    <style>
       @page {
            margin: 0;
        }

        .title {
            text-transform: uppercase;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f0f8ff;
            color: #0a072e;
            font-family: Arial, sans-serif;
        }

        .recibo {
            margin: 5;
            padding: 10;
        }
        .recibo-header {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 0;
            font-size: 9px;
        }

        .recibo-table, table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            border-radius: 10px;
            overflow: hidden; 
        }

        .recibo-table td, th, td {
            border: 2px solid #0e0c4f;
            padding: 5px;
            text-align: center;
            font-size: 7px;
            font-weight: bold;
        }
        .folio {
            text-align: center;
        }

        p{
            text-align: center;
            font-size: 7px;
            font-weight: bold;
        }

        .header-cell {
            font-weight: bold;
            text-align: left;
        }

        .recibo-table .header {
            font-weight: bold;
            text-align: left;
        }

        .recibo-footer {
            text-align: center;
            font-size: 10px;
        }

    </style>
</head>
<body>
    <div class="recibo">
        <header class="recibo-header">
            <div class="logo">
            </div>
            <div class="title">
                <h2>COMITÉ DEL SISTEMA DE AGUA POTABLE
                DE {{ $payment->locality->locality_name }} A.C.
                {{ $payment->locality->municipality }}, {{ $payment->locality->state }}</h2>
            </div>
            <div class="logo">
            </div>
        </header>
        <div class="card card-bg">
            <div class="card-content">
                <h2>Recibo de pago</h2>
            </div>
        </div>
        <table id="table_pago_dos">
            <tr>
                <td class="datos_pago_dos">
                    <div>
                        <p>Nombre del cliente: {{ $payment->debt->customer->name }} {{ $payment->debt->customer->last_name }}</p>
                        <p>Dirección: {{ $payment->debt->customer->street }} #{{ $payment->debt->customer->interior_number }},  {{ $payment->debt->customer->block }}, {{ $payment->locality->zip_code }}, {{ $payment->locality->locality_name }}, {{ $payment->locality->municipality }}  </p>
                    </div>
                </td>
            </tr>
        </table>
        <table id="table_pago_uno">
            <tr>
                <td class="datos_pago_uno">
                    <div>
                        <p>Folio: {{ $payment->id }}</p>
                        <p>Fecha y hora del pago: {{ \Carbon\Carbon::parse($payment->created_at)->locale('es')->isoFormat('D [de ]MMMM [del] YYYY') }} a las {{ \Carbon\Carbon::parse($payment->created_at)->locale('es')->format('H:i') }} </label></p>
                    </div>
                </td>
            </tr>
        </table>
        <table id="table_pago_tres">
            <tr>
                <td class="datos_pago_tres">
                    <div>
                        <p>Monto total del pago: ${{ $payment->amount }}</p>
                        <p>Correspondiente a la deuda: {{ $payment->debt->id }}</p>
                        <p>Fecha de la deuda: {{ \Carbon\Carbon::parse($payment->debt->start_date)->locale('es')->isoFormat('D [de ]MMMM [del] YYYY') }}</p>
                        <p>Fecha de vencimiento de la deuda: {{\Carbon\Carbon::parse($payment->debt->end_date)->locale('es')->isoFormat('D [de ]MMMM [del] YYYY')}}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="info_Eabajo">
        <a class="text_infoE" href="https://www.rootheim.com/">AquaControl powered by Root Heim Company</a>
    </div>

        <footer class="recibo-footer">  
            <p>{{ $payment->locality->locality_name }} A: {{ \Carbon\Carbon::parse($payment->created_at)->locale('es')->isoFormat('D [de]MMMM [del] YYYY')}}</p>
             <br><br> 
            _______________________________________________
            <p>{{ $payment->creator->name }} {{ $payment->creator->last_name }}</p>
        </footer>
        
    </div>
</body>
</html>
