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
                DE SANTIAGO TOLMAN A.C.
                OTUMBA, MÉXICO</h2>
            </div>
            <div class="logo">
            </div>
        </header>
        <div class="recibo-container">
            <table class="recibo-table">
                
                <tr>
                    <td class="header">PAGO POR MES:</td>
                    <td class="header">MZ: {{ $payment->debt->customer->block}}</td>
                    <td class="header-cell">REGISTRO:    </td>
                </tr>
                <tr>
                    <td colspan="2" class="header">
                        C: 
                        @if ($payment->debt->customer->status == '0')
                            {{ $payment->debt->customer->name ?? 'Desconocido' }} 
                            {{ $payment->debt->customer->last_name ?? 'Desconocido' }} 
                            (Pago realizado por: {{ $payment->debt->customer->responsible_name ?? 'Desconocido' }})
                        @else
                            {{ $payment->debt->customer->name ?? 'Desconocido' }} 
                            {{ $payment->debt->customer->last_name ?? 'Desconocido' }}
                        @endif
                    </td>
                    
                    <td class="folio">FOLIO:</td>
                </tr>
                <tr>
                    <td colspan="2" class="header">CON DOMICILIO: Calle. {{ $payment->debt->customer->street}}, # {{ $payment->debt->customer->interior_number ?? 'S/N'}}, Col. Santiago Tolman, Otumba</td>
                    <td rowspan="2" class="header">{{ $payment->id }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="header">BUENO POR: $ {{ $payment->amount }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="header">CANTIDAD CON LETRA POR LOS SIGUIENTES CONCEPTOS:</td>
                </tr>
            </table>
        </div>
        <div class="recibo-container">
            <p> PAGO POR CONSUMO DE AGUA POTABLE</p>
            <table>
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Año</th>
                        <th>Monto</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($months as $month)
                        <tr>
                            <td>{{ $month['month'] }}</td>
                            <td>{{ $month['year'] ?? '' }}</td>
                            <td>{{ $month['amount'] ? '$' . number_format($month['amount'], 2) : '' }}</td>
                            <td>{{ $month['note'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($message)
            <div class="recibo-footer">
                <p>{{ $message }}</p>
            </div>
        @endif
        <footer class="recibo-footer">  
            <p>SANTIAGO TOLMAN A: {{ \Carbon\Carbon::parse($payment->payment_date)->locale('es')->isoFormat('D [de]MMMM [del] YYYY')}}</p>
             <br><br> 
            _______________________________________________
            <p>COMITE DEL SISTEMA DE AGUA POTABLE</p>
        </footer>
        
    </div>
</body>
</html>
