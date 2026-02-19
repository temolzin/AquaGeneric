@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Tipos de Gasto')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Tipos de Gasto</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-column flex-lg-row justify-content-lg-end align-items-lg-center gap-3">
                                <button type="button" class="btn btn-success mt-2"
                                        data-toggle="modal" data-target="#createExpenseTypeModal" title="Registrar Tipo de Gasto">
                                    <i class="fa fa-plus"></i>
                                    <span class="d-none d-md-inline">Registrar Tipo</span>
                                    <span class="d-inline d-md-none">Registrar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="expenseTypes" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>TIPO DE GASTO</th>
                                            <th>DESCRIPCIÓN</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($expenseTypes as $expenseType)
                                            <tr>
                                                <td>{{ $expenseType->id }}</td>
                                                <td>
                                                    <span class="badge {{ $expenseType->color ?? 'bg-secondary' }} text-white" style="color: #fff !important;">
                                                        {{ $expenseType->name }}
                                                    </span>
                                                </td>
                                                <td>{{ $expenseType->description }}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Opciones">
                                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#viewExpenseType{{ $expenseType->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if (!is_null($expenseType->locality_id))
                                                            <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Registro" data-target="#editExpenseType{{ $expenseType->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger mr-2" title="Eliminar Registro" 
                                                            data-toggle="modal" data-target="#deleteExpenseType{{ $expenseType->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>

                                            @include('expenseTypes.show')
                                            @include('expenseTypes.edit')
                                            @include('expenseTypes.delete')
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay tipos de gasto registrados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @include('expenseTypes.create')
                                <div class="d-flex justify-content-center">
                                    {!! $expenseTypes->links('pagination::bootstrap-4') !!}
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
    .color-badge {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .color-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .table-dark .color-badge {
        border: 1px solid rgba(255,255,255,0.1);
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#expenseTypes').DataTable({
            responsive: true,
            buttons: ['csv', 'excel', 'print'],
            dom: 'Bfrtip',
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

        $('#createExpenseTypeModal').on('shown.bs.modal', function() {
            $('.select2').select2({
                dropdownParent: $('#createExpenseTypeModal')
            });
        });

        $('[id^="editExpenseType"]').on('shown.bs.modal', function() {
            $('.select2').select2({
                dropdownParent: $(this)
            });
        });
    });
</script>
@endsection
