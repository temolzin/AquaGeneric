@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Empleados')

@section('content')
    <section class="content">
        <div class="right_col" role="main">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Empleados</h2>
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <div class="btn-group" role="group" aria-label="Acciones de Empleado">
                                    <button class="btn btn-success mr-2" data-toggle='modal' data-target="#createEmployee">
                                        <i class="fa fa-plus"></i> Registrar Empleado
                                    </button>
                                    <a type="button" class="btn btn-secondary" target="_blank" title="Employees"
                                        href="{{ route('report.generateEmployeeListPDF') }}">
                                        <i class="fas fa-users"></i> Generar Lista
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-lg-4">
                        <form method="GET" action="{{ route('employees.index') }}" class="my-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Buscar por nombre, apellido" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="employees" class="table table-striped display responsive nowrap"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>FOTO</th>
                                                <th>NOMBRE</th>
                                                <th>DIRECCION</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($employees) <= 0)
                                                <tr>
                                                    <td colspan="7">No hay resultados</td>
                                                </tr>
                                            @else
                                                @foreach($employees as $employee)
                                                    <tr>
                                                        <td scope="row">{{$employee->id}}</td>
                                                        <td>
                                                            @php
                                                                $photoUrl = $employee->getFirstMediaUrl('employeeGallery');
                                                            @endphp
                                                            @if ($photoUrl)
                                                                <img src="{{ $photoUrl }}" alt="Foto de {{ $employee->name }}"
                                                                    style="width: 50px; height: 50px; border-radius: 50%;">
                                                            @else
                                                                <img src="{{ asset('img/userDefault.png') }}" alt="Imagen por defecto"
                                                                    style="width: 50px; height: 50px; border-radius: 50%;">
                                                            @endif
                                                        </td>
                                                        <td>{{$employee->name}} {{$employee->last_name}}</td>
                                                        <td>{{$employee->state}}, {{$employee->locality}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group" aria-label="Opciones">
                                                                <button type="button" class="btn btn-info mr-2" data-toggle="modal"
                                                                    title="Ver Detalles" data-target="#view{{$employee->id}}">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-warning mr-2"
                                                                    data-toggle="modal" title="Editar Datos"
                                                                    data-target="#edit{{$employee->id}}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger mr-2"
                                                                    data-toggle="modal" title="Eliminar Registro"
                                                                    data-target="#delete{{$employee->id}}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                        @include('employees.edit')
                                                        @include('employees.delete')
                                                        @include('employees.show')
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    @include('employees.create')
                                    <div class="d-flex justify-content-center">
                                        {!! $employees->links('pagination::bootstrap-4') !!}
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
        $(document).ready(function () {
            $('#employees').DataTable({
                responsive: true,
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
    </script>
@endsection
