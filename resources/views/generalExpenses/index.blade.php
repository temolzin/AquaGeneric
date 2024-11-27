@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Gastos')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Gastos</h2>
                    <div class="row">
                        @include('generalExpenses.weeklyExpenses')
                        @include('generalExpenses.weeklyGains')
                        @include('generalExpenses.annualExpenses')
                        @include('generalExpenses.annualGains')
                        <div class="col-lg-12 text-right">
                            <button class="btn btn-success" data-toggle='modal' data-target="#createGeneralExpenses">
                                <i class="fa fa-plus"></i> Registrar Gasto
                            </button>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#annualExpenses">
                                <i class="fa fa-dollar-sign"></i> Egresos Anuales
                            </button>
                            <button type="button" class="btn bg-olive" data-toggle="modal" target="_blank"  data-target="#weeklyExpenses">
                                <i class="fa fa-dollar-sign"></i> Egresos Semanales
                            </button>
                            <button type="button" class="btn bg-navy" data-toggle="modal" target="_blank"  data-target="#weeklyGains">
                                <i class="fa fa-dollar-sign"></i> Ganancias Semanales
                            </button>
                            <button type="button" class="btn btn-primary" data-toggle="modal" target="_blank"  data-target="#annualGains">
                                <i class="fa fa-dollar-sign"></i> Ganancias Anuales
                            </button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <form method="GET" action="{{ route('generalExpenses.index') }}" class="my-3">
                    <div class="input-group w-50">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por ID o Concepto" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="generalExpenses" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>CONCEPTO</th>
                                            <th>TIPO</th>
                                            <th>COSTO</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($expenses) <= 0)
                                            <tr>
                                                <td colspan="8">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach($expenses as $expense)
                                                <tr>
                                                    <td scope="row">{{ $expense->id }}</td>
                                                    <td>{{ $expense->concept }}</td>
                                                    @switch($expense->type)
                                                        @case('mainteinence')
                                                        <td>Mantenimiento</td>
                                                            @break
                                                        @case('services')
                                                        <td>Servicios</td>
                                                            @break
                                                        @case('supplies')
                                                        <td>Insumos</td>
                                                            @break
                                                        @case('taxes')
                                                        <td>Impuestos</td>
                                                            @break
                                                        @case('staff')
                                                        <td>Personal</td>
                                                            @break
                                                    @endswitch
                                                    <td>${{ $expense->amount }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                            @can('viewGeneralExpense')
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $expense->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @endcan
                                                            @can('editGeneralExpense')
                                                            <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{ $expense->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @endcan
                                                            @can('deleteGeneralExpense')
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $expense->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('generalExpenses.show')
                                                @include('generalExpenses.edit')
                                                @include('generalExpenses.delete')
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('generalExpenses.create')
                                <div class="d-flex justify-content-center">
                                    {!! $expenses->links('pagination::bootstrap-4') !!}
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
    $(document).ready(function() {
        $('#generalExpenses').DataTable({
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
    });

    $('#createGeneralExpenses').on('shown.bs.modal', function() {
        $('.select2').select2({
                dropdownParent: $('#createGeneralExpenses')
        });
    });

    $('[id^="edit"]').on('shown.bs.modal', function() {
        $(this).find('.select2').select2({
            dropdownParent: $(this)
        });
    });
</script>
@endsection
