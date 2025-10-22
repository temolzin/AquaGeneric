@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Costos')

@section('content')
<section class="content">
    <div class="right_col" cost="main">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Costos</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <button type="button" class="btn btn-success flex-grow-1 flex-md-grow-0 mr-1 mt-1" data-toggle="modal"
                                    data-target="#create" title="Registrar Costo">
                                    <i class="fa fa-plus"></i>
                                    <span class="d-none d-md-inline">Registrar Costo</span>
                                    <span class="d-inline d-md-none">Registrar Costo</span>
                                </button>
                                <a type="button" class="btn btn-secondary flex-grow-1 flex-md-grow-0 ml-1 mt-1" target="_blank"
                                    title="Generar Lista" href="{{ route('report.generateCostListReport') }}">
                                    <i class="fas fa-file-pdf"></i>
                                    <span class="d-none d-md-inline">Generar Lista</span>
                                    <span class="d-inline d-md-none">Generar Lista</span>
                                </a>
                            </div>
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
                                                    <td>${{ number_format($cost->price, 2) }}</td>
                                                    <td>
                                                        <div class="btn-group" cost="group" aria-label="Opciones">
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $cost->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @can('editCost')
                                                            @if ($cost->locality_id != 0)
                                                                <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Registro" data-target="#edit{{ $cost->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @endif
                                                            @endcan
                                                            @can('deleteCost')
                                                            @if ($cost->locality_id != 0)
                                                                <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $cost->id }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endif
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
                                <div class="d-flex justify-content-center">
                                    {!! $costs->links('pagination::bootstrap-4') !!}
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
