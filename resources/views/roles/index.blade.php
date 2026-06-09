@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Roles')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Roles</h2>
                    @include('roles.create')
                    <div class="clearfix"></div>
                </div>
                <div class="row align-items-center my-3">
                    <div class="col-md-6 col-lg-4">
                        <form id="formSearch" method="GET" action="{{ route('roles.index') }}" class="mb-2 mb-md-0">
                            <div class="input-group">
                                <input type="text" name="search" id="searchName" class="form-control" placeholder="Buscar por nombre" value="{{ request('search') ?? '' }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 col-lg-8 text-md-right">
                        <div class="btn-group" role="group" aria-label="Acciones de Rol">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createRoleModal" title="Registrar Rol">
                                <i class="fa fa-plus"></i> Registrar Rol
                            </button>
                        </div>
                    </div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="roles" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th class="text-center">NOMBRE</th>
                                            <th class="text-center">OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($roles) <= 0)
                                            <tr>
                                                <td colspan="3">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach ($roles as $role)
                                                <tr>
                                                    <td class="text-center">{{ $role->id }}</td>
                                                    <td class="text-center">{{ $role->name }}</td>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal"
                                                                    title="Ver Detalles" data-target="#view{{ $role->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <a type="button" class="btn btn-warning mr-2" title="Editar Datos"
                                                               href="{{ route('roles.edit', $role) }}">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal"
                                                                    title="Eliminar Registro" data-target="#delete{{ $role->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    @include('roles.delete')
                                                    @include('roles.show')
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
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
        $('#roles').DataTable({
            responsive: true,
            buttons: ['excel', 'pdf', 'print'],
            dom: 'Brtip',
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
</script>
@endsection
