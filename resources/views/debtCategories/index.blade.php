@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Categorías de Deudas')

@section('content')
<section class="content">
    <div class="right_col" cost="main">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Categorías de Deudas</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-lg-flex justify-content-between align-items-center flex-wrap">
                                <form method="GET" action="{{ route('debtCategories.index') }}" class="mb-3 mb-lg-3" style="min-width: 300px;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por Nombre, Descripción..." value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Categorías">
                                                <i class="fa fa-search"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                    <button type="button" class="btn btn-success flex-grow-1 flex-md-grow-0 mt-2 mr-1"
                                            data-toggle="modal" data-target="#create" title="Registrar Categoría">
                                        <i class="fa fa-plus"></i>
                                        <span class="d-none d-md-inline">Registrar Categoría</span>
                                    </button>
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
                                            <th>NOMBRE</th>
                                            <th>DESCRIPCIÓN</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($categories) <= 0)
                                            <tr>
                                                <td colspan="4">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach($categories as $category)
                                                <tr>
                                                    <td>{{ $category->id }}</td>
                                                    <td>
                                                        <span class="badge {{ $category->color ?? 'bg-secondary' }} text-white" style="color: #fff !important;">
                                                            {{ $category->name }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $category->description }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $category->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if (!is_null($category->locality_id))
                                                                <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Registro" data-target="#edit{{ $category->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    class="btn {{ $category->hasDependencies() ? 'btn-secondary' : 'btn-danger' }} mr-2"
                                                                    title="{{ $category->hasDependencies() ? 'Eliminación no permitida: Existen deudas asociadas a esta categoría.' : 'Eliminar Registro' }}"
                                                                    {{ $category->hasDependencies() ? 'disabled' : 'data-toggle=modal data-target=#delete' . $category->id }}>
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('debtCategories.edit')
                                                @include('debtCategories.delete')
                                                @include('debtCategories.show')
                                            @endforeach
                                        @endif
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
        $('[id^="edit"]').on('shown.bs.modal', function() {
            reinitializeSelect2();
        });
    });
</script>
@endsection
