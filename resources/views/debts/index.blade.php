@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Deudas')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Deudas</h2>
                    <div class="row mb-2">
                        @include('debts.create')
                        @include('debts.periods')
                        <div class="col-lg-12">
                            <div class="d-lg-flex justify-content-between align-items-center flex-wrap">
                                <form method="GET" action="{{ route('debts.index') }}" class="flex-grow-1 mt-2" style="min-width: 328px; max-width: 30%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por ID del cliente" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Deuda">Buscar</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="d-flex flex-column flex-lg-row justify-content-end mb-2 mt-2 mt-lg-0">
                                    <button type="button" class="btn btn-primary mb-2 mb-lg-0 mr-lg-2 w-100 w-lg-auto" data-toggle="modal" title="Asignar Deuda a Todos" data-target="#assignDebtModal">
                                        <i class="fa fa-plus"></i>
                                        Asignar a Todos
                                    </button>
                                    <button type="button" class="btn btn-success mx-1" data-toggle="modal"
                                            title="Registrar Deuda" data-target="#createDebt">
                                        <i class="fa fa-plus"></i>
                                        <span class="d-none d-lg-inline">Registrar Deuda</span>
                                        <span class="d-inline d-lg-none">Registrar Deuda</span>
                                    </button>
                                    <button type="button" class="btn btn-success mb-2 mb-lg-0 mr-lg-2 w-100 w-lg-auto" data-toggle="modal" title="Crear Deuda" data-target="#createDebt">
                                        <i class="fa fa-plus"></i>
                                        Crear Deuda
                                    </button>
                                    <a class="btn btn-secondary w-100 w-lg-auto" target="_blank" title="Clientes con deudas" href="{{ route('report.with-debts') }}">
                                        <i class="fas fa-file-pdf"></i>
                                        Clientes con deudas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="debts" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>CLIENTE</th>
                                            <th>TOTAL DE LA DEUDA</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($debts as $customer)
                                            <tr>
                                                <td>{{ $customer->id }}</td>
                                                <td>{{ $customer->name }} {{ $customer->last_name }}</td>
                                                <td>
                                                    @php
                                                        $unpaidDebts = collect($customer->waterConnections)->flatMap(function ($waterConnection) {
                                                            return $waterConnection->debts->where('status', '!=', 'paid');
                                                        });
                                                        $totalDebt = $unpaidDebts->sum('amount');
                                                        $totalPaid = $unpaidDebts->sum('debt_current');
                                                        $pendingBalance = $totalDebt - $totalPaid;
                                                    @endphp
                                                    ${{ number_format($pendingBalance, 2, '.', ',') }}
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Opciones">
                                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles"
                                                            data-target="#viewDebts{{ $customer->id }}"> <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @include('debts.showDebts', ['debt' => (object)['waterConnection' => (object)['customer' => $customer]]])
                                        @empty
                                            <tr>
                                                <td colspan="4">No hay deudas registradas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center">
                                    {!! $debts->links('pagination::bootstrap-4') !!}
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
@section('css')
<style>
    @media (min-width: 992px) {
        .w-lg-auto {
            width: auto !important;
        }
    }
</style>
@endsection
@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var modalId = "{{ session('modal_id') }}";
        if (modalId) {
            $('#' + modalId).modal('show');
        }
    });

    $(document).ready(function() {
        console.log('Debts JS cargado');
        var successMessage = "{{ session('success') }}";
        var errorMessage = "{{ session('error') }}";
        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: successMessage,
                confirmButtonText: 'Aceptar'
            });
        }
        
        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                confirmButtonText: 'Aceptar'
            });
        }
        
        function formatCurrency(amount) {
            return '$' + Number(amount || 0).toFixed(2);
        }

        function resetDebtDiscountFields() {
            $('#debtDiscountContainer').hide();
            $('#debt_has_discount').prop('checked', false);
            $('#debt_has_discount_hidden').val(0);
            $('#debt_discount_id').prop('disabled', true).val('').trigger('change');
            $('#debt_discount_percentage').val('');
            $('#debt_discount_amount').val('');
            $('#debt_amount_with_discount').val('');
            
            $('#debt_discount_percentage_hidden').val('');
            $('#debt_discount_amount_hidden').val('');
            $('#debt_final_amount_hidden').val('');
            $('#debt_discount_id_hidden').val('');
        }

        function calculateDebtDiscount() {
            if (!$('#debt_has_discount').is(':checked')) {
                return;
            }

            let option = $('#debt_discount_id option:selected');
            let percentage = parseFloat(option.data('percentage')) || 0;
            let original = parseFloat($('#debt_amount').val()) || 0;

            let discount = original * (percentage / 100);
            let finalAmount = original - discount;

            $('#debt_discount_percentage').val(percentage.toFixed(2));
            $('#debt_discount_amount').val(formatCurrency(discount));
            $('#debt_amount_with_discount').val(formatCurrency(finalAmount));

            $('#debt_discount_percentage_hidden').val(percentage);
            $('#debt_discount_amount_hidden').val(discount.toFixed(2));
            $('#debt_final_amount_hidden').val(finalAmount.toFixed(2));
            $('#debt_discount_id_hidden').val($('#debt_discount_id').val() || '');
        }

        $('#createDebt').on('shown.bs.modal', function() {
            const $modal = $(this);

            setTimeout(function() {
                $modal.find('.select2').select2({
                    dropdownParent: $modal.find('.modal-content')
                });
            }, 0);

            resetDebtDiscountFields();
        });

        $('#debt_has_discount').on('change', function() {
            let checked = $(this).is(':checked');

            $('#debt_has_discount_hidden').val(checked ? 1 : 0);
            $('#debtDiscountContainer')[checked ? 'slideDown' : 'slideUp']();
            $('#debt_discount_id').prop('disabled', !checked).prop('required', checked);

            if (!checked) {
                $('#debt_discount_id').val('').trigger('change');
                $('#debt_discount_id_hidden').val('');

                $('#debt_discount_percentage').val('');
                $('#debt_discount_amount').val('');
                $('#debt_amount_with_discount').val('');

                $('#debt_discount_percentage_hidden').val('');
                $('#debt_discount_amount_hidden').val('');
                $('#debt_final_amount_hidden').val('');
            }

            calculateDebtDiscount();
        });

        $('#debt_discount_id').on('change', function() {
            calculateDebtDiscount();
        });

        $('#debt_amount').on('input', function() {
            calculateDebtDiscount();
        });

        $('#customer_id').on('change', function() {
            var customerId = $(this).val();
            if (customerId) {
                $.ajax({
                    url: "{{ route('getWaterConnections') }}",
                    type: "GET",
                    data: { customer_id: customerId },
                    success: function(response) {
                        var waterConnectionSelect = $('#water_connection_id');
                        waterConnectionSelect.empty();
                        waterConnectionSelect.append('<option value="">Selecciona una toma</option>');
                        $.each(response.waterConnections, function(index, waterConnection) {
                            waterConnectionSelect.append(
                                '<option value="' + waterConnection.id + '">' + waterConnection.id + ' - ' + waterConnection.name + '</option>'
                            );
                        });
                        waterConnectionSelect.trigger('change');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron cargar las tomas de agua para el cliente seleccionado.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            } else {
                $('#water_connection_id').empty().append('<option value="">Selecciona una toma</option>');
            }
        });
    });
</script>
@endsection
