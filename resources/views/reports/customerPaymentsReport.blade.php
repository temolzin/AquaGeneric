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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pagos por Toma de Agua</title>
    <style>
        html{
            margin: 0;
            padding: 0;
        }

        body{
            background-image: url('file://{{ $verticalBgPath }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #page_pdf {
            margin: 30px;
        }

        .aqua_title {
            font-family: 'Montserrat', sans-serif;
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 5px;
            margin-right: 5px;
            display: inline-block;
            text-decoration: none;
            color: #0B1C80;
            text-transform: uppercase;
        }

        p, label, span, table {
            font-family: 'Montserrat', sans-serif;
            font-size: 12pt;
        }

        .h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 17pt;
        }

        .h3 {
            font-weight: bold;
            font-family: 'Montserrat', sans-serif;
            font-size: 13pt;
            display: block;
            color: #0B1C80;
            text-align: left;
            margin-bottom: 5px;
        }

        #client_report {
            width: 100%;
            padding: 8px;
            margin: 3px 0;
            border: 1px solid #0B1C80;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .company_info {
            width: 50%;
            text-align: center;
            align-content: stretch;
            font-family: 'Montserrat', sans-serif;
        }

        .report_info {
            padding: 15px;
            border: 1px solid white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-family: 'Montserrat', sans-serif;
        }

        .client_info {
            width: 100%;
            padding-left: 10px;
        }

        .client_data {
            padding: 0;
            width: 100%;
            table-layout: fixed;
        }

        .client_data td {
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }

        .client_data td:last-child {
            padding-right: 0;
        }

        .client_data label {
            display: block;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .client_data p {
            margin: 0;
            font-weight: normal;
            min-height: 1.2em;
        }

        .field_container {
            display: flex;
            align-items: flex-start;
            min-height: 20px;
            margin-bottom: 3px;
        }

        .field_container label {
            min-width: 80px;
            margin-right: 8px;
        }

        .field_container p {
            flex: 1;
            margin: 0;
        }

        .table_text {
            text-align: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 12pt;
            color: #FFF;
        }

        .total_payment{
            padding: 12px;
            font-size: 15pt;
            text-align: right;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
        }

        .text_center {
            text-align: center;
            font-size: 12pt;
            font-family: 'Montserrat', sans-serif;
        }

        .text_right {
            text-align: right;
            font-size: 12pt;
            font-family: 'Montserrat', sans-serif;
        }

        .text_left {
            text-align: left;
            font-size: 12pt;
            font-family: 'Montserrat', sans-serif;
        }

        #report_detail {
            border-collapse: collapse;
            width: 100%;
            margin: 3px 0;
        }

        #report_detail thead th {
            background: #0B1C80;
            color: #FFF;
            padding: 5px;
        }

        #product_detail tr {
            border-top: 1px solid #bfc9ff;
        }

        .info_bottom {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            position: absolute;
            bottom: 5px;
            left: 20px; 
            right: 20px; 
        }

        .text_info {
            text-align: center;
            font-size: 12pt;
            font-family: 'Montserrat', sans-serif;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 30px;
            margin-top: 70px;
            margin-left: 10%;
        }

        .logo img {
            border-radius: 50%;
            width: 140px;
            height: 140px;
        }

        .link_whatsapp,
        .link_email {
            display: inline-block;
            text-decoration: none;
            border-radius: 5px;
            font-family: 'Montserrat', sans-serif;
            color: black;
        }

        .quarter_info {
            margin: 5px 0;
        }

        .main_table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .main_table thead tr td {
            border-bottom: none;
            padding-bottom: 20px;
        }
        
        .header_content {
            margin-bottom: 0px;
        }
        
        .header_first_page {
            display: block;
        }
        
        .header_other_pages {
            display: none;
        }
        
        .footer_last_page {
            display: block;
        }
        
        .footer_other_pages {
            display: none;
        }

        .water_connection_content {
            margin: 0 auto 10px;
            max-width: 95%;
        }

        .table_group {
            margin-bottom: 8px;
        }

        .consecutive_table {
            margin-top: 3px;
        }

        .reduced_space {
            margin: 5px 0;
        }

        @media print {
            body {
                background-image: none !important;
                background: white !important;
            }
            
            @page {
                background: white;
                margin: 2cm;
            }
            
            .header_first_page {
                display: block;
            }
            
            .water_connection_content {
                margin: 0 auto 5px;
                page-break-inside: avoid;
            }
            
            #client_report {
                margin: 10px 0;
                padding: 10px;
            }
            
            #report_detail {
                margin: 5px 0;
            }
            
            thead { 
                display: table-header-group; 
            }
            
            tfoot { 
                display: table-footer-group; 
            }
            
            .header_other_pages {
                display: none;
            }
            
            .footer_last_page {
                display: block;
                position: fixed;
                bottom: 20px;
                left: 0;
                right: 0;
            }
            
            .footer_other_pages {
                display: none;
            }
            
            #page_pdf {
                margin: 0;
            }
            
            .page_break {
                page-break-before: auto;
            }
            
            .h3 {
                margin: 8px 0 5px 0;
            }
            
            .field_container {
                margin-bottom: 2px;
            }
            
            .table_group {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="header_first_page">
        <table class="main_table">
            <thead>
                <tr>
                    <td colspan="2">
                        <div class="header_content">
                            <div class="logo">
                                @if ($authUser->locality->hasMedia('localityGallery'))
                                    <img src="{{ $authUser->locality->getFirstMediaUrl('localityGallery') }}" alt="Photo of {{ $authUser->locality->name }}">
                                @else
                                    <img src='img/localityDefault.png' alt="Default Photo">
                                @endif
                            </div>
                            <table id="report_header" style="width: 100%;">
                                <tr>
                                    <td class="company_info">
                                        <div>
                                            <p class="aqua_title"> COMITÉ DEL SISTEMA DE AGUA POTABLE DE {{ $authUser->locality->name }}, {{ $authUser->locality->municipality }}, {{ $authUser->locality->state }}
                                            </p><br>
                                            <a class="link_whatsapp" href="https://wa.me/525623640302">WhatsApp: +52 56 2364 0302</a><br>
                                            <a class="link_email" href="mailto:info@rootheim.com">Email: info@rootheim.com</a>
                                        </div>
                                    </td>
                                    <td class="report_info">
                                        <div class="round">
                                            <span class="h3">Reporte de Pagos</span>
                                            <p class="quarter_info"><strong>Fecha: </strong>{{ \Carbon\Carbon::now()->translatedFormat('j \d\e F \d\e Y') }}</p> 
                                            <p class="quarter_info"><strong>Trimestre: </strong>{{ $quarterName }}</p>
                                            <p class="quarter_info"><strong>Del</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('j \d\e F \d\e Y') }} <strong>Al</strong> {{ \Carbon\Carbon::parse($endDate)->translatedFormat('j \d\e F \d\e Y') }}</p> 
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </thead>
        </table>
    </div>
    <div id="page_pdf">
        @php
            $totalGeneral = 0;
            $connectionCounter = 0;
            $totalConnections = count($paymentsByWaterConnection);
        @endphp
        @foreach($paymentsByWaterConnection as $waterConnectionId => $waterConnectionData)
            @php $connectionCounter++; @endphp
            @if($connectionCounter > 1)
                <div style="page-break-before: auto;"></div>
            @endif
            <div class="table_group">
                <div class="water_connection_content">
                    <table id="client_report">
                        <tr>
                            <td class="client_info">
                                <div class="round">
                                    <span class="h3">Toma de Agua</span>
                                    <table class="client_data">
                                        <tr>
                                            <td>
                                                <div class="field_container">
                                                    <label>Nombre:</label> 
                                                    <p>{{ $waterConnectionData['water_connection']->name}}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="field_container">
                                                    <label>Tipo:</label> 
                                                    <p>
                                                        @php
                                                            $tipos = [
                                                                'commercial' => 'Comercial',
                                                                'residencial' => 'Residencial'
                                                            ];
                                                        @endphp
                                                        {{ $tipos[$waterConnectionData['water_connection']->type] ?? ucfirst($waterConnectionData['water_connection']->type) }}
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="reduced_space"></div>
                                    <span class="h3">Propietario</span>
                                    <table class="client_data">
                                        <tr>
                                            <td>
                                                <div class="field_container">
                                                    <label>Nombre:</label> 
                                                    <p>{{ $customer->name}} {{$customer->last_name}}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="field_container">
                                                    <label>Dirección:</label> 
                                                    <p>
                                                        {{ $customer->street }}, #{{ $customer->exterior_number }} 
                                                        @if($customer->interior_number)
                                                            Int. {{ $customer->interior_number }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table id="report_detail" class="consecutive_table">
                        <thead>
                            <tr>
                                <th class="table_text">Folio</th>
                                <th class="table_text">Fecha del Pago</th>
                                <th class="table_text">Folio Deuda</th>
                                <th class="table_text">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody id="product_detail">
                            @php
                                $subtotalConnection = 0;
                            @endphp
                            @if(count($waterConnectionData['payments']) > 0)
                                @foreach($waterConnectionData['payments'] as $debtId => $debtPayments)
                                    @foreach($debtPayments as $payment)
                                        @php
                                            $subtotalConnection += $payment->amount;
                                            $totalGeneral += $payment->amount;
                                        @endphp
                                        <tr>
                                            <td class="text_center">{{ $payment->id }}</td>
                                            <td class="text_center">{{ \Carbon\Carbon::parse($payment->created_at)->translatedFormat('j \d\e F \d\e Y') }}</td>
                                            <td class="text_center">{{ $payment->debt->id }}</td>
                                            <td class="text_center">$ {{ number_format($payment->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr>
                                    <td colspan="3" class="total_payment"><strong>Total pagado por la toma {{ $waterConnectionData['water_connection']->name }}:</strong></td>
                                    <td class="text_center"><strong>$ {{ number_format($subtotalConnection, 2) }}</strong></td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="4" class="text_center">No hay pagos registrados para esta toma en el trimestre seleccionado</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
    <div class="footer_last_page">
        <div class="info_bottom">
            <a class="text_info" href="https://aquacontrol.rootheim.com/"><strong>AquaControl</strong></a>
            <a class="text_info" href="https://rootheim.com/">powered by<strong> Root Heim Company </strong></a><img src="img/rootheim.png" width="15px" height="15px">
        </div>
    </div>
</body>
</html>
