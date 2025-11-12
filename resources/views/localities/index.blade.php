@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Localidades')

@section('content')
@php
    use App\Models\Locality;
@endphp
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
                                            <th>SUSCRIPCIÓN</th>
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
                                            <td class="text-left align-center">
                                                <span class="badge 
                                                    {{ $locality->getSubscriptionStatus() === Locality::SUBSCRIPTION_ACTIVE ? 'badge-success' : 
                                                    ($locality->getSubscriptionStatus() === Locality::SUBSCRIPTION_EXPIRED ? 'badge-danger' : 'badge-secondary') }}">
                                                    {{ $locality->getSubscriptionStatus() }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Opciones">
                                                    @can('viewLocality')
                                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{$locality->id}}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    @endcan
                                                    @can('editLocality')
                                                        <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{$locality->id}}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-primary mr-2" data-toggle="modal" title="Actualizar Imagen" data-target="#editLogo{{$locality->id}}">
                                                            <i class="fas fa-image"></i>
                                                        </button>
                                                    @endcan
                                                    <button type="button" class="btn bg-purple mr-2" data-toggle="modal"  title="Configurar correo" data-target="#mailConfigModal{{$locality->id}}">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                    <button type="button" class="btn bg-navy mr-2" data-toggle="modal" title="Fondo de reporte" data-target="#editPdfBackground{{$locality->id}}">
                                                            <i class="fas fa-fill-drip"></i>
                                                    </button>
                                                    @can('deleteLocality')
                                                        @if($locality->hasDependencies())
                                                            <button type="button" class="btn btn-secondary mr-2" data-toggle="modal" title="Eliminación no permitida: Existen datos relacionados con este registro." disabled>
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{$locality->id}}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                    <form action="{{ route('localities.generateToken') }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="idLocality" value="{{ $locality->id }}">
                                                        <button type="button" class="btn" title="Generar token" style="background-color: #fd7e14; color: white;"
                                                            data-toggle="modal" data-target="#generateTokenModal{{ $locality->id }}">
                                                            <i class="fas fa-key"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn bg-maroon ml-2" title="Historial de Movimientos"
                                                            data-toggle="modal" data-target="#historyModal{{ $locality->id }}">
                                                            <i class="fas fa-history"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            @include('localities.edit')
                                            @include('localities.delete')
                                            @include('localities.show')
                                            @include('localities.editLogo')
                                            @include('localities.mailConfiguration')
                                            @include('localities.editPdfBackground')
                                            @include('localities.tokenModal')
                                            @include('localities.historyModal')
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('localities.create')
                                <div class="d-flex justify-content-center">
                                    {!! $localities->links('pagination::bootstrap-4') !!}
                                </div>
                                @if (session('createdToken') && session('localityName'))
                                    @include('localities.generatedTokenModal', [
                                        'token' => session('createdToken'),
                                        'localityName' => session('localityName')
                                    ])
                                @endif
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

        @if (session('createdToken'))
        $('#generatedTokenModal').modal('show');
        @endif
    });
</script>
@endsection
