@php
    $authUser = $authUser ?? auth()->user();
    $locality = $authUser->locality ?? null;
    $reportTitle = 'PAGOS ADELANTADOS';
    $generatedAt = now()->format('d/m/Y H:i');
    $generatedBy = trim(($authUser->name ?? '') . ' ' . ($authUser->last_name ?? ''));
    $localityLine = $locality
        ? 'LOCALIDAD DE ' . mb_strtoupper($locality->name ?? '-') . ', ' . mb_strtoupper($locality->municipality ?? '-') . ', ' . mb_strtoupper($locality->state ?? '-')
        : null;
    $logoPath = ($locality && $locality->hasMedia('localityGallery'))
        ? $locality->getFirstMedia('localityGallery')->getPath()
        : public_path('img/localityDefault.png');
    $types = ['commercial' => 'Comercial', 'residencial' => 'Residencial'];
@endphp

@extends('reports.layouts.base')

@section('title', 'REPORTE DE PAGOS ADELANTADOS')

@push('styles')
    <style>
        .section-title { font-size: 11px; font-weight: bold; margin: 16px 0 6px; }
        .data-table, .report-table { width: 100%; border-collapse: collapse; background: #fff; }
        .data-table td, .report-table td, .report-table th { border: 1px solid #d9d9d9; padding: 8px 10px; }
        .data-table td { font-size: 10px; }
        .report-table th { background: #f4f4f4; font-size: 10px; text-align: center; text-transform: uppercase; }
        .report-table td { font-size: 10px; text-align: center; }
        .total-row { background: #f9f9f9; font-weight: bold; }
    </style>
@endpush

@section('content')
    <p class="section-title">CLIENTE</p>
    <table class="data-table">
        <tr>
            <td><strong>Nombre:</strong> {{ $customer->name }} {{ $customer->last_name }}</td>
            <td><strong>Dirección:</strong> {{ $customer->street }}, #{{ $customer->interior_number }}</td>
        </tr>
    </table>

    <p class="section-title">TOMA DE AGUA</p>
    <table class="data-table">
        <tr>
            <td><strong>Nombre:</strong> {{ $waterConnection->name }}</td>
            <td><strong>Tipo:</strong> {{ $types[$waterConnection->type] ?? ucfirst($waterConnection->type) }}</td>
        </tr>
    </table>

    <p class="section-title">DETALLE DE PAGOS</p>
    <table class="report-table">
        <thead>
            <tr>
                <th>Folio de pago</th>
                <th>Fecha del pago</th>
                <th>Período adelantado</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $debtPayments)
                @foreach ($debtPayments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->debt->start_date)->translatedFormat('F Y') }} - {{ \Carbon\Carbon::parse($payment->debt->end_date)->translatedFormat('F Y') }}</td>
                        <td>${{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align:right;">Total de pagos adelantados:</td>
                <td style="text-align:center;">${{ number_format($totalPayments, 2) }}</td>
            </tr>
        </tbody>
    </table>
@endsection
