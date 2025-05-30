@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Pagos adelantados')

@section('content')
    <h2>Pagos adelantados</h2>
    
    @include('advancePayments.advancePaymentsReportForm')

    <div class="row">
        <div class="col-lg-12 text-right">
            <div class="btn-group" role="group" aria-label="Acciones de gráfica de pagos">
                <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#paymentChart">
                    <i class="fa fa-money-bill"></i> Gráfica de pagos
                </button>
                <button type="button" class="btn bg-teal mr-2" data-toggle="modal"
                    data-target="#generateAdvancePaymentsReportModal">
                    <i class="fas fa-fw fa-calendar-plus"></i> Pagos Adelantados
                </button>
                <button class="btn btn-secondary mr-2" data-toggle="modal" data-target="#paymentChart">
                    <i class="fa fa-dollar-sign"></i> Comprobante de pagos
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mt-3">
        <form method="GET" action="" class="my-3">
            <div class="input-group">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Buscar por nombre o apellido"
                    value="{{ request('search') }}"
                >
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $('#generateAdvancePaymentsReportModal').on('shown.bs.modal', function () {
            $('.select2').select2({
                dropdownParent: $('#generateAdvancePaymentsReportModal')
            });
        });

        $('#advancePaymentsCustomerSelect').on('change', function () {
            const customerId = $(this).val();
            const waterConnectionSelect = $('#advancePaymentsWaterConnectionSelect');

            waterConnectionSelect.empty().append('<option value="">Selecciona una toma</option>');

            if (customerId) {
                $.ajax({
                    url: '{{ route('getWaterConnectionsByCustomer') }}',
                    method: 'GET',
                    data: {
                        waterCustomerId: customerId
                    },
                    success: function (response) {
                        $.each(response.waterConnections, function (index, connection) {
                            waterConnectionSelect.append(
                                `<option value="${connection.id}">${connection.id} - ${connection.name}</option>`
                            );
                        });
                    },
                    error: function (xhr) {
                        console.error('Error al cargar tomas de agua', xhr.responseText);
                    }
                });
            }
        });
    </script>
@endsection
