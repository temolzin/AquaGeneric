<div class="modal fade" id="importData" tabindex="-1" role="dialog" aria-labelledby="importDataLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="importDataLabel">
                    <i class="fas fa-file-import mr-2"></i>Importar Empleados
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="importForm" action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-file-csv text-info fa-3x mb-3"></i>
                        <h5 class="text-dark">Subir Archivo CSV</h5>
                        <p class="text-muted small">Formato aceptado: .csv</p>
                        <a href="{{ route('employees.downloadTemplate') }}" class="btn btn-outline-info btn-sm mt-2" download>Descargar Plantilla CSV</a>
                    </div>
                    <div class="bg-light-info p-3 rounded mb-3 text-center">
                        <h6 class="text-info mb-3">
                            <i class="fas fa-info-circle mr-2"></i>Información Importante
                        </h6>
                        <p class="small text-dark mb-3">Estas columnas deben ser llenadas correctamente:</p>
                        <div class="row justify-content-center">
                            <div class="col-md-6 mb-2">
                                <strong class="text-dark small">Roles válidos:</strong>
                                <div class="mt-1">
                                    <span class="badge badge-info mr-1">Administrativo</span>
                                    <span class="badge badge-info mr-1">Supervisor</span>
                                    <span class="badge badge-info mr-1">Operativo</span>
                                    <span class="badge badge-info">Gerente</span>
                                </div>
                            </div>
                        </div>
                        <p class="small text-info mt-2 mb-0">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Solo se aceptan estos valores. Cualquier otro valor será rechazado.
                        </p>
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
                            <div id="progressBar" class="progress-bar bg-info progress-bar-striped progress-bar-animated" 
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
                    <button type="submit" id="importButton" class="btn btn-info">
                        <i class="fas fa-file-import mr-2"></i>Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .bg-info {
        background-color: #17a2b8 !important;
    }
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }
    .btn-info:hover {
        background-color: #138496;
        border-color: #138496;
        color: white;
    }
    .btn-outline-info {
        color: #17a2b8;
        border-color: #17a2b8;
        background-color: transparent;
    }
    .btn-outline-info:hover {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }
    .text-info {
        color: #17a2b8 !important;
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
        border: 1px solid #17a2b8;
    }
    .custom-file-input:focus ~ .custom-file-label {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }
    .bg-light-info {
        background-color: rgba(23, 162, 184, 0.1);
        border: 1px solid rgba(23, 162, 184, 0.2);
    }
    .badge-info {
        background-color: #17a2b8;
        color: white;
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    .badge-info:hover {
        background-color: #138496;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    $(document).ready(function() {
        $('#excel_file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('#fileLabel').html('<i class="fas fa-file-csv mr-2 text-success"></i>' + fileName);
                $('#fileName').removeClass('d-none');
                $('#selectedFileName').text(fileName);

                $('#importResults').addClass('d-none');
                $('#importErrors').addClass('d-none');
            } else {
                $('#fileLabel').html('<i class="fas fa-file-csv mr-2"></i>Buscar archivo CSV...');
                $('#fileName').addClass('d-none');
            }
        });

        $('#importForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var importButton = $('#importButton');
            var progressContainer = $('#progressContainer');
            var progressBar = $('#progressBar');
            var progressText = $('#progressText');

            $('#importResults').addClass('d-none');
            $('#importErrors').addClass('d-none');

            progressContainer.removeClass('d-none');
            importButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importando...');

            axios.post('{{ route('employees.import') }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                onUploadProgress: function(progressEvent) {
                    if (progressEvent.lengthComputable) {
                        var percentComplete = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        progressBar.css('width', percentComplete + '%');
                        progressText.text(percentComplete + '%');
                    }
                }
            })
            .then(function(response) {
                console.log('Respuesta exitosa:', response.data);

                $('#importResults').removeClass('d-none');
                $('#resultsContent').html(`
                    <p><strong>Registros procesados:</strong> ${response.data.processed}</p>
                    <p><strong>Registros importados:</strong> ${response.data.imported}</p>
                    <p><strong>Registros con errores:</strong> ${response.data.failed}</p>
                `);

                $('#importForm')[0].reset();
                $('#fileLabel').html('<i class="fas fa-file-csv mr-2"></i>Buscar archivo CSV...');
                $('#fileName').addClass('d-none');

                progressContainer.addClass('d-none');
                progressBar.css('width', '0%');
                progressText.text('0%');
                importButton.prop('disabled', false).html('<i class="fas fa-file-import mr-2"></i>Importar');

                if (response.data.failed > 0 && response.data.errors && response.data.errors.length > 0) {
                    $('#importErrors').removeClass('d-none');
                    let errorsHtml = '<ul class="mb-0">';
                    response.data.errors.forEach(error => {
                        errorsHtml += `<li class="small">${error}</li>`;
                    });
                    errorsHtml += '</ul>';
                    $('#errorsContent').html(errorsHtml);
                }

                if (response.data.imported > 0) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                }
            })
            .catch(function(error) {
                console.error('Error en la petición:', error);

                $('#importErrors').removeClass('d-none');
                let errorMessage = 'Error al importar el archivo. Verifica el formato.';

                if (error.response && error.response.data) {
                    if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                    if (error.response.data.errors) {
                        let errors = error.response.data.errors;
                        let errorsHtml = '<ul class="mb-0">';
                        for (let field in errors) {
                            errors[field].forEach(msg => {
                                errorsHtml += `<li class="small">${msg}</li>`;
                            });
                        }
                        errorsHtml += '</ul>';
                        $('#errorsContent').html(errorsHtml);
                    } else {
                        $('#errorsContent').html('<p class="mb-0">' + errorMessage + '</p>');
                    }
                } else {
                    $('#errorsContent').html('<p class="mb-0">' + errorMessage + '</p>');
                }

                progressContainer.addClass('d-none');
                importButton.prop('disabled', false).html('<i class="fas fa-file-import mr-2"></i>Importar');
            });
        });
    });
</script>
