@php
    $authUser = $authUser ?? auth()->user();
    $locality = $authUser->locality ?? null;
    $reportTitle = 'LISTA DE CLIENTES';
    $generatedAt = $generatedAt ?? now()->format('d/m/Y H:i');
    $generatedBy = $generatedBy ?? ($authUser ? $authUser->name : 'Sistema');
    $localityLine = $locality ? "{$locality->name}, {$locality->municipality}, {$locality->state}" : 'Localidad no disponible';
    $logoPath = ($locality && $locality->hasMedia('localityGallery'))
        ? $locality->getFirstMedia('localityGallery')->getPath()
        : public_path('img/localityDefault.png');
@endphp

@extends('reports.layouts.base')

@section('title', 'Lista de clientes')
@section('subtitle', 'Clientes registrados por localidad')

@push('styles')
    <style>
        .page-break {
            page-break-before: always;
        }

        .report-table {
            margin-top: 12px;
        }

        .report-table th:nth-child(1), .report-table td:nth-child(1) { width: 10%; }
        .report-table th:nth-child(2), .report-table td:nth-child(2) { width: 28%; }
        .report-table th:nth-child(3), .report-table td:nth-child(3) { width: 42%; }
        .report-table th:nth-child(4), .report-table td:nth-child(4) { width: 20%; }
    </style>
@endpush

@section('content')
    <table class="report-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>NOMBRE</th>
                <th>DIRECCIÓN</th>
                <th>NUM. DE TOMAS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($firstPageCustomers as $customer)
                <tr>
                    <td class="center">{{ $customer->id }}</td>
                    <td>{{ trim(($customer->name ?? '') . ' ' . ($customer->last_name ?? '')) ?: 'Sin nombre' }}</td>
                    <td>
                        @php
                            $address = trim(($customer->street ?? '') . ' ' . ($customer->external_number ?? '') . ' ' . ($customer->interior_number ?? '') . ' ' . ($customer->colony ?? ''));
                        @endphp
                        {{ $address ?: 'Sin dirección' }}
                    </td>
                    <td class="center">{{ $customer->waterConnections->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @foreach ($otherPagesCustomers as $pageCustomers)
        <div class="page-break"></div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>DIRECCIÓN</th>
                    <th>NUM. DE TOMAS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pageCustomers as $customer)
                    <tr>
                        <td class="center">{{ $customer->id }}</td>
                        <td>{{ trim(($customer->name ?? '') . ' ' . ($customer->last_name ?? '')) ?: 'Sin nombre' }}</td>
                        <td>
                            @php
                                $address = trim(($customer->street ?? '') . ' ' . ($customer->external_number ?? '') . ' ' . ($customer->interior_number ?? '') . ' ' . ($customer->colony ?? ''));
                            @endphp
                            {{ $address ?: 'Sin dirección' }}
                        </td>
                        <td class="center">{{ $customer->waterConnections->count() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
@endsection
