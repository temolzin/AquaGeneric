@extends('adminlte::page')

@section('title', 'Roles')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Roles</h2>
                    <div class="row">
                        @include('roles.create')
                        <div class="col-lg-12 text-right">
                            <button type="button" class="btn btn-success" data-toggle="modal"
                                    data-target="#createRoleModal"><i class="fa fa-plus"></i> Registrar Rol
                            </button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="roles" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
                                            <th>OPCIONES</th>
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
                                                    <td>{{ $role->id }}</td>
                                                    <td>{{ $role->name }}</td>
                                                    <td>
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
            dom: 'Bfrtip',
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
                window.location.href = "{{ route('roles.index') }}";
            });
        }

        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                window.location.href = "{{ route('roles.index') }}";
            });
        }
    });
</script>
@endsection
