@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Membresías')

@section('content')
    <section class="content">
        <div class="right_col" role="main">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Membresías</h2>
                        <div class="row mb-2">
                            <div class="col-12">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                    <form method="GET" action="{{ route('memberships.index') }}" class="flex-grow-1 w-100" style="min-width: 300px; max-width: 100%;">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary" title="Buscar Membresía">
                                                    <i class="fas fa-search"></i>
                                                    <span class="d-none d-md-inline">Buscar</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="d-flex flex-wrap justify-content-end gap-2 w-100 w-md-auto">
                                        <button class="btn btn-success flex-grow-1 flex-md-grow-0 mr-1 mt-2" data-toggle='modal'
                                                data-target="#createMembership" title="Registrar Membresía">
                                            <i class="fa fa-plus"></i>
                                            <span class="d-none d-md-inline">Registrar Membresía</span>
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
                                    <table id="memberships" class="table table-striped display responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>NOMBRE</th>
                                                <th>PRECIO</th>
                                                <th>DURACIÓN</th>
                                                <th>TOMAS DE AGUA</th>
                                                <th>USUARIOS</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($memberships) <= 0)
                                                <tr>
                                                    <td colspan="7">No hay resultados</td>
                                                </tr>
                                            @else
                                                @foreach($memberships as $membership)
                                                    <tr>
                                                        <td scope="row">{{$membership->id}}</td>
                                                        <td>{{$membership->name}}</td>
                                                        <td>${{number_format($membership->price, 2)}}</td>
                                                        <td>{{$membership->term_months}} meses</td>
                                                        <td>{{number_format($membership->water_connections_number, 0)}}</td>
                                                        <td>{{number_format($membership->users_number, 0)}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group" aria-label="Opciones">
                                                                <button type="button" class="btn btn-info mr-2" data-toggle="modal"
                                                                    title="Ver Detalles" data-target="#view{{$membership->id}}">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @can('editMemberships')
                                                                <button type="button" class="btn btn-warning mr-2"
                                                                    data-toggle="modal" title="Editar Datos"
                                                                    data-target="#edit{{$membership->id}}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                @endcan
                                                                @can('deleteMemberships')
                                                                    @if($membership->hasDependencies())
                                                                        <button type="button" class="btn btn-secondary mr-2" 
                                                                                data-toggle="modal" title="Eliminación no permitida: Existen datos relacionados con este registro." disabled>
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    @else
                                                                        <button type="button" class="btn btn-danger mr-2"
                                                                                data-toggle="modal" title="Eliminar Registro"
                                                                                data-target="#delete{{$membership->id}}">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    @endif
                                                                @endcan
                                                            </div>
                                                        </td>
                                                        @include('memberships.edit')
                                                        @include('memberships.delete')
                                                        @include('memberships.show')
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    @include('memberships.create')
                                    <div class="d-flex justify-content-center">
                                        {!! $memberships->links('pagination::bootstrap-4') !!}
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
            $('#memberships').DataTable({
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
    </script>
@endsection
