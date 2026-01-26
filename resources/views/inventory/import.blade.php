<div class="modal fade" id="importData" tabindex="-1" role="dialog" aria-labelledby="importDataLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-purple text-white">
                <h5 class="modal-title" id="importDataLabel">
                    <i class="fas fa-file-import mr-2"></i>Importar Inventario
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="importForm" action="{{ route('inventory.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-file-csv text-purple fa-3x mb-3"></i>
                        <h5 class="text-dark">Subir Archivo CSV</h5>
                        <p class="text-muted small">Formato aceptado: .csv</p>
                        <a href="{{ asset('layout/plantilla_inventario.csv') }}" class="btn btn-outline-purple btn-sm mt-2" download>
                            <i class="fas fa-download mr-2"></i>Descargar Plantilla
                        </a>
                    </div>
                    <div class="bg-light-purple p-3 rounded mb-3 text-center">
                        <h6 class="text-purple mb-3">
                            <i class="fas fa-info-circle mr-2"></i>Información Importante
                        </h6>
                        <div class="row justify-content-center mt-3">
                            <div class="col-md-6 mb-2">
                                <strong class="text-dark small">Formato de Cantidad:</strong>
                                <div class="mt-1">
                                    <span class="badge bg-purple">Número Entero</span>
                                    <p class="small text-muted mb-0 mt-1">Ejemplo: 10, 25, 100</p>
                                    <p class="small text-purple mt-2 mb-0">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Todos los campos son obligatorios.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Seleccionar archivo CSV:</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="excel_file" name="excel_file" accept=".csv" required>
                            <label class="custom-file-label text-truncate" for="excel_file" id="fileLabel">
                                <i class="fas fa-file-csv mr-2"></i>Buscar archivo CSV...
                            </label>
                        </div>
                    </div>
                    <div id="fileName" class="mb-3 p-2 bg-light rounded text-center d-none">
                        <i class="fas fa-file-csv text-success mr-2"></i>
                        <span id="selectedFileName" class="font-weight-bold text-dark"></span>
                    </div>
                    <div id="progressContainer" class="d-none">
                        <div class="progress mb-2">
                            <div id="progressBar" class="progress-bar bg-purple progress-bar-striped progress-bar-animated" 
                                role="progressbar" style="width: 0%">
                                <span id="progressText">0%</span>
                            </div>
                        </div>
                        <p class="text-center text-muted small mb-0">
                            <i class="fas fa-sync fa-spin mr-1"></i>Procesando...
                        </p>
                    </div>
                    <div id="importResults" class="alert alert-success d-none">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>¡Importación Exitosa!</strong>
                        <div id="resultsContent" class="small mt-1"></div>
                    </div>
                    <div id="importErrors" class="alert alert-danger d-none">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Error en la Importación</strong>
                        <div id="errorsContent" class="small mt-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="importButton" class="btn btn-purple">
                        <i class="fas fa-file-import mr-2"></i>Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .btn-purple {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }

    .btn-purple:hover {
        background-color: #5a2d9c;
        border-color: #5a2d9c;
        color: white;
    }

    .btn-outline-purple {
        color: #6f42c1;
        border-color: #6f42c1;
        background-color: transparent;
    }

    .btn-outline-purple:hover {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }

    .text-purple {
        color: #6f42c1 !important;
    }

    .progress {
        height: 20px;
        border-radius: 5px;
    }

    .progress-bar {
        border-radius: 5px;
        font-size: 0.8rem;
        font-weight: bold;
    }

    .custom-file-label {
        border: 1px solid #6f42c1;
    }

    .custom-file-input:focus ~ .custom-file-label {
        border-color: #6f42c1;
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
    }

    .bg-light-purple {
        background-color: rgba(111, 66, 193, 0.1);
        border: 1px solid rgba(111, 66, 193, 0.2);
    }

    .badge-purple {
        background-color: #6f42c1;
        color: white;
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }

    .badge-purple:hover {
        background-color: #5a2d9c;
    }
</style>

<script>
    $('#excel_file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#fileLabel').html('<i class="fas fa-file-csv mr-2 text-success"></i>' + fileName);
            $('#fileName').removeClass('d-none');
            $('#selectedFileName').text(fileName);
        } else {
            $('#fileLabel').html('<i class="fas fa-file-csv mr-2"></i>Buscar archivo CSV...');
            $('#fileName').addClass('d-none');
        }
    });
</script>
