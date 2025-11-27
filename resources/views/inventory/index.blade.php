@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Inventario')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Inventario de Componentes</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                                <form method="GET" action="{{ route('inventory.index') }}" class="flex-grow-1 mt-2" style="min-width: 328px; max-width: 40%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por ID, Nombre, Categoría, Material" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Componente">
                                                <i class="fas fa-search d-lg-none"></i>
                                                <span class="d-none d-lg-inline">Buscar</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="d-flex flex-wrap justify-content-end gap-2 w-100 w-md-auto">
                                    <button class="btn btn-success flex-grow-1 flex-md-grow-0 mr-1 mt-2" data-toggle="modal" data-target="#createInventory" title="Registrar Componente">
                                        <i class="fa fa-plus"></i>
                                        <span class="d-none d-md-inline">Registrar Componente</span>
                                        <span class="d-inline d-md-none">Nuevo Componente</span>
                                    </button>
                                    <a class="btn btn-secondary flex-grow-1 flex-md-grow-0 ml-1 mt-2" target="_blank" 
                                    href="{{ route('inventory.pdfInventory', ['search' => request()->query('search')]) }}" 
                                    title="Generar Lista">
                                        <i class="fas fa-file-pdf"></i> Generar Lista
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
                                <table id="inventory" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
                                            <th>CANTIDAD</th>
                                            <th>CATEGORÍA</th>
                                            <th>MATERIAL</th>
                                            <th>DIMENSIONES</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($components) <= 0)
                                            <tr>
                                                <td colspan="7">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach($components as $component)
                                                <tr>
                                                    <td scope="row">{{ $component->id }}</td>
                                                    <td>{{ $component->name }}</td>
                                                    <td>{{ $component->amount }}</td>
                                                    <td>
                                                        @if($component->category)
                                                            <span class="badge {{ $component->category->color ?? 'bg-secondary' }} text-white" style="color: #fff !important;">
                                                                {{ $component->category->name }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary color-badge">Sin categoría</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $component->material ?? 'N/A' }}</td>
                                                    <td>{{ $component->dimensions ?? 'N/A' }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $component->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @can('updateInventory')
                                                                <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{ $component->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @endcan
                                                            @can('deleteInventory')
                                                                <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $component->id }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('inventory.show', ['component' => $component])
                                                @include('inventory.edit', ['component' => $component, 'localities' => $localities, 'users' => $users, 'categories' => $categories])
                                                @include('inventory.delete', ['component' => $component])
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('inventory.create', ['localities' => $localities, 'users' => $users, 'categories' => $categories])
                                <div class="d-flex justify-content-center">
                                    {!! $components->links('pagination::bootstrap-4') !!}
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
        $('#inventory').DataTable({
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
    });

    $('#createInventory').on('shown.bs.modal', function() {
        $('.select2').select2({
            dropdownParent: $('#createInventory')
        });
    });

    $('[id^="edit"]').on('shown.bs.modal', function() {
        $(this).find('.select2').select2({
            dropdownParent: $(this)
        });
    });

    $(document).on('shown.bs.modal', '.modal', function() {
        $(this).find('.select2').select2({
            dropdownParent: $(this)
        });
    });
</script>
@endsection
