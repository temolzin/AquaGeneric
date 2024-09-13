@extends('adminlte::page')

@section('title', 'Admin')

@section('content_header')
    <h1></h1>
@stop

@section('content')
    <div class="card card-warning" style="max-width: 800px; margin: auto;">
        <div class="card-header bg-warning text-white">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="card-title">Editar Rol</h4>
                <button type="button" class="close text-white" aria-label="Close" id="close-button">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" placeholder="Ingresar nombre">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <h2 class="h5">Permisos</h2>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="header-row">
                                <th class="select-column">Seleccionar</th>
                                <th>Permiso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td class="select-column">
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }} class="form-check-input">
                                        </div>
                                    </td>
                                    <td>{{ $permission->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="button-group">
                    <button type="button" class="btn btn-secondary" id="close-form-button">Cerrar</button>
                    <button type="submit" class="btn btn-warning-custom">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/roles/editRol.css') }}">
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('close-button').addEventListener('click', function() {
                window.location.href = "{{ route('roles.index') }}";
            });
            document.getElementById('close-form-button').addEventListener('click', function() {
                window.location.href = "{{ route('roles.index') }}";
            });
        });

        var errorMessage = "{{ session('error') }}";
            if (errorMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'Aceptar'
                });
            }
    </script>
@stop
