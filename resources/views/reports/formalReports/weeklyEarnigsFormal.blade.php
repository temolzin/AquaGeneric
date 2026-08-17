@php
    $authUser = $authUser ?? auth()->user();
    $locality = $authUser->locality ?? null;
    $reportTitle = $reportTitle ?? 'INGRESOS SEMANALES';
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

@section('title', 'REPORTE SEMANAL DE INGRESOS')

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

        .subtitle {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: left;
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
        $daysInSpanish = [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado',
            'Sunday'    => 'Domingo',
        ];
    @endphp

    @foreach ($weeks as $week)
        <div style="margin-bottom: 20px; page-break-inside: avoid;">
            <p class="subtitle">
                Semana del {{ \Carbon\Carbon::parse($week['start'])->translatedFormat('j \d\e F') }} al {{ \Carbon\Carbon::parse($week['end'])->translatedFormat('j \d\e F') }}
            </p>
            <table class="report-table">
                <thead>
                    <tr>
                        @foreach ($daysInSpanish as $dayName)
                            <th>{{ $dayName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach ($week['dailyEarnings'] as $dayItem)
                            <td>
                                @if($dayItem['date'] < $startDate || $dayItem['date'] > $endDate)
                                    N/A
                                @endif

                                @if(!($dayItem['date'] < $startDate || $dayItem['date'] > $endDate))
                                    ${{ number_format($dayItem['amount'] ?? 0, 2) }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="7" class="right" style="font-weight: bold; padding: 8px;">
                            Total de la semana: ${{ number_format($week['weekTotal'] ?? 0, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="period-total">
        TOTAL DEL PERIODO: ${{ number_format($totalPeriodEarnings, 2) }}
    </div>
@endsection
