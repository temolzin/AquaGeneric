@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Localidades')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Localidades</h2>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <div class="btn-group" role="group" aria-label="Acciones de Usuario">
                                <button class="btn btn-success mr-2" data-toggle='modal' data-target="#createLocality">
                                    <i class="fa fa-plus"></i> Registrar Localidad
                                </button>
                                <a type="button" class="btn btn-secondary" target="_blank" title="Localities" href="#">
                                    <i class="fas fa-map"></i> Generar Lista
                                </a>
                            </div>
                        </div>
                    </div>                    
                    <div class="clearfix"></div>
                </div>
                <div class="col-lg-4">
                <form method="GET" action="{{ route('localities.index') }}" class="my-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por localidad, municipio, código postal" value="{{ request('search') }}">
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
                                <table id="localities" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>LOGO</th>
                                            <th>NOMBRE</th>
                                            <th>MUNICIPIO</th>
                                            <th>ESTADO</th>
                                            <th>C.P</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($localities) <= 0)
                                        <tr>
                                            <td colspan="7">No hay resultados</td>
                                        </tr>
                                        @else
                                        @foreach($localities as $locality)
                                        <tr>
                                            <td scope="row">{{$locality->id}}</td>
                                            <td>
                                                @if ($locality->getFirstMediaUrl('localityGallery'))
                                                <img src="{{$locality->getFirstMediaUrl('localityGallery') }}" alt="Foto de {{$locality->name}}"
                                                style="width: 50px; height: 50px; border-radius: 50%;">
                                            @else
                                                <img src="{{ asset('img/localityDefault.png') }}"
                                                style="width: 50px; height: 50px; border-radius: 50%;">
                                            @endif
                                            </td>
                                            <td>{{$locality->name}}</td>
                                            <td>{{$locality->municipality}}</td>
                                            <td>{{$locality->state}}</td>
                                            <td>{{$locality->zip_code}}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Opciones">
                                                    <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{$locality->id}}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{$locality->id}}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-primary mr-2" data-toggle="modal" title="Actualizar Imagen" data-target="#editLogo{{$locality->id}}">
                                                        <i class="fas fa-image"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{$locality->id}}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            @include('localities.edit')
                                            @include('localities.delete')
                                            @include('localities.show')
                                            @include('localities.editLogo')
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('localities.create')
                                <div class="d-flex justify-content-center">
                                    {!! $localities->links('pagination::bootstrap-4') !!}
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
        $('#localities').DataTable({
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
            window.location.href = "{{ route('localities.index') }}";
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            window.location.href = "{{ route('localities.index') }}";
        });
    }
    });
</script>
@endsection
