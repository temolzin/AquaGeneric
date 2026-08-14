@php
    $authUser = $authUser ?? auth()->user();
    $locality = $authUser->locality ?? null;
    $reportTitle = $reportTitle ?? 'INGRESOS DEL ' . ($year ?? now()->year);
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

@section('title', 'REPORTE ANUAL DE INGRESOS')

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

        .period-total {
            margin-top: 25px;
            padding: 10px;
            border: 1px solid #111;
            background-color: #f2f2f2;
            text-align: right;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    @php
        $months = [
            1  => 'Enero',
            2  => 'Febrero',
            3  => 'Marzo',
            4  => 'Abril',
            5  => 'Mayo',
            6  => 'Junio',
            7  => 'Julio',
            8  => 'Agosto',
            9  => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
    @endphp

    <table class="report-table">
        <thead>
            <tr>
                <th>MES</th>
                <th>INGRESOS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($months as $monthNumber => $monthName)
                <tr>
                    <td>{{ $monthName }}</td>
                    <td>${{ number_format($monthlyEarnings[$monthNumber] ?? 0, 2) }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td class="right">Total del Año:</td>
                <td>${{ number_format($totalEarnings, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="period-total">
        TOTAL GENERAL: ${{ number_format($totalEarnings, 2) }}
    </div>
@endsection
