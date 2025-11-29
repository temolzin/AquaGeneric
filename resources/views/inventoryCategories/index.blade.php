@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Categorías de Inventario')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Categorías de Inventario</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                @can('createInventoryCategories')
                                <button type="button" class="btn btn-success flex-grow-1 flex-md-grow-0 mt-2 mr-1"
                                        data-toggle="modal" data-target="#createInventoryCategoryModal" title="Registrar Categoría de Inventario">
                                    <i class="fa fa-plus"></i>
                                    <span class="d-none d-md-inline">Registrar Categoría</span>
                                    <span class="d-inline d-md-none">Categoría</span>
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="inventoryCategories" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>CATEGORÍA</th>
                                            <th>DESCRIPCIÓN</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($inventoryCategories as $category)
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
                                                        @can('viewInventoryCategories')
                                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#viewInventoryCategory{{ $category->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @endcan
                                                        @if (!is_null($category->locality_id))
                                                        @can('editInventoryCategories')
                                                        <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Registro" data-target="#editInventoryCategory{{ $category->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @endcan
                                                        @can('deleteInventoryCategories')
                                                        <button type="button" class="btn btn-danger mr-2" title="Eliminar Registro" 
                                                        data-toggle="modal" data-target="#deleteInventoryCategory{{ $category->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        @endcan
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @include('inventoryCategories.show')
                                            @include('inventoryCategories.edit')
                                            @include('inventoryCategories.delete')
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay categorías de inventario registradas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @include('inventoryCategories.create')
                                <div class="d-flex justify-content-center">
                                    {!! $inventoryCategories->links('pagination::bootstrap-4') !!}
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
        $('#inventoryCategories').DataTable({
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

        $('#createInventoryCategoryModal').on('shown.bs.modal', function() {
            $('.select2').select2({
                dropdownParent: $('#createInventoryCategoryModal')
            });
        });

        $('[id^="editInventoryCategory"]').on('shown.bs.modal', function() {
            $('.select2').select2({
                dropdownParent: $(this)
            });
        });
    });
</script>
@endsection
