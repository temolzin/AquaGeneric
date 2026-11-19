@php
$locality = Auth::user()->locality ?? null;
$verticalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundVertical')
    ? $locality->getFirstMedia('pdfBackgroundVertical')->getPath()
    : public_path('img/backgroundReport.png');

$horizontalBgPath = $locality && $locality->getFirstMedia('pdfBackgroundHorizontal')
    ? $locality->getFirstMedia('pdfBackgroundHorizontal')->getPath()
    : public_path('img/customersBackgroundHorizontal.png');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Agua</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 15mm;
            background-color: #f8f9fa;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 11px;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table {
            margin-bottom: 12px;
            border-bottom: 2px solid #333333;
            padding-bottom: 8px;
        }
        .company-title {
            color: #111111;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .company-subtitle {
            color: #666666;
            font-size: 11px;
            margin: 2px 0 0 0;
        }
        .badge-folio {
            background-color: #f0f0f0;
            color: #111111;
            text-align: right;
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #cccccc;
        }
        .badge-folio .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            color: #555555;
        }
        .badge-folio .value {
            font-size: 15px;
            font-weight: bold;
        }
        .main-grid {
            width: 100%;
        }
        .main-grid > tbody > tr > td {
            vertical-align: top;
        }
        .left-col {
            width: 61%;
            padding-right: 10px;
        }
        .right-col {
            width: 39%;
            padding-left: 10px;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }
        .card-title {
            color: #222222;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #cccccc;
            padding-bottom: 3px;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }
        .field-label {
            font-size: 8px;
            color: #777777;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .field-value {
            font-size: 11px;
            color: #111111;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .total-card {
            background-color: #f4f4f4;
            color: #111111;
            border: 1px solid #cccccc;
            border-radius: 4px;
            padding: 15px;
            text-align: center;
            margin-bottom: 12px;
        }
        .total-card .title {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            color: #555555;
        }
        .total-card .amount {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .total-card .pill {
            background-color: #e0e0e0;
            color: #333333;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 10px;
            display: inline-block;
            text-transform: uppercase;
            border: 1px solid #cccccc;
        }
        .summary-row {
            padding: 3px 0;
            font-size: 10px;
        }
        .summary-row.discount {
            color: #555555;
        }
        .summary-row.total {
            font-size: 12px;
            font-weight: bold;
            color: #111111;
            border-top: 1px solid #eeeeee;
            padding-top: 6px;
            margin-top: 4px;
        }
        .signature-area {
            text-align: center;
            padding-top: 73px;
        }
        .signature-line {
            border-top: 1px solid #aaaaaa;
            width: 80%;
            margin: 0 auto 5px auto;
        }
        .signer-name {
            font-size: 10px;
            font-weight: bold;
            color: #222222;
        }
        .signer-title {
            font-size: 8px;
            color: #777777;
        }
        .talon-container {
            border: 1.5px dashed #999999;
            border-radius: 6px;
            padding: 10px 15px;
            background-color: #ffffff;
            margin-top: 15px;
        }
        .talon-title {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #444444;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .talon-barcode {
            text-align: center;
            font-family: monospace;
            font-size: 11px;
            letter-spacing: 2px;
            color: #555555;
            margin-top: 10px;
        }
        .footer-branding {
            text-align: center;
            font-size: 9px;
            color: #888888;
            margin-top: 20px;
        }
        .footer-branding strong {
            color: #555555;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <h1 class="company-title">COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $payment->locality->name }} A.C.</h1>
                <p class="company-subtitle">{{ $payment->locality->municipality }}, {{ $payment->locality->state }}</p>
            </td>
            <td style="width: 180px; text-align: right; vertical-align: middle;">
                <div class="badge-folio">
                    <span class="label">Comprobante de Pago</span>
                    <span class="value">FOLIO: #{{ $payment->id }}</span>
                </div>
            </td>
        </tr>
    </table>
    <table class="main-grid">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-title">Datos del Cliente y Suministro</div>
                    <table>
                        <tr>
                            <td style="width: 50%; vertical-align: top;">
                                <div class="field-label">Nombre del Titular</div>
                                <div class="field-value">
                                    @if ($payment->debt->customer->responsible_name)
                                        {{ $payment->debt->customer->responsible_name }}
                                    @endif
                                    @if (!$payment->debt->customer->responsible_name)
                                        {{ $payment->debt->customer->name }} {{ $payment->debt->customer->last_name }}
                                    @endif
                                </div>
                                <div class="field-label">Dirección del Predio</div>
                                <div class="field-value" style="margin-bottom: 0;">
                                    {{ $payment->debt->customer->street }}, Mz. {{ $payment->debt->customer->exterior_number }}, {{ $payment->debt->customer->interior_number }}, {{ $payment->debt->customer->block }}, {{ $payment->customer->zip_code }}
                                    <br>
                                    <span style="font-weight: normal; color: #555555;">{{ $payment->customer->locality }}, {{ $payment->customer->state }}</span>
                                </div>
                            </td>
                            <td style="width: 50%; vertical-align: top;">
                                <div class="field-label">Identificador de Toma</div>
                                <div class="field-value">{{ $payment->debt->waterConnection->name }}</div>
                                <div class="field-label">Tipo de Servicio</div>
                                <div class="field-value" style="margin-bottom: 0;">
                                    @switch($payment->debt->waterConnection->type)
                                        @case('commercial') Comercial @break
                                        @case('residencial') Residencial @break
                                        @default {{ $payment->debt->waterConnection->type }}
                                    @endswitch
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card">
                    <div class="card-title">Detalle de la Deuda</div>
                    <table>
                        <tr>
                            <td style="width: 50%; vertical-align: top;">
                                <div class="field-label">Folio Deuda</div>
                                <div class="field-value">#{{ $payment->debt->id }}</div>
                                <div class="field-label">Periodo de Deuda</div>
                                <div class="field-value" style="margin-bottom: 0;">
                                    {{ \Carbon\Carbon::parse($payment->debt->start_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }} 
                                    al 
                                    {{ \Carbon\Carbon::parse($payment->debt->end_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}
                                </div>
                            </td>
                            <td style="width: 50%; vertical-align: top;">
                                <div class="field-label">Estado</div>
                                <div class="field-value">
                                    @php $remainingAmount = max(0, $payment->debt->amount - $payment->debt->debt_current); @endphp
                                    @if ($remainingAmount > 0)
                                        LIQUIDADO DEBE: ${{ number_format($remainingAmount, 2) }}
                                    @endif
                                    @if ($remainingAmount <= 0)
                                        PAGADO
                                    @endif
                                </div>
                                <div class="field-label">Observaciones</div>
                                <div class="field-value" style="margin-bottom: 0;">
                                    @if ($payment->debt->note)
                                        {{ $payment->debt->note }}
                                    @endif
                                    @if (!$payment->debt->note)
                                        Sin observaciones
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-title">Información del Pago</div>
                    <table>
                        <tr>
                            <td style="width: 50%; vertical-align: top;">
                                <div class="field-label">Fecha de Aplicación</div>
                                <div class="field-value">
                                    {{ \Carbon\Carbon::parse($payment->created_at)->setTimezone('America/Mexico_City')->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }} a las {{ \Carbon\Carbon::parse($payment->created_at)->setTimezone('America/Mexico_City')->locale('es')->format('H:i') }}
                                </div>
                                <div class="field-label">Método de Pago</div>
                                <div class="field-value" style="margin-bottom: 0;">
                                    @switch($payment->method)
                                        @case('cash') Efectivo @break
                                        @case('card') Tarjeta @break
                                        @case('transfer') Transferencia @break
                                        @case('openpay') Tarjeta (En línea) @break
                                        @default {{ $payment->method }}
                                    @endswitch
                                </div>
                            </td>
                            <td style="width: 50%; vertical-align: top;">
                                <div class="field-label">Observaciones</div>
                                <div class="field-value" style="margin-bottom: 0;">
                                    @if ($payment->note)
                                        {{ $payment->note }}
                                    @endif
                                    @if (!$payment->note && $payment->discount)
                                        Descuento aplicado: {{ $payment->discount->name }} - {{ $payment->discount->percentage }}%
                                    @endif
                                    @if (!$payment->note && !$payment->discount && $payment->isOpenPayPayment())
                                        Tx: {{ $payment->openpay_transaction_id }}
                                    @endif
                                    @if (!$payment->note && !$payment->discount && !$payment->isOpenPayPayment())
                                        Sin observaciones
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="right-col">
                <div class="total-card">
                    <div class="title">Total Pagado</div>
                    <div class="amount">${{ number_format($payment->amount, 2) }}</div>
                    <div class="pill">Pago Concluido</div>
                </div>
                <div class="card">
                    <div class="card-title">Resumen de Liquidación</div>
                    <table>
                        <tr class="summary-row">
                            <td>Subtotal:</td>
                            <td style="text-align: right;"><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                        </tr>
                        @if($payment->debt->discount)
                            <tr class="summary-row discount">
                                <td>Descuento Deuda ({{ $payment->debt->discount->percentage }}%):</td>
                                <td style="text-align: right;"><strong>-{{ $payment->debt->discount->name }}</strong></td>
                            </tr>
                        @endif
                        @if($payment->discount)
                            <tr class="summary-row discount">
                                <td>Descuento Pago ({{ $payment->discount->percentage }}%):</td>
                                <td style="text-align: right;"><strong>-{{ $payment->discount->name }}</strong></td>
                            </tr>
                        @endif
                        <tr class="summary-row total">
                            <td>Total Operación:</td>
                            <td style="text-align: right;">${{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @if($payment->debt->pending_amount > 0)
                            <tr class="summary-row" style="color: #444444; border-top: 1px dashed #cccccc; padding-top: 4px;">
                                <td>Usted debe:</td>
                                <td style="text-align: right;"><strong>${{ number_format($payment->debt->pending_amount, 2) }}</strong></td>
                            </tr>
                        @endif
                    </table>
                </div>
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-title">Autorización / Sello</div>
                    <div class="signature-area">
                        @if ($payment->isOpenPayPayment())
                            <p style="font-size: 8px; color: #666; margin: 10px 0;">
                                Este comprobante corresponde a un pago electrónico autorizado por la plataforma de pagos.<br>
                                <strong>ID:</strong> {{ $payment->openpay_transaction_id }}
                            </p>
                        @endif
                        @if (!$payment->isOpenPayPayment())
                            <div class="signature-line"></div>
                            <div class="signer-name">{{ $payment->creator->name }} {{ $payment->creator->last_name }}</div>
                            <div class="signer-title">Firma Recaudador</div>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div class="talon-container">
        <div class="talon-title">Validación Digital</div>
        <table>
            <tr>
                <td style="width: 33.3%;"><strong>ID Cliente:</strong> {{ $payment->debt->customer->id }}</td>
                <td style="width: 33.3%; text-align: center;"><strong>ID Toma:</strong> {{ $payment->debt->waterConnection->name }}</td>
                <td style="width: 33.3%; text-align: right;"><strong>Monto:</strong> ${{ number_format($payment->amount, 2) }} MXN</td>
            </tr>
        </table>
        <div class="talon-barcode">| ID PAGO: #{{ $payment->id }} FOLIO DEUDA: #{{ $payment->debt->id }} |</div>
    </div>
    <div class="footer-branding">
        <strong>AquaControl</strong> — Sistema Integral de Gestión de Agua Potable | Powered by <strong> Root Heim Company</strong>
    </div>
</body>
</html>
