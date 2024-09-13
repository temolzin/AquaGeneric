<!DOCTYPE html>
<html>
<head>
    <title>Customer List</title>
    <style>
        @page {
            size: legal landscape;
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #a4c5c7;
            font-size: 10px;
            font-weight: bold;
        }

        td {
            font-size: 12px;
        }

        @media print {
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <h1>COMITÉ DEL SISTEMA DE AGUA POTABLE, SANTIAGO TOLMAN A.C</h1>
    <table id="customers" class="table table-striped display responsive nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>NOMBRE</th>
                <th>MANZANA</th>
                <th>CALLE</th>
                <th># INTERIOR</th>
                <th>ESTADO CIVIL</th>
                <th>PAREJA</th>
                <th>¿TIENE TOMA?</th>
                <th>¿TIENE LOCAL?</th>
                <th>¿VA AL CORRIENTE?</th>
                <th>¿TIENE AGUA DE DIA Y NOCHE?</th>
                <th>DIAS DE AGUA</th>
                <th>¿CUENTA CON PRESION?</th>
                <th>¿TIENE CISTERNA?</th>
            </tr>
        </thead>
        <tbody>
            @if(count($customers) <= 0)
            <tr>
                <td colspan="13" style="text-align: center;">No hay resultados</td>
            </tr>
            @else
            @foreach($customers as $customer)
            <tr>
                <td scope="row">{{ $customer->id }}</td>
                <td class="name">{{ $customer->name }} {{ $customer->last_name }}</td>
                <td>{{ $customer->block }}</td>
                <td>{{ $customer->street }}</td>
                <td>{{ $customer->interior_number }}</td>
                <td>{{ $customer->marital_status ? 'Casado' : 'Soltero'}}</td>
                <td>{{ $customer->partner_name }}</td>
                <td>{{ $customer->has_water_connection ? 'Sí' : 'No' }}</td>
                <td>{{ $customer->has_store ? 'Sí' : 'No' }}</td>
                <td>{{ $customer->is_current ? 'Sí' : 'No' }}</td>
                <td>{{ $customer->has_water_24_7 ? 'Sí' : 'No' }}</td>
                <td>{{ $customer->water_days }}</td>
                <td>{{ $customer->has_pressure ? 'Sí' : 'No' }}</td>
                <td>{{ $customer->has_cistern ? 'Sí' : 'No' }}</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>
