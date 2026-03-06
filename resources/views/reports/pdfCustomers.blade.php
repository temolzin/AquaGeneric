@extends('reports.layouts.base')

@section('title')
    Lista de clientes
@endsection

@section('subtitle')
    {{ $localityLine ?? '' }}
@endsection

@section('content')
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:60px;">ID</th>
                <th>NOMBRE</th>
                <th>DIRECCIÓN</th>
                <th style="width:90px;">NUM. TOMAS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td class="center">{{ $customer->id }}</td>
                    <td>{{ $customer->name }} {{ $customer->last_name }}</td>
                    <td>
                        {{ $customer->street }}
                        No. Ext. {{ $customer->exterior_number ?? '—' }}
                        No. Int. {{ $customer->interior_number ?? '—' }}
                    </td>
                    <td class="center">{{ $customer->water_connections_count ?? $customer->waterConnections->count() }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
