@extends('adminlte::page')

@section('title', 'Deudas')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Deudas</h2>
                    <div class="row">
                        @include('debts.create')
                        @include('debts.periods')
                        <div class="col-lg-12 text-right">
                            <form action="{{ route('debts.assignAll') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#assignDebtModal">
                                    <i class="fa fa-plus"></i> Asignar Deuda a Todos
                                </button>
                            </form>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createDebt">
                                <i class="fa fa-plus"></i> Crear Deuda
                            </button>
                            <a type="button" class="btn btn-secondary" target="_blank" title="Clientes con deudas" href="{{ route('report.with-debts') }}">
                                <i class="fas fa-users"></i> Clientes con deudas
                            </a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-lg-4">
                        <form method="GET" action="{{ route('debts.index') }}" class="my-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, apellido o ID" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="debts" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>USUARIO</th>
                                            <th>TOTAL DE LA DEUDA</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($debts as $debt)
                                            <tr>
                                                <td>{{ $debt->customer->id }}</td>
                                                <td>{{ $debt->customer->name }} {{ $debt->customer->last_name }}</td>
                                                <td>{{ $debt->total_amount }}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Opciones">
                                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" 
                                                            data-target="#viewDebts{{ $debt->customer->id }}"> <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @include('debts.showDebts')
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
            }).then((result) => {
                window.location.href = "{{ route('debts.index') }}";
            });
        }

        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                window.location.href = "{{ route('debts.index') }}";
            });
        }
    });

    $('#createDebt').on('shown.bs.modal', function() {
        $('.select2').select2({
                dropdownParent: $('#createDebt')
        });
    });

    function closeCurrentModal(modalId) {
        $(modalId).modal('hide');
    }
</script>
@endsection
