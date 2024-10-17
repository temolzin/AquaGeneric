@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Perfil')

@section('content_header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="col-sm-6">
                <h1>Perfil</h1>
            </div>
        </div>
    </section>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Foto de perfil</h3>
                        </div>
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <div class="profile-pic-container" style="position: relative; display: inline-block;">
                                    @if ($authUser->getFirstMediaUrl('userGallery'))
                                        <img class="profile-user-img" style="width: 150px; height: 150px; border-radius: 50%;" src="{{$authUser->getFirstMediaUrl('userGallery') }}" alt="Foto de {{ $authUser->name }}">
                                    @else
                                        <img class="profile-user-img" style="width: 150px; height: 150px; border-radius: 50%;" src="{{ asset('img/userDefault.png') }}">
                                    @endif
                                    <button href="#" class="btn btn-outline-primary btn-sm edit-profile-pic" data-toggle="modal" data-target="#updateImage"
                                        style="position: absolute; bottom: 5px; right: 5px; background: white; border-radius: 10%; padding: 5px;">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </div>
                            </div>
                            <h3 class="profile-username text-center">{{ $authUser->name }} {{ $authUser->last_name }}</h3>
                            <p class="text-muted text-center">{{ $authUser->roles->first()->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Editar perfil</h3>
                        </div>
                        <div class="card-body" id="userUpdateInformation" name="userUpdateInformation" style="display: none">
                            <form id="updateForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                                @csrf
                                @method('PUT')
                                <div class="form-group row">
                                    <label for="nameUpdate" class="col-sm-2 col-form-label">
                                        Nombre
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" id="nameUpdate" name="nameUpdate" class="form-control" placeholder="Nombre" value="{{ $authUser->name }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="lastNameUpdate" class="col-sm-2 col-form-label">
                                        Apellidos
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" id="lastNameUpdate" name="lastNameUpdate" class="form-control" placeholder="Apellidos" value="{{ $authUser->last_name }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="phoneUpdate" class="col-sm-2 col-form-label">
                                        Teléfono
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="tel" pattern="^\d{10}$" id="phoneUpdate" name="phoneUpdate"  class="form-control" placeholder="Teléfono" title="Debe contener exactamente 10 dígitos" value="{{ $authUser->phone }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="emailUpdate" class="col-sm-2 col-form-label">
                                        Correo
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="email" id="emailUpdate" name="emailUpdate" class="form-control" placeholder="Correo" value="{{ $authUser->email }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success">
                                                Actualizar
                                            </button>
                                            <button type="button" class="btn btn-secondary" id="cancelUpdate">
                                                Cancelar
                                            </button>
                                        </div>   
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body" id="userInformation" name="userInformation">
                            <div class="form-group row">
                                <label for="name" class="col-sm-2 col-form-label">
                                    Nombre
                                </label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $authUser->name }}</p>
                                </div>
                                <label for="lastName" class="col-sm-2 col-form-label">
                                    Apellidos
                                </label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $authUser->last_name }}</p>
                                </div>
                                <label for="phone" class="col-sm-2 col-form-label">
                                    Telefono
                                </label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $authUser->phone }}</p>
                                </div>
                                <label for="email" class="col-sm-2 col-form-label">
                                    Correo
                                </label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $authUser->email }}</p>
                                </div>
                                <div class="offset-sm-2 col-sm-10">
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success" id="updateInformation" name="updateInformation">
                                            Editar datos
                                        </button>
                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editPassword">
                                            Cambiar contraseña
                                        </button>
                                    </div>   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('profile.editImage')
    @include('profile.editPassword')
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#updateInformation').click(function() {
                $('#userInformation').toggle();
                $('#userUpdateInformation').toggle();
            });

            $('#cancelUpdate').click(function() {
                $('#userUpdateInformation').hide();
                $('#userInformation').show();
                $('#updateForm')[0].reset();
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
