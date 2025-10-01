@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Tomas')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Tomas</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                                <form method="GET" action="{{ route('waterConnections.index') }}" class="flex-grow-1 mt-2" style="min-width: 328px; max-width: 40%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Buscar por ID, Nombre, Propietario"
                                            value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Toma de Agua">
                                                <i class="fas fa-search d-lg-none"></i>
                                                <span class="d-none d-lg-inline">Buscar</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <button class="btn btn-success flex-grow-1 flex-lg-grow-0 mt-2"
                                        data-toggle='modal' data-target="#createWaterConnections"
                                        title="Registrar Toma">
                                    <i class="fa fa-plus"></i>
                                    <span class="d-none d-lg-inline">Registrar Toma</span>
                                    <span class="d-inline d-lg-none">Nueva Toma</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
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
                                                    <td>
                                                        @if ($connection->customer)
                                                            {{ $connection->customer->name }} {{ $connection->customer->last_name }}
                                                        @else
                                                            <span class="text-danger">Toma sin cliente asignado</span>
                                                        @endif
                                                    </td>
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

                                                            {{--
                                                            @can('deleteWaterConnection')
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $connection->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            @endcan
                                                            --}}

                                                            <button type="button" class="btn {{ $connection->cancelDescription ? 'btn-secondary' : 'btn-danger' }} mr-2" data-toggle="modal" title="{{ $connection->cancelDescription ? 'Toma ya cancelada' : 'Cancelar Toma' }}"
                                                                data-target="#cancel{{ $connection->id }}" {{ $connection->cancelDescription ? 'disabled' : '' }}>
                                                                <i class="fas fa-times-circle"></i>
                                                            </button>

                                                            @if($connection->cancelDescription)
                                                                <button type="button" class="btn btn-info" data-toggle="modal" title="Ver motivo de cancelación" data-target="#viewCancellationReason{{ $connection->id }}">
                                                                <i class="fas fa-info-circle"></i>
                                                                </button>

                                                                <button type="button" class="btn"  style="background-color: #0d6efd;  margin-left: 8px;" data-toggle="modal" title="Reactivar toma de agua" data-target="#ReactivateWaterService{{ $connection->id }}">
                                                                <i class="fas fa-sync-alt"></i>
                                                                </button>
                                                            @endif
                                                                <button type="button" class="btn btn-success mr-2 btn-generate-qr" data-id="{{ $connection->id }}" data-toggle="modal" data-target="#qrModal" title="Generar QR">
                                                                <i class="fas fa-qrcode"></i>
                                                                </button>
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
                                                                <div class="modal-header bg-danger text-white">
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
                                                            <div class="modal-header bg-info text-white">
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
                                                <div class="modal fade" id="ReactivateWaterService{{ $connection->id }}" tabindex="-1" role="dialog" aria-labelledby="cancelLabel{{ $connection->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <form action="{{ route('waterConnections.reactivate', $connection->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title" id="cancelLabel{{ $connection->id }}">Reactivar Toma de Agua</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <label for="cancelDescription{{ $connection->id }}">Cliente</label>
                                                                    <select class="form-control select2" name="customer_id" id="customer_id" required>
                                                                        <option value="">Selecciona un cliente</option>
                                                                        @foreach($customers as $customer)
                                                                            <option value="{{ $customer->id }}">
                                                                                {{ $customer->id }} - {{ $customer->name }} {{ $customer->last_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-success">Reactivar Toma</button>
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                                </div>
                                                            </div>
                                                        </form>
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
       $('#qrModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var modal = $(this);
        var img = modal.find('#qrImage');
        var downloadBtn = modal.find('#downloadQrBtn');
        
        img.attr('src', '').attr('alt', 'Cargando...');
        downloadBtn.attr('href', '#').hide();
        
        $.ajax({
            url: '/waterConnections/' + id + '/qr-generate',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    img.attr('src', response.image).attr('alt', 'Código QR');
                    downloadBtn.attr('href', response.download_url).show();
                } else {
                    img.attr('alt', 'Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', xhr.responseText);
                var errorMsg = 'Error al cargar el código QR';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                img.attr('alt', errorMsg);
                modal.find('.modal-body').prepend(
                    '<div class="alert alert-danger">' + errorMsg + '</div>'
                );
            }
        });
    });

        $('#waterConnections').DataTable({
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
    
    $(document).on('shown.bs.modal', '.modal', function () {
        $(this).find('.select2').select2({
            dropdownParent: $(this)
        });
    });
</script>

<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalLabel">Código QR</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img id="qrImage" src="" alt="Código QR" style="max-width: 100%; height: auto;">
      </div>
      <div class="modal-footer">
        <a id="downloadQrBtn" href="#" class="btn btn-success" download>Descargar QR</a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection
