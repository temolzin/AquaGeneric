@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Usuarios</h2>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <div class="btn-group" role="group" aria-label="Acciones de Usuario">
                                <button class="btn btn-success mr-2" data-toggle='modal' data-target="#createCustomer">
                                    <i class="fa fa-plus"></i> Registrar Usuario
                                </button>
                                <a type="button" class="btn btn-secondary" target="_blank" title="Customers" href="{{ route('customers.pdfCustomers') }}">
                                    <i class="fas fa-users"></i> Generar Lista
                                </a>
                            </div>
                        </div>
                    </div>                    
                    <div class="clearfix"></div>
                </div>
                <div class="col-lg-4">
                <form method="GET" action="{{ route('customers.index') }}" class="my-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, apellido" value="{{ request('search') }}">
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
                                <table id="customers" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>FOTO</th>
                                            <th>NOMBRE</th>
                                            <th>TIENE TOMA</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($customers) <= 0)
                                        <tr>
                                            <td colspan="7">No hay resultados</td>
                                        </tr>
                                        @else
                                        @foreach($customers as $customer)
                                        <tr>
                                            <td scope="row">{{$customer->id}}</td>
                                            <td>
                                                @if ($customer->getFirstMediaUrl('customerGallery'))
                                                <img src="{{$customer->getFirstMediaUrl('customerGallery') }}" alt="Foto de {{$customer->name}}"
                                                    style="width: 50px; height: 50px; border-radius: 50%;">
                                            @else
                                                <img src="{{ asset('img/userDefault.png') }}"
                                                    style="width: 50px; height: 50px; border-radius: 50%;">
                                            @endif
                                            </td>
                                            <td>{{$customer->name}} {{$customer->last_name}}</td>
                                            <td>{{ $customer->has_water_connection ? 'SI' : 'NO' }}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Opciones">
                                                    <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{$customer->id}}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @can('editCustomer')
                                                    <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{$customer->id}}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    @endcan
                                                    @can('deleteCustomer')
                                                    <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{$customer->id}}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                    @endcan
                                                </div>
                                            </td>
                                            @include('customers.edit')
                                            @include('customers.delete')
                                            @include('customers.show')
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('customers.create')
                                <div class="d-flex justify-content-center">
                                    {!! $customers->links('pagination::bootstrap-4') !!}
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
        $('#customers').DataTable({
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
        }).then((result) => {
            window.location.href = "{{ route('customers.index') }}";
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            window.location.href = "{{ route('customers.index') }}";
        });
    }
    });
</script>
@endsection
