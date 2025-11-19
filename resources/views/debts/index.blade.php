@extends('adminlte::page')

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
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center">
                                <form method="GET" action="{{ route('debts.index') }}" class="flex-grow-1" style="min-width: 330px; max-width: 50%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control mt-2" placeholder="Buscar por nombre, apellido o ID" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary mt-2" title="Buscar Deuda">Buscar</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="d-flex flex-wrap justify-content-end gap-2">
                                    <button type="button" class="btn btn-primary flex-grow-1 flex-lg-grow-0 mr-1 mt-2" data-toggle="modal"
                                            title="Asignar Deuda a Todos" data-target="#assignDebtModal">
                                        <i class="fa fa-plus"></i>
                                        <span class="d-none d-lg-inline">Asignar Deuda a Todos</span>
                                        <span class="d-inline d-lg-none">Asignar a Todos</span>
                                    </button>
                                    <button type="button" class="btn btn-success flex-grow-1 flex-lg-grow-0 ml-1 mt-2" data-toggle="modal"
                                            title="Crear Deuda" data-target="#createDebt">
                                        <i class="fa fa-plus"></i>
                                        <span class="d-none d-lg-inline">Crear Deuda</span>
                                        <span class="d-inline d-lg-none">Crear Deuda</span>
                                    </button>
                                    <a type="button" class="btn btn-secondary flex-grow-1 flex-lg-grow-0 mt-2 ml-1" target="_blank"
                                    title="Clientes con deudas" href="{{ route('report.with-debts') }}">
                                        <i class="fas fa-file-pdf"></i>
                                        <span class="d-none d-lg-inline">Clientes con deudas</span>
                                        <span class="d-inline d-lg-none">Clientes con deudas</span>
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
                                        @php
                                            $shownCustomers = [];
                                        @endphp
                                        @forelse ($debts as $debt)
                                            @if (!in_array($debt->waterConnection->customer_id, $shownCustomers))
                                                <tr>
                                                    <td>{{ $debt->waterConnection->customer->id }}</td>
                                                    <td>{{ $debt->waterConnection->customer->name }} {{ $debt->waterConnection->customer->last_name }}</td>
                                                    <td>
                                                        @php
                                                            $unpaidDebts = collect($debt->waterConnection->customer->waterConnections)->flatMap(function ($waterConnection) {
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
                                                                data-target="#viewDebts{{ $debt->waterConnection->customer_id }}"> <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('debts.showDebts')
                                                @php
                                                    $shownCustomers[] = $debt->waterConnection->customer_id;
                                                @endphp
                                            @endif
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

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var modalId = "{{ session('modal_id') }}";
        if (modalId) {
            $('#' + modalId).modal('show');
        }
    });

    $(document).ready(function() {
        $('#debts').DataTable({
            responsive: true,
            paging: false,
            info: false,
            searching: false
        });

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

        $('#createDebt').on('shown.bs.modal', function() {
            $('.select2').select2({
                dropdownParent: $('#createDebt')
            });
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
