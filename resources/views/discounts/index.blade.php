@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Descuentos')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Descuentos</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-lg-flex justify-content-between align-items-center flex-wrap">
                                <form method="GET" action="{{ route('discounts.index') }}" class="mb-3 mb-lg-0 mr-lg-3" style="min-width:380px;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por Nombre o Porcentaje" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Descuento">
                                                <i class="fa fa-search"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="btn-group d-none d-md-flex" role="group">
                                    @can('createDiscount')
                                    <button class="btn btn-success mr-2" data-toggle="modal" data-target="#create">
                                        <i class="fa fa-plus"></i> Registrar Descuento
                                    </button>
                                    @endcan
                                </div>
                                <div class="d-md-none w-100">
                                    @can('createDiscount')
                                    <button class="btn btn-success btn-block" data-toggle="modal" data-target="#create">
                                        <i class="fa fa-plus"></i> Registrar
                                    </button>
                                    @endcan
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
                                <table id="discounts" class="table table-striped display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>DESCUENTO</th>
                                            <th>DESCRIPCIÓN</th>
                                            <th>PORCENTAJE</th>
                                            <th class="not-export">OPCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($discounts as $discount)
                                            <tr>
                                                <td>{{ $discount->id }}</td>
                                                <td>
                                                    <span class="badge text-white" style="background-color: {{ $discount->color ?? '#6c757d' }};">
                                                        {{ $discount->name }}
                                                    </span>
                                                </td>
                                                <td>{{ $discount->description ?? 'Sin descripción' }}</td>
                                                <td>{{ number_format($discount->percentage,2) }}%</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Opciones">
                                                        <button type="button" class="btn btn-info mr-2" data-toggle="modal" data-target="#viewDiscount{{ $discount->id }}" title="Ver Detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @can('editDiscount')
                                                        <button type="button" class="btn btn-warning mr-2" data-toggle="modal" title="Editar Registro" data-target="#edit{{ $discount->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @endcan
                                                        @can('deleteDiscount')
                                                        <button type="button" class="btn btn-danger mr-2" title="Eliminar Registro" data-toggle="modal" data-target="#delete{{ $discount->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>

                                            @include('discounts.show')
                                            @include('discounts.edit')
                                            @include('discounts.delete')

                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    No hay descuentos registrados.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @include('discounts.create')
                                <div class="d-flex justify-content-center">
                                    {!! $discounts->links('pagination::bootstrap-4') !!}
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
    .color-badge{
        display:inline-block;
        padding:8px 16px;
        border-radius:20px;
        font-weight:600;
        color:#fff!important;
        opacity:1!important;
        box-shadow:0 2px 5px rgba(0,0,0,.25);
    }
    .color-badge:hover{
        transform:translateY(-2px);
        box-shadow:0 4px 8px rgba(0,0,0,.25);
    }
    @media(max-width:767px){
        .table-responsive{
            overflow-x:auto;
            -webkit-overflow-scrolling:touch;
        }
        table.dataTable th,
        table.dataTable td{
            white-space:nowrap;
        }
        td .btn-group{
            display:flex;
            flex-wrap:wrap;
            gap:3px;
        }
    }
    .card-box.table-responsive {
        width: 100%;
        margin: 0 auto;
        padding-right: 0;
    }
    table#discounts {
        width: 100% !important;
        table-layout: auto;
        white-space: normal;
    }
    table#discounts th,
    table#discounts td {
        text-align: center;
        vertical-align: middle;
    }
    .dataTables_wrapper {
        overflow-x: hidden !important;
    }
</style>
@endsection

@section('js')
<script>
     $(document).ready(function(){Expand commentComment on line L159Resolved

        $('#discounts').DataTable({
            responsive:true,
            buttons:[
                {
                    extend:'csv',
                    charset:'utf-8',
                    bom:true,
                    exportOptions:{
                        columns:':not(.not-export)'
                    }
                },
                {
                    extend:'excel',
                    exportOptions:{
                        columns:':not(.not-export)'
                    }
                },
                {
                    extend:'print',
                    exportOptions:{
                        columns:':not(.not-export)'
                    }
                }
            ],
            dom:'Bfrtip',
            paging:false,
            info:false,
            searching:false
    document.addEventListener('DOMContentLoaded', function () {
        const colorSelect = document.getElementById('color');
        const previewBox = document.createElement('div');

        previewBox.style.width = '40px';
        previewBox.style.height = '40px';
        previewBox.style.borderRadius = '5px';
        previewBox.style.border = '1px solid #ccc';
        previewBox.style.marginLeft = '10px';
        previewBox.style.display = 'inline-block';
        previewBox.style.verticalAlign = 'middle';
        previewBox.style.backgroundColor = '#6c757d';

        colorSelect.parentNode.appendChild(previewBox);

        colorSelect.addEventListener('change', function () {
            const selectedColor = colorSelect.value;
            previewBox.style.backgroundColor = selectedColor;
        });

        let successMessage="{{ session('success') }}";
        let errorMessage="{{ session('error') }}";

        if(successMessage){Expand commentComment on line L194Resolved
            Swal.fire({
                icon:'success',
                title:'Éxito',
                text:successMessage,
                confirmButtonText:'Aceptar'
            });
        }
        if(errorMessage){
            Swal.fire({
                icon:'error',
                title:'Error',
                text:errorMessage,
                confirmButtonText:'Aceptar'
            });
        }

        const form = document.querySelector('#createDiscount form');
        form.addEventListener('submit', function (e) {
            const selectedColor = colorSelect.value;
            if (!selectedColor) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor selecciona un color para el descuento.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });
</script>
@endsection
