@extends('adminlte::page')

@section('title', 'Costos')

@section('content')
<section class="content">
    <div class="right_col" cost="main">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Costos</h2>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#create">
                                <i class="fa fa-plus"></i> Registrar Costo
                            </button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="costs" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>CATEGORIA</th>
                                            <th>PRECIO</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($costs) <= 0)
                                            <tr>
                                                <td colspan="4">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach($costs as $cost)
                                                <tr>
                                                    <td>{{ $cost->id }}</td>
                                                    <td>{{ $cost->category }}</td>
                                                    <td>{{ $cost->price }}</td>
                                                    <td>
                                                        <div class="btn-group" cost="group" aria-label="Opciones">
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $cost->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @can('editCost')
                                                            <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Registro" data-target="#edit{{ $cost->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @endcan
                                                            @can('deleteCost')
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $cost->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('costs.edit')
                                                @include('costs.delete')
                                                @include('costs.show')
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('costs.create')
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
        $('#costs').DataTable({
            responsive: true,
            buttons: ['excel', 'pdf', 'print'],
            dom: 'Bfrtip',
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
                window.location.href = "{{ route('costs.index') }}";
            });
        }

        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                window.location.href = "{{ route('costs.index') }}";
            });
        }
    });
</script>
@endsection
