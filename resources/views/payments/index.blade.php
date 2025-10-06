@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Pagos')

@section('content')
    <section class="content">
        <div class="right_col" payment="main">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Pagos</h2>
                        <div class="row">
                            @include('payments.create')
                            @include('payments.clientPayments')
                            @include('payments.waterConnectionPayments')
                            <div class="col-12 order-first">
                                <form id="formSearch" method="GET" action="{{ route('payments.index') }}" class="mb-2">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-4 mt-2">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                                <input type="text" name="name" id="searchName" class="form-control"
                                                    placeholder="Buscar por nombre de cliente"
                                                    value="{{ request('name') }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-5 mt-2">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                                <input type="text" name="period" id="searchPeriod" class="form-control"
                                                    placeholder="Buscar por Fecha ejemplo: enero / 2024"
                                                    value="{{ request('period') }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2 pe-1 mt-2">
                                            <button type="submit" class="btn btn-primary w-100" title="Buscar">
                                                <i class="fas fa-search me-1"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <button type="button" class="btn btn-success flex-grow-1 flex-md-grow-0 mt-2 mr-1" data-toggle="modal"
                                        data-target="#createPayment" title="Registrar Pago">
                                        <i class="fa fa-plus"></i>
                                        <span class="d-none d-md-inline">Registrar Pago</span>
                                        <span class="d-inline d-md-none">Registrar Pago</span>
                                    </button>
                                    <a type="button" class="btn btn-secondary flex-grow-1 flex-md-grow-0 mt-2 ml-1" target="_blank"
                                        href="{{ route('report.current-customers') }}" title="Clientes al Día">
                                        <i class="fas fa-file-pdf"></i>
                                        <span class="d-none d-md-inline">Clientes al Día</span>
                                        <span class="d-inline d-md-none">Clientes al Día</span>
                                    </a>
                                    <button type="button" class="btn bg-maroon flex-grow-1 flex-md-grow-0 ml-1 mt-2 mr-2" data-toggle="modal"
                                        data-target="#clientPayments" title="Pagos por Cliente">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span class="d-none d-md-inline">Pagos por Cliente</span>
                                        <span class="d-inline d-md-none">Por Cliente</span>
                                    </button>
                                    <button type="button" class="btn bg-purple flex-grow-1 flex-md-grow-0 mt-2 ml-1" data-toggle="modal"
                                        data-target="#waterConnectionPayments" title="Pagos por Toma">
                                        <i class="fas fa-fw fa-water"></i>
                                        <span class="d-none d-md-inline">Pagos por Toma</span>
                                        <span class="d-inline d-md-none">Por Toma</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="payments" class="table table-striped display responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID PAGO</th>
                                                <th>CLIENTE</th>
                                                <th>DEUDA</th>
                                                <th>FECHA DE PAGO</th>
                                                <th>MONTO</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($payments) <= 0)
                                                <tr>
                                                    <td colspan="5">No hay resultados</td>
                                                </tr>
                                            @else
                                                @foreach($payments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>{{ $payment->debt->customer->name ?? 'Desconocido' }} {{ $payment->debt->customer->last_name ?? 'Desconocido' }}</td>
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($payment->debt->start_date)->locale('es')->isoFormat('MMMM [/] YYYY')}} -
                                                            {{ \Carbon\Carbon::parse($payment->debt->end_date)->locale('es')->isoFormat('MMMM [/] YYYY') }}
                                                            | Deuda: ${{ number_format($payment->debt->amount, 2) }}
                                                        </td>
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($payment->created_at)->locale('es')->isoFormat('DD [/] MMMM [/] YYYY HH:mm:ss')}}
                                                        </td>
                                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                                        <td>
                                                            <div class="btn-group" payment="group" aria-label="Opciones">
                                                                <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $payment->id }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @can('editPayment')
                                                                <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#editPayment{{$payment->id}}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                @endcan
                                                                @can('deletePayment')
                                                                <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $payment->id }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                                @endcan
                                                                <a type="button" class="btn btn-block bg-gradient-secondary mr-2" target="_blank" title="Generar Recibo"
                                                                    href="{{ route('reports.receiptPayment', Crypt::encrypt($payment->id)) }}">
                                                                    <i class="fas fa-file-invoice"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                        @include('payments.delete')
                                                        @include('payments.edit')
                                                        @include('payments.show')
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                       {!! $payments->links('pagination::bootstrap-4') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
