@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Pagos adelantados')

@section('content')

<h2>Pagos adelantados</h2>

<div class="row">
    <div class="col-lg-12 text-right">
        <div class="btn-group" role="group" aria-label="Acciones de gráfica de pagos">
            <button class="btn btn-primary mr-2" data-toggle='modal' data-target="#paymentChart">
                <i class="fa fa-money-bill"></i> Gráfica de pagos
            </button>

            <a class="btn btn-success mr-2" data-toggle="modal" data-target="#paymentHistoryModal" title="Historial de pagos">
                <i class="fas fa-clipboard"></i> Historial de pagos
            </a>

            <button class="btn btn-secondary mr-2" data-toggle='modal' data-target="#paymentChart">
                <i class="fa fa-dollar-sign"></i> Comprobante de pagos
            </button>
        </div>
    </div>
</div>

@include('advancePayments.paymentHistoryModal')

@endsection
