@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Tomas')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Tomas</h2>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button class="btn btn-success" data-toggle='modal' data-target="#createWaterConnections">
                                <i class="fa fa-plus"></i> Registrar Toma
                            </button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <form method="GET" action="{{ route('waterConnections.index') }}" class="my-3">
                    <div class="input-group w-50">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por ID, Nombre, Propietario" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="waterConnections" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
                                            <th>PROPIETARIO</th>
                                            <th>COSTO</th>
                                            <th>TIPO</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($connections) <= 0)
                                            <tr>
                                                <td colspan="8">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach($connections as $connection)
                                                <tr>
                                                    <td scope="row">{{ $connection->id }}</td>
                                                    <td>{{ $connection->name }}</td>
                                                    <td>{{ $connection->customer->name }} {{ $connection->customer->last_name }}</td>
                                                    <td>${{ $connection->cost->price }}</td>
                                                    @if ($connection->type === 'residencial')
                                                        <td>Residencial</td>
                                                    @elseif ($connection->type === 'commercial')
                                                        <td>Comercial</td>
                                                    @endif
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                            @can('viewWaterConnection')
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $connection->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @endcan

                                                            @can('editWaterConnection')
                                                            <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{ $connection->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @endcan

                                                            @can('deleteWaterConnection')
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $connection->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            @endcan

                                                            <button type="button" class="btn {{ $connection->cancelDescription ? 'btn-secondary' : 'btn-danger' }} mr-2" data-toggle="modal" title="{{ $connection->cancelDescription ? 'Toma ya cancelada' : 'Cancelar Toma' }}"
                                                                data-target="#cancel{{ $connection->id }}" {{ $connection->cancelDescription ? 'disabled' : '' }}>
                                                                <i class="fas fa-times-circle"></i>
                                                            </button>

                                                            @if($connection->cancelDescription)
                                                                <button type="button" class="btn btn-info" data-toggle="modal" title="Ver motivo de cancelación" data-target="#viewCancellationReason{{ $connection->id }}">
                                                                <i class="fas fa-info-circle"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>

                                                @include('waterConnections.show')
                                                @include('waterConnections.edit')
                                                @include('waterConnections.delete')

                                                <div class="modal fade" id="cancel{{ $connection->id }}" tabindex="-1" role="dialog" aria-labelledby="cancelLabel{{ $connection->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <form action="{{ route('waterConnections.cancel', $connection->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="cancelLabel{{ $connection->id }}">Cancelar Toma</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <label for="cancelDescription{{ $connection->id }}">Motivo de cancelación</label>
                                                                    <textarea id="cancelDescription{{ $connection->id }}" name="cancelDescription" class="form-control" required></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-danger">Cancelar Toma</button>
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                                @if($connection->cancelDescription)
                                                <div class="modal fade" id="viewCancellationReason{{ $connection->id }}" tabindex="-1" role="dialog" aria-labelledby="viewCancellationReasonLabel{{ $connection->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="viewCancellationReasonLabel{{ $connection->id }}">Motivo de Cancelación</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>{{ $connection->cancelDescription }}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('waterConnections.create')
                                <div class="d-flex justify-content-center">
                                    {!! $connections->links('pagination::bootstrap-4') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(session('debtError'))
<div class="modal fade" id="debtErrorModal" tabindex="-1" role="dialog" aria-labelledby="debtErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="debtErrorModalLabel">No se puede cancelar</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                La toma <strong>{{ session('connectionName') }}</strong> tiene deudas activas. Debe saldarlas antes de cancelarla.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#waterConnections').DataTable({
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

    $('#createWaterConnections').on('shown.bs.modal', function() {
        $('.select2').select2({
            dropdownParent: $('#createWaterConnections')
        });
    });

    $('[id^="edit"]').on('shown.bs.modal', function() {
        $(this).find('.select2').select2({
            dropdownParent: $(this)
        });
    });

    @if(session('debtError'))
        $('#debtErrorModal').modal('show');
    @endif
</script>
@endsection
