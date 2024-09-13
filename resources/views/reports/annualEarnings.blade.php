<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Anual de Ganancias</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #020404;
            margin: 5;
            padding: 0;
        }
        .header {
            background-color: #a3c0d9;
            color: white;
            padding: 15px;
            text-align: center;
            border-bottom: 5px solid #15304b;
        }
        .header img {
            width: 80px;
            height: auto;
            vertical-align: middle;
        }
        .header h1 {
            display: inline;
            font-size: 12px;
            margin: 0;
            padding-left: 10px;
        }
       
        h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 20px;
            color: #343a40;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table, th, td {
            border: 1px solid #dee2e6;
        }
        th, td {
            padding: 12px;
            text-align: center;
            font-size: 16px;
        }
        th {
            background-color: #6c757d;
            color: white;
        }
        tfoot td {
            font-weight: bold;
            background-color: #343a40;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="img/gota.png" alt="Logo">
        <h1>COMITÉ DEL SISTEMA DE AGUA POTABLE, SANTIAGO TOLMAN A.C</h1>
    </div>

    <div class="container">
        <h2>Reporte Anual de Ganancias del Año: {{ $year }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Ganancias</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $months = [
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre'
                    ];
                @endphp

                @foreach($months as $monthNumber => $monthName)
                    <tr>
                        <td>{{ $monthName }}</td>
                        <td>${{ number_format($monthlyEarnings[$monthNumber] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td>${{ number_format($totalEarnings, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
