@extends('adminlte::page')

@section('title', 'Editar rol')

@section('content_header')
    <h1></h1>
@stop

@section('content')
<div class="card card-warning" style="max-width: 900px; margin: auto;">
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
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ $role->name }}"
                       placeholder="Ingresar nombre">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <h2 class="h5 my-3">Permisos</h2>
            <div id="accordionPermissions">
                @foreach ($permissions as $module => $perms)
                    <div class="card mb-2"
                         style="border-radius: 8px; border: 1px solid #e6e6e6;">
                        <div class="p-3"
                             data-toggle="collapse"
                             data-target="#module-{{ $module }}"
                             style="cursor: pointer;">
                            <strong style="color:#007bff;">
                                {{ $moduleNames[$module] ?? $module }}
                            </strong>
                        </div>
                        <div id="module-{{ $module }}" class="collapse show">
                            <div class="card-body p-0">
                                <table class="table table-bordered m-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">Seleccionar</th>
                                            <th>Permiso</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($perms as $permission)
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $permission->id }}"
                                                           {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                </td>
                                                <td>{{ $permission->description }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <button type="button"
                        class="btn btn-secondary mr-2"
                        id="close-form-button">
                    Cerrar
                </button>
                <button type="submit"
                        class="btn btn-warning">
                    Actualizar
                </button>
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
    document.getElementById('close-button').addEventListener('click', function() {
        window.location.href = "{{ route('roles.index') }}";
    });
    document.getElementById('close-form-button').addEventListener('click', function() {
        window.location.href = "{{ route('roles.index') }}";
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
