@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Incidencias')

@section('content')
    <section class="content">
        <div class="right_col" incident="main">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Incidencias</h2>
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-success mr-2" data-toggle='modal' data-target="#createIncidence">
                                                <i class="fa fa-plus"></i> Registrar Incidencia
                                            </button>
                                            <button class="btn btn-warning mr-2" data-toggle='modal' data-target="#createResponsible">
                                                <i class="fa fa-plus"></i> Cambio de Estatus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table class="table table-striped display responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>NOMBRE</th>
                                                <th>FECHA DE INICIO</th>
                                                <th>DESCRIPCIÓN</th>
                                                <th>CATEGORIA</th>
                                                <th>ESTADO</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($incidents) <= 0)
                                                <tr>
                                                    <td colspan="7">No hay incidentes registrados.</td>
                                                </tr>
                                            @else
                                                @foreach ($incidents as $incident)
                                                    <tr>
                                                        <td>{{ $incident->id }}</td>
                                                        <td>{{ $incident->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($incident->start_date)->translatedFormat('d/F/Y') }}</td>
                                                        <td>{{ $incident->description }}</td>
                                                        <td>
                                                            {{$incident->incidentCategory->name}}
                                                        </td>
                                                        <td>
                                                            {{$incident->status}}
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{$incident->id}}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{$incident->id}}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn bg-purple mr-2" data-toggle="modal" title="Ver Tomas de Agua" data-target="#delete{{$incident->id}}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                        @include('incidents.edit')
                                                        @include('incidents.delete')
                                                        @include('incidents.show')
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    @include('incidents.changeStatusModal')
                                    <div class="d-flex justify-content-center">
                                        {!! $incidents->links('pagination::bootstrap-4') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('incidents.create')

@endsection

@section('js')
<script>
    $(document).ready(function() {
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

    $('#createIncidence').on('shown.bs.modal', function () {
        $(this).find('.select2').select2({
            dropdownParent: $('#createIncidence')
        });
    });

    $('[id^="edit"]').on('shown.bs.modal', function () {
        $(this).find('.select2').select2({
            dropdownParent: $(this)
        });
    });

    $('#createResponsible').on('shown.bs.modal', function () {
        $(this).find('.select2').select2({
            dropdownParent: $('#createResponsible'),
        });
    });
</script>
@endsection
