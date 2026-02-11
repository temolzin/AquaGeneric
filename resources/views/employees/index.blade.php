@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Empleados')

@section('content')
    <section class="content">
        <div class="right_col" role="main">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title mb-3">
                        <h2>Empleados</h2>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-lg-flex justify-content-between align-items-center flex-wrap">
                                    <form method="GET" action="{{ route('employees.index') }}" class="mb-3 mb-lg-0" style="min-width: 300px;">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, apellido" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary" title="Buscar Empleado">Buscar</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="btn-group d-none d-md-flex" role="group" aria-label="Acciones de Empleado">
                                        <button class="btn btn-success mr-2" data-toggle='modal' data-target="#createEmployee" title="Registrar Empleado">
                                            <i class="fa fa-plus"></i> Registrar Empleado
                                        </button>
                                        <button class="btn btn-info mr-2" data-toggle="modal" data-target="#importData" title="Importar Empleados">
                                            <i class="fas fa-file-import"></i> Importar Empleados
                                        </button>
                                        <a type="button" class="btn btn-secondary" target="_blank" title="Generar Lista de Empleados" href="{{ route('report.generateEmployeeListReport') }}">
                                            <i class="fas fa-file-pdf"></i> Generar Lista
                                        </a>
                                    </div>
                                    <div class="d-md-none w-100">
                                        <div class="row g-2">
                                            <div class="col-6 pe-1">
                                                <button class="btn btn-success w-100 py-2" data-toggle='modal' data-target="#createEmployee" title="Registrar Empleado">
                                                    <i class="fa fa-plus"></i> Registrar Empleado
                                                </button>
                                            </div>
                                            <div class="col-6 ps-1">
                                                <button class="btn btn-info w-100 py-2" data-toggle="modal" data-target="#importData" title="Importar Empleados">
                                                    <i class="fas fa-file-import"></i> Importar Empleados
                                                </button>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <a type="button" class="btn btn-secondary w-100 py-2" target="_blank" title="Generar Lista" href="{{ route('report.generateEmployeeListReport') }}">
                                                    <i class="fas fa-file-pdf"></i> Generar Lista
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
                                    <table id="employees" class="table table-striped display responsive nowrap"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>FOTO</th>
                                                <th>NOMBRE</th>
                                                <th>DIRECCION</th>
                                                <th>OPCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($employees) <= 0)
                                                <tr>
                                                    <td colspan="5">No hay resultados</td>
                                                </tr>
                                            @else
                                                @foreach($employees as $employee)
                                                    <tr>
                                                        <td scope="row">{{$employee->id}}</td>
                                                        <td>
                                                            @php
                                                                $photoUrl = $employee->getFirstMediaUrl('employeeGallery');
                                                            @endphp
                                                            @if ($photoUrl)
                                                                <img src="{{ $photoUrl }}" alt="Foto de {{ $employee->name }}"
                                                                    style="width: 50px; height: 50px; border-radius: 50%;">
                                                            @else
                                                                <img src="{{ asset('img/userDefault.png') }}" alt="Imagen por defecto"
                                                                    style="width: 50px; height: 50px; border-radius: 50%;">
                                                            @endif
                                                        </td>
                                                        <td>{{$employee->name}} {{$employee->last_name}}</td>
                                                        <td>{{$employee->state}}, {{$employee->locality}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group" aria-label="Opciones">
                                                                <button type="button" class="btn btn-info btn-lg mr-2" data-toggle="modal"
                                                                    title="Ver Detalles" data-target="#view{{$employee->id}}">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @can('editEmployee')
                                                                <button type="button" class="btn btn-warning btn-lg mr-2"
                                                                    data-toggle="modal" title="Editar Datos"
                                                                    data-target="#edit{{$employee->id}}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                @endcan
                                                                @can('deleteEmployee')
                                                                <button type="button" class="btn btn-danger btn-lg mr-2"
                                                                    data-toggle="modal" title="Eliminar Registro"
                                                                    data-target="#delete{{$employee->id}}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                        @include('employees.edit')
                                                        @include('employees.delete')
                                                        @include('employees.show')
                                                        @include('employees.import-modal')
                                                        @include('employees.create') 
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                        {!! $employees->links('pagination::bootstrap-4') !!}
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $(document).ready(function () 
        {
            $('#employees').DataTable
            ({
                responsive: true,
                buttons: ['csv', 'excel', 'print'],
                dom: 'Bfrtip',
                paging: false,
                info: false,
                searching: false
            });

            $('#excel_file').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $('#fileLabel').text(fileName || 'Ningún archivo seleccionado');
                $('#importResults').addClass('d-none');
                $('#importErrors').addClass('d-none');
            });

            $('#importForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var importButton = $('#importButton');
                var progressContainer = $('#progressContainer');
                var progressBar = $('#progressBar');
                var progressText = $('#progressText');

                progressContainer.removeClass('d-none');
                importButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importando...');

                axios.post('{{ route('employees.import') }}', formData, {
                    headers: {'Content-Type': 'multipart/form-data'},
                    onUploadProgress: function(progressEvent) {
                        if (progressEvent.lengthComputable) {
                            var percentComplete = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                            progressBar.css('width', percentComplete + '%');
                            progressText.text(percentComplete + '%');
                        }
                    }
                })
                .then(function(response) {
                    $('#importForm')[0].reset();
                    $('#fileLabel').text('Ningún archivo seleccionado');
                    progressContainer.addClass('d-none');
                    progressBar.css('width', '0%');
                    progressText.text('0%');
                    importButton.prop('disabled', false).html('<i class="fas fa-file-import"></i> Importar');

                    if (response.data.failed === 0) {
                        $('#importResults').removeClass('d-none');
                        $('#resultsContent').html(`
                            <p><strong>Registros procesados:</strong> ${response.data.processed}</p>
                            <p><strong>Registros importados:</strong> ${response.data.imported}</p>
                            <p><strong>Registros con errores:</strong> ${response.data.failed}</p>
                        `);
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        $('#importErrors').removeClass('d-none');
                        let errorsHtml = `
                            <p><strong>Registros procesados:</strong> ${response.data.processed}</p>
                            <p><strong>Registros importados:</strong> ${response.data.imported}</p>
                            <p><strong>Registros con errores:</strong> ${response.data.failed}</p>
                            <ul class="mt-2 mb-0">
                        `;
                        response.data.errors.forEach(error => {
                            errorsHtml += `<li>${error}</li>`;
                        });
                        errorsHtml += '</ul>';
                        $('#errorsContent').html(errorsHtml);
                    }
                })
                .catch(function(error) {
                    $('#importErrors').removeClass('d-none');
                    let errorMessage = 'Error al importar el archivo. Verifica el formato.';
                    
                    if (error.response && error.response.data && error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }

                    $('#errorsContent').html('<p>' + errorMessage + '</p>');
                    progressContainer.addClass('d-none');
                    importButton.prop('disabled', false).html('<i class="fas fa-file-import"></i> Importar');
                });
            });

            var successMessage = "{{ session('success') }}";
            var errorMessage = "{{ session('error') }}";
            if (successMessage) {
                Swal.fire
                ({
                    icon: 'success',
                    title: 'Éxito',
                    text: successMessage,
                    confirmButtonText: 'Aceptar'
                });
            }
            if (errorMessage) 
            {
                Swal.fire
                ({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'Aceptar'
                });
            }

            @if(session('import_errors'))
                $('#importData').modal('show');
            @endif
        });
    </script>
@endsection
