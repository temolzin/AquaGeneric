@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Usuarios</h2>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button class="btn btn-success" data-toggle='modal' data-target="#create">
                                <i class="fa fa-plus"></i> Registrar Usuario
                            </button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="users" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>FOTO</th>
                                            <th>NOMBRE</th>
                                            <th>TELEFONO</th>
                                            <th>EMAIL</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($users) <= 0)
                                            <tr>
                                                <td colspan="8">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach($users as $user)
                                                <tr>
                                                    <td scope="row">{{ $user->id }}</td>
                                                    <td>
                                                        @if ($user->getFirstMediaUrl('userGallery'))
                                                            <img src="{{ $user->getFirstMediaUrl('userGallery') }}"
                                                                alt="Foto de {{ $user->name }}"
                                                                style="width: 50px; height: 50px; border-radius: 50%;">
                                                        @else
                                                            <img src="{{ asset('img/userDefault.png') }}"
                                                                style="width: 50px; height: 50px; border-radius: 50%;">
                                                        @endif
                                                    </td>
                                                    <td>{{ $user->name }} {{ $user->last_name }}</td>
                                                    <td>{{ $user->phone }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $user->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{ $user->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $user->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            <a type="button" class="permiso btn btn-secondary mr-2" title="Asignar Rol" href="{{ route('users.edit', Crypt::encrypt($user->id)) }}">
                                                                <i class="fa fa-key"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('users.edit')
                                                @include('users.delete')
                                                @include('users.show')
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('users.create')
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
        $('#users').DataTable({
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
                window.location.href = "{{ route('users.index') }}";
            });
        }

        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                window.location.href = "{{ route('users.index') }}";
            });
        }
    });
</script>
@endsection
