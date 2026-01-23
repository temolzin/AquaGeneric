@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Ingresos')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Ingresos</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                                <form method="GET" action="{{ route('generalEarnings.index') }}" class="flex-grow-1 mt-2" style="min-width: 330px; max-width: 30%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por ID o Concepto" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Ingreso">
                                                <i class="fas fa-search"></i>
                                                <span class="d-none d-md-inline">Buscar</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <button class="btn btn-success flex-grow-1 flex-lg-grow-0 mt-2" data-toggle='modal' 
                                    data-target="#createGeneralEarnings" title="Registrar Ingreso">
                                    <i class="fa fa-plus"></i>
                                    <span class="d-none d-md-inline">Registrar Ingreso</span>
                                    <span class="d-inline d-md-none">Registrar Ingreso</span>
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
                                <table id="generalEarnings" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>CONCEPTO</th>
                                            <th>TIPO</th>
                                            <th>COSTO</th>
                                            <th>OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($earnings) <= 0)
                                            <tr>
                                                <td colspan="8">No hay resultados</td>
                                            </tr>
                                        @else
                                            @foreach($earnings as $earning)
                                                <tr>
                                                    <td scope="row">{{ $earning->id }}</td>
                                                    <td>{{ $earning->concept }}</td>
                                                    <td>
                                                        @if($earning->earningType)
                                                            <span class="badge {{ $earning->earningType->color ?? 'bg-secondary' }} text-white" style="color: #fff !important;">
                                                                {{ $earning->earningType->name }}
                                                            </span>
                                                        @else
                                                            <span class="badge color-badge" style="background-color: #6c757d; color: white;">
                                                                Sin tipo
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>${{ $earning->amount }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                            @can('viewGeneralEarning')
                                                            <button type="button" class="btn btn-info mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $earning->id }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @endcan
                                                            @can('editGeneralEarning')
                                                            <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Datos" data-target="#edit{{ $earning->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @endcan
                                                            @can('deleteGeneralEarning')
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $earning->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('generalEarnings.show')
                                                @include('generalEarnings.edit')
                                                @include('generalEarnings.delete')
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @include('generalEarnings.create')
                                <div class="d-flex justify-content-center">
                                    {!! $earnings->links('pagination::bootstrap-4') !!}
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

@section('css')
<style>
    .color-badge {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .color-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .table-dark .color-badge {
        border: 1px solid rgba(255,255,255,0.1);
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#generalEarnings').DataTable({
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

    $('#createGeneralEarnings').on('shown.bs.modal', function() {
        $('.select2').select2({
                dropdownParent: $('#createGeneralEarnings')
        });
    });

    $('[id^="edit"]').on('shown.bs.modal', function() {
        $(this).find('.select2').select2({
            dropdownParent: $(this)
        });
    });
</script>
@endsection
