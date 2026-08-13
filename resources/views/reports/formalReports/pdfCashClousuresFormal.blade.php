@php
    $authUser = $authUser ?? auth()->user();
    $locality = $authUser->locality ?? null;
    $reportTitle = $reportTitle ?? 'REPORTE DE CORTE DE CAJA';
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

@section('title', 'CORTE DE CAJA')

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
            text-align: left;
        }

        .report-table th {
            background: #f4f4f4;
            font-weight: bold;
            text-transform: uppercase;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .net-box {
            margin-top: 20px;
            padding: 12px;
            border: 1px solid #111;
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    <div style="margin-bottom: 15px;">
        <table class="report-table">
            <thead>
                <tr>
                    <th colspan="2">RESUMEN DE INGRESOS Y PAGOS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Efectivo</td>
                    <td class="right">${{ number_format($totalCash, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Tarjeta</td>
                    <td class="right">${{ number_format($totalCard, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Transferencia</td>
                    <td class="right">${{ number_format($totalTransfer, 2) }}</td>
                </tr>
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td>Total Pagos</td>
                    <td class="right">${{ number_format($totalPayments, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if(isset($earnings) && $earnings->count() > 0)
        <div style="margin-bottom: 15px;">
            <table class="report-table">
                <thead>
                    <tr>
                        <th colspan="2">INGRESOS ADICIONALES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($earnings as $earning)
                        <tr>
                            <td>{{ $earning->earningType?->name ?? 'Sin tipo' }}</td>
                            <td class="right">${{ number_format($earning->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                        <td>Total Ingresos Adicionales</td>
                        <td class="right">${{ number_format($totalEarnings, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
    <div style="margin-bottom: 15px;">
        <table class="report-table">
            <thead>
                <tr>
                    <th colspan="2">EGRESOS</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $expensesCollection = $expenses ?? collect();
                    $expensesByType = $expensesCollection
                        ->groupBy(function ($expense) {
                            return $expense->expenseType?->name ?? 'Sin tipo';
                        })
                        ->map(function ($group) {
                            return $group->sum('amount');
                        });
                @endphp

                @if($expensesCollection->isEmpty())
                    <tr>
                        <td colspan="2" class="center">No hay egresos registrados.</td>
                    </tr>
                @endif

                @if(!$expensesCollection->isEmpty())
                    @foreach($expensesByType as $typeName => $amount)
                        <tr>
                            <td>{{ $typeName }}</td>
                            <td class="right">${{ number_format($amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                        <td>Total Egresos</td>
                        <td class="right">${{ number_format($totalExpenses, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="net-box">
        TOTAL NETO DEL CORTE: ${{ number_format(($totalIncome ?? $totalPayments) - $totalExpenses, 2) }}
    </div>
@endsection
