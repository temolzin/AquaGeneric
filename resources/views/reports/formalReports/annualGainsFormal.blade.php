@php
    $authUser = $authUser ?? auth()->user();
    $locality = $authUser->locality ?? null;
    $reportTitle = $reportTitle ?? 'GANANCIAS ANUALES';
    $generatedAt = $generatedAt ?? now()->format('d/m/Y H:i');
    $generatedBy = $generatedBy ?? trim(($authUser->name ?? '') . ' ' . ($authUser->last_name ?? ''));
    $localityLine = $locality
        ? 'LOCALIDAD DE ' . mb_strtoupper($locality->name ?? '-') . ', ' . mb_strtoupper($locality->municipality ?? '-') . ', ' . mb_strtoupper($locality->state ?? '-')
        : null;
    $logoPath = ($locality && $locality->hasMedia('localityGallery'))
        ? $locality->getFirstMedia('localityGallery')->getPath()
        : public_path('img/localityDefault.png');
@endphp

@extends('reports.layouts.base')

@section('title', 'REPORTE ANUAL DE GANANCIAS')

@push('styles')
    <style>
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            border: 1px solid #d9d9d9;
            background: #fff;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #d9d9d9;
            padding: 8px 10px;
            font-size: 11px;
            text-align: center;
        }

        .report-table th {
            background: #f4f4f4;
            font-weight: bold;
            text-transform: uppercase;
        }

        .right {
            text-align: right;
        }

        .total-row {
            background: #f9f9f9;
            font-weight: bold;
        }

        .subtitle {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: left;
        }
    </style>
@endpush

@section('content')
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

    <div style="margin-bottom: 18px; page-break-inside: avoid;">
        <p class="subtitle">Año: {{ $yearGains }}</p>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Ingresos</th>
                    <th>Gastos</th>
                    <th>Ganancias</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($months as $monthNumber => $monthName)
                    <tr>
                        <td>{{ $monthName }}</td>
                        <td>${{ number_format($monthlyEarnings[$monthNumber] ?? 0, 2) }}</td>
                        <td>${{ number_format($monthlyExpenses[$monthNumber] ?? 0, 2) }}</td>
                        <td>${{ number_format($monthlyGains[$monthNumber] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total</td>
                    <td>${{ number_format($totalEarnings, 2) }}</td>
                    <td>${{ number_format($totalExpenses, 2) }}</td>
                    <td>${{ number_format($totalGains, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
