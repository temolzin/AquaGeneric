@extends('adminlte::page')

@section('title')

@section('content_header')
    <h1></h1>
@stop

@section('content')
    <div class="card card-warning" style="max-width: 800px; margin: auto;">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="card-title">Asignar Rol</h4>
                <a href="{{ route('users.index') }}" class="close text-white" aria-label="Close">&times;</a>
            </div>
        </div>
        <div class="card-body">
            <p class="h5">Nombre</p>
            <p class="form-control">{{ $user->name }} {{ $user->last_name }}</p>
            <h2 class="h5">Lista de roles</h2>
            <form action="{{ route('users.updateRole', $user) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="header-row">
                                <th class="select-column">Seleccionar</th>
                                <th>Permiso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td class="select-column">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]"
                                                value="{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>{{ $role->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-warning-custom">Asignar rol</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/roles/assingRol.css') }}">
@stop

@section('js')

@stop
