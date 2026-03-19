@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Categorías de Deuda')

@section('content')
    <section class="content">
        <div class="right_col" role="main">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title mb-3">
                        <h2>Categorías de Deuda</h2>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-lg-flex justify-content-between align-items-center flex-wrap">
                                    <!-- Buscador -->
                                    <form method="GET" action="{{ route('debtCategories.index') }}" class="mb-3 mb-lg-0"
                                        style="min-width: 300px;">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Buscar categoría" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary">
                                                    Buscar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Botón crear -->
                                    <div class="btn-group d-none d-md-flex">
                                        <button class="btn btn-success" data-toggle="modal" data-target="#create">
                                            <i class="fa fa-plus"></i> Registrar Categoría
                                        </button>
                                    </div>
                                    <!-- Versión móvil -->
                                    <div class="d-md-none w-100">
                                        <button class="btn btn-success w-100 mt-2" data-toggle="modal"
                                            data-target="#create">
                                            <i class="fa fa-plus"></i> Registrar
                                        </button>
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
                                    <table id="categories" class="table table-striped display responsive nowrap"
                                        style="width:100%">

                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>NOMBRE</th>
                                                <th>DESCRIPCIÓN</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @if ($debtCategories->count() <= 0)
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        No hay categorías registradas
                                                    </td>
                                                </tr>
                                            @else
                                                @foreach ($debtCategories as $category)
                                                    <tr>
                                                        <td>{{ $category->id }}</td>
                                                        <td>
                                                            <span class="badge {{ $category->color ?? 'bg-secondary' }} text-white px-3 py-2"> {{ $category->name }} </span>
                                                        </td>
                                                        <td>
                                                            {{ Str::limit($category->description, 50) }}
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @can('viewDebtCategories')
                                                                    <button class="btn btn-info btn-lg mr-2" data-toggle="modal"
                                                                        data-target="#view{{ $category->id }}">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                @endcan

                                                                @can('editDebtCategories')
                                                                    <button class="btn btn-warning btn-lg mr-2"
                                                                        data-toggle="modal"
                                                                        data-target="#edit{{ $category->id }}">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                @endcan

                                                                @can('deleteDebtCategories')
                                                                    <form
                                                                        action="{{ route('debtCategories.destroy', $category->id) }}"
                                                                        method="POST" style="display:inline-block">
                                                                        @csrf
                                                                        @method('DELETE')

                                                                        <button class="btn btn-danger btn-lg">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </form>
                                                                @endcan

                                                            </div>

                                                        </td>

                                                    </tr>

                                                    @include('debtCategories.show')
                                                    @include('debtCategories.edit')
                                                    @include('debtCategories.delete')
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    @include('debtCategories.create')

                                    <div class="d-flex justify-content-center">
                                        {!! $debtCategories->links('pagination::bootstrap-4') !!}
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
