@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Categorías de Deudas')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Categorías de Deudas</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-lg-flex justify-content-between align-items-center flex-wrap">
                                <form method="GET" action="{{ route('debtCategories.index') }}"
                                       class="mb-3 mb-lg-3 flex-grow-1" style="max-width: 400px;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                               placeholder="Buscar por Nombre, Descripción o ID"
                                               value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-end align-items-lg-center gap-3">
                                    @can('createDebtCategories')
                                    <button type="button" class="btn btn-success mt-2"
                                            data-toggle="modal" data-target="#createDebtCategoryModal"
                                            title="Registrar Categoría">
                                        <i class="fa fa-plus"></i>
                                        <span class="d-none d-md-inline">Registrar Categoría</span>
                                        <span class="d-inline d-md-none">Registrar</span>
                                    </button>
                                    @endcan
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
                                <table id="debtCategories" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>CATEGORÍA</th>
                                            <th>DESCRIPCIÓN</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($categories as $category)
                                            <tr>
                                                <td>{{ $category->id }}</td>
                                                <td>
                                                    <span class="badge {{ $category->color ?? 'bg-secondary' }} text-white" style="color: #fff !important;">
                                                        {{ $category->name }}
                                                    </span>
                                                </td>
                                                <td>{{ $category->description ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Opciones">
                                                        @can('viewDebtCategories')
                                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#viewDebtCategory{{ $category->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @endcan
                                                        @if (!is_null($category->locality_id))
                                                        @can('editDebtCategories')
                                                        <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Registro" data-target="#editDebtCategory{{ $category->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @endcan
                                                        @can('deleteDebtCategories')
                                                            @if ($category->hasDependencies())
                                                                <button type="button" class="btn btn-secondary mr-2" title="Eliminación no permitida: Existen deudas asociadas." disabled>
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @else
                                                                <button type="button" class="btn btn-danger mr-2" title="Eliminar Registro" data-toggle="modal" data-target="#deleteDebtCategory{{ $category->id }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endif
                                                        @endcan
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @include('debtCategories.show')
                                            @include('debtCategories.edit')
                                            @include('debtCategories.delete')
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    No hay categorías registradas.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @include('debtCategories.create')
                                <div class="d-flex justify-content-center">
                                    {!! $categories->links('pagination::bootstrap-4') !!}
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

        $('#debtCategories').DataTable({
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
                confirmButtonText: 'Aceptar',
                timer: 3000
            });
        }

        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                confirmButtonText: 'Aceptar',
                timer: 4000
            });
        }

        $('[id^="editDebtCategory"]').on('shown.bs.modal', function() {
            reinitializeSelect2();
        });

    });
</script>
@endsection
