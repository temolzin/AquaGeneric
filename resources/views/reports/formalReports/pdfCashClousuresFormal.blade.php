<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Corte de Caja</title>
    <style>
        @page {
            margin: 110px 28px 60px 28px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #000000;
            margin: 0;
            padding: 0;
        }

        header {
            position: fixed;
            top: -95px;
            left: 0;
            right: 0;
            height: 85px;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 35px;
            font-size: 10px;
        }

        .footer-line {
            border-top: 1px solid #000000;
            margin-bottom: 6px;
        }

        main {
            padding-top: 10px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000000;
            border-bottom: 1.5px solid #000000;
            padding-bottom: 3px;
            margin-top: 18px;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        th, td {
            padding: 6px 8px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.report-table {
            margin-top: 5px;
            margin-bottom: 12px;
        }

        table.report-table thead th {
            background-color: #f5f5f5;
            color: #000000;
            font-weight: bold;
            text-align: left;
            font-size: 9.5px;
            border: 1px solid #cccccc;
            text-transform: uppercase;
        }

        table.report-table tbody td {
            border: 1px solid #cccccc;
            font-size: 10px;
            color: #000000;
        }

        table.report-table tr.total-row td {
            background-color: #fafafa;
            font-weight: bold;
            border-top: 2px solid #000000;
            border-bottom: 1px solid #cccccc;
        }

        .kpi-table {
            margin-bottom: 15px;
        }

        .kpi-card {
            border: 1px solid #cccccc;
            background-color: #f5f5f5;
            padding: 8px;
            text-align: center;
        }

        .kpi-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
        }

        .kpi-value {
            font-size: 13px;
            font-weight: bold;
            color: #000000;
            margin-top: 2px;
        }

        .right { text-align: right; }
        .center { text-align: center; }
        .left { text-align: left; }
        
        tr, td, th { page-break-inside: avoid; }
    </style>
</head>
<body>
    @php
        // 1. Obtención y formateo de la Localidad
        $localityObj = Auth::user()->locality ?? null;
        $localityName = $localityObj->name ?? null;
        $municipality = $localityObj->municipality ?? null;
        $state = $localityObj->state ?? null;
        $localityLineFormatted = "";
        if ($localityName) {
            $localityLineFormatted = "DE " . mb_strtoupper($localityName) . ", " . mb_strtoupper($municipality ?? '') . ", " . mb_strtoupper($state ?? '');
        }
        if (!empty($localityLine)) {
            $localityLineFormatted = $localityLine;
        }
        $userObj = $authUser ?? Auth::user();
        $generatedByFormatted = trim(($userObj->name ?? '') . ' ' . ($userObj->last_name ?? ''));
        if (empty($generatedByFormatted)) {
            $generatedByFormatted = 'Sistema AquaControl';
        }
        if (!empty($generatedBy)) {
            $generatedByFormatted = $generatedBy;
        }
        $reportTitleFormatted = $reportTitle ?? 'REPORTE DE CORTE DE CAJA';
        $generatedAtFormatted = $generatedAt ?? now()->format('d/m/Y H:i:s');
    @endphp
    <header>
        <table style="width:100%; border-collapse:collapse; border:1px solid #000; background:#fff; margin:0 auto;">
            <tr>
                <td style="vertical-align:middle; text-align:left; padding:12px 16px 10px 16px;">
                    <div style="font-weight:bold; font-size:16px; color:#000; text-transform:uppercase; line-height:1.2; text-align:left;">
                        COMITÉ DEL SISTEMA DE AGUA POTABLE
                    </div>

                    @if(!empty($localityLineFormatted))
                        <div style="font-size:11px; margin-top:3px; color:#000; text-align:left; font-weight:normal;">
                            {{ $localityLineFormatted }}
                        </div>
                    @endif

                    <div style="font-size:10px; margin-top:6px; color:#000; text-align:left;">
                        <strong>Reporte:</strong> {{ $reportTitleFormatted }} |
                        <strong>Generado:</strong> {{ $generatedAtFormatted }} |
                        <strong>Por:</strong> {{ $generatedByFormatted }}
                    </div>
                </td>
            </tr>
        </table>
    </header>
    <footer>
        <div class="footer-line"></div>
        <table style="width:100%;">
            <tr>
                <td style="vertical-align:bottom; color:#000;">
                    <strong>AquaControl</strong>
                    <span style="font-size:9px;"> powered by Root Heim Company</span>
                </td>
                <td class="right" style="vertical-align:bottom;">
                </td>
            </tr>
        </table>
    </footer>
    <main>
        @php
            $totalIngresos = $totalIncome ?? $totalPayments;
            $totalEgresosMonto = $totalExpenses ?? 0;
            $balanceNeto = $totalIngresos - $totalEgresosMonto;
        @endphp

        <table class="kpi-table">
            <tr>
                <td style="width: 33.33%; padding-left: 0;">
                    <div class="kpi-card">
                        <div class="kpi-title">Total Ingresos</div>
                        <div class="kpi-value">${{ number_format($totalIngresos, 2) }}</div>
                    </div>
                </td>
                <td style="width: 33.33%;">
                    <div class="kpi-card">
                        <div class="kpi-title">Total Egresos</div>
                        <div class="kpi-value">${{ number_format($totalEgresosMonto, 2) }}</div>
                    </div>
                </td>
                <td style="width: 33.33%; padding-right: 0;">
                    <div class="kpi-card" style="background-color: #eaebed;">
                        <div class="kpi-title">Balance Neto</div>
                        <div class="kpi-value">${{ number_format($balanceNeto, 2) }}</div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="section-title">Desglose de Pagos</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th class="right">Monto Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Efectivo</td>
                    <td class="right">${{ number_format($totalCash ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Tarjeta (Débito/Crédito)</td>
                    <td class="right">${{ number_format($totalCard ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Transferencia Bancaria</td>
                    <td class="right">${{ number_format($totalTransfer ?? 0, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Pagos Directos</strong></td>
                    <td class="right"><strong>${{ number_format($totalPayments ?? 0, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        @if(isset($earnings) && $earnings->count() > 0)
            <div class="section-title">2. Ingresos Adicionales</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Concepto / Tipo de Ingreso</th>
                        <th class="right">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($earnings as $earning)
                        <tr>
                            <td>{{ $earning->earningType?->name ?? 'Sin tipo' }}</td>
                            <td class="right">${{ number_format($earning->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td><strong>Total Ingresos Adicionales</strong></td>
                        <td class="right"><strong>${{ number_format($totalEarnings ?? 0, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @endif
        <div class="section-title">Desglose de Egresos</div>
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
        <table class="report-table">
            <thead>
                <tr>
                    <th>Categoría de Egreso</th>
                    <th class="right">Monto Total</th>
                </tr>
            </thead>
            <tbody>
                @if($expensesCollection->isEmpty())
                    <tr>
                        <td colspan="2" class="center" style="color: #000000;">No hay egresos registrados en este periodo.</td>
                    </tr>
                @endif
                @if(!$expensesCollection->isEmpty())
                    @foreach($expensesByType as $typeName => $amount)
                        <tr>
                            <td>{{ $typeName }}</td>
                            <td class="right">${{ number_format($amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td><strong>Total Egresos</strong></td>
                        <td class="right"><strong>${{ number_format($totalExpenses ?? 0, 2) }}</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </main>
</body>
</html>
