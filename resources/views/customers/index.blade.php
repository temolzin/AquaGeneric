@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Clientes')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title mb-3">
                    <h2>Clientes</h2>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-lg-flex justify-content-between align-items-center flex-wrap">
                                    <form method="GET" action="{{ route('customers.index') }}" class="mb-3 mb-lg-0" style="min-width: 300px;">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, apellido o email" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary" title="Buscar Cliente">Buscar</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="btn-group d-none d-md-flex" role="group" aria-label="Acciones de Cliente">
                                        <button class="btn btn-success mr-2" data-toggle='modal' data-target="#createCustomer" title="Registrar Cliente">
                                            <i class="fa fa-plus"></i> Registrar Cliente
                                        </button>
                                        <a type="button" class="btn btn-secondary me-2" target="_blank" title="Generar Lista" href="{{ route('customers.pdfCustomers', ['search' => request('search')]) }}">
                                            <i class="fas fa-file-pdf"></i> Generar Lista
                                        </a>
                                        <a type="button" class="btn btn-primary ms-2" style="margin-left: 10px;" target="_blank" title="Generar Lista Resumen" href="{{ route('customers.pdfCustomersSummary', ['search' => request('search')]) }}">
                                            <i class="fas fa-file-pdf"></i> Generar Lista Resumen
                                        </a>
                                    </div>
                                    <div class="d-md-none w-100">
                                        <div class="row g-2">
                                            <div class="col-6 pe-1">
                                                <button class="btn btn-success w-100 py-2" data-toggle='modal'
                                                        data-target="#createCustomer" title="Registrar Cliente">
                                                        <i class="fa fa-plus"></i> Registrar Cliente
                                                </button>
                                            </div>
                                            <div class="col-6 ps-1">
                                                <a type="button" class="btn btn-secondary w-100 py-2" target="_blank" title="Generar Lista" href="{{ route('customers.pdfCustomers', ['search' => request('search')]) }}">
                                                    <i class="fas fa-file-pdf"></i> Generar Lista
                                                </a>
                                            </div>
                                            <div class="col-6 ps-1 mt-2">
                                                <a type="button" class="btn btn-secondary w-100 py-2" target="_blank" title="Generar Lista Resumen" href="{{ route('customers.pdfCustomersSummary', ['search' => request('search')]) }}">
                                                    <i class="fas fa-file-pdf"></i> Generar Lista Resumen
                                                </a>
                                            </div>
                                        </div>
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
                                <table id="customers" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>FOTO</th>
                                            <th>NOMBRE</th>
                                            <th>DIRECCION</th>
                                            <th>ESTADO</th>
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
                                            <td>{{$customer->user->name ?? 'N/A'}} {{$customer->user->last_name ?? ''}}</td>
                                            <td>{{$customer->state}}, {{$customer->locality}}</td>
                                            <td>
                                                @switch($customer->status)
                                                    @case(0)
                                                        Fallecido
                                                        @break
                                                    @case(1)
                                                        Con Vida
                                                        @break
                                                    @default
                                                        Desconocido
                                                        @break
                                                @endswitch
                                            </td>
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
                                                    <button type="button" class="btn bg-purple mr-2" data-toggle="modal" title="Ver Tomas de Agua" data-target="#waterConnections{{$customer->id}}">
                                                        <i class="fas fa-fw fa-water"></i>
                                                    </button>
                                                    <button type="button" class="btn bg-blue mr-2" data-toggle="modal" title="Ver Deudas Por Toma de Agua" data-target="#showDebtsPerWaterConnection{{$customer->id}}">
                                                        <i class="fa fa-dollar-sign"></i>
                                                    </button>
                                                    @can('deleteCustomer')
                                                        @if($customer->hasDependencies())
                                                            <button type="button" class="btn btn-secondary mr-2" title="Eliminación no permitida: Existen datos relacionados con este registro." disabled>
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{$customer->id}}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                            @include('customers.edit')
                                            @include('customers.delete')
                                            @include('customers.show')
                                            @include('customers.waterConnections')
                                            @include('customers.showDebtsPerWaterConnection')
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
