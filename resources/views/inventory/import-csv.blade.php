<div class="modal fade" id="importCsvModal" tabindex="-1" role="dialog" aria-labelledby="importCsvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header bg-warning">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title text-white">Importar Componentes desde CSV <small class="text-white"> &nbsp;Formatos soportados: .csv</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('inventory.importCsv') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="csv_file" class="form-label">Archivo CSV(*)</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="csv_file" name="csv_file" accept=".csv" required>
                                                <label class="custom-file-label" for="csv_file" id="csv_file_label">Selecciona un archivo CSV</label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Formatos aceptados: CSV (máximo 5MB)
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="alert alert-warning">
                                            <h6><i class="fas fa-info-circle"></i> Estructura requerida del CSV:</h6>
                                            <ul class="mb-0">
                                                <li><strong>Columnas requeridas:</strong> Nombre, Cantidad, Categoría</li>
                                                <li><strong>Columnas opcionales:</strong> Descripción, Material, Dimensiones</li>
                                                <li><strong>Formato:</strong> UTF-8, delimitado por comas</li>
                                                <li><strong>Categorías:</strong> Usar nombres exactos (ej: "Medidores de Agua", "Tuberías y Conexiones")</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-label">Ejemplo de estructura:</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr class="bg-warning text-dark">
                                                            <th>Nombre</th>
                                                            <th>Cantidad</th>
                                                            <th>Categoría</th>
                                                            <th>Descripción</th>
                                                            <th>Material</th>
                                                            <th>Dimensiones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Válvula de bola</td>
                                                            <td>50</td>
                                                            <td>Válvulas y Reguladores</td>
                                                            <td>Válvula para control de flujo</td>
                                                            <td>PVC</td>
                                                            <td>1 pulgada</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tubería acero</td>
                                                            <td>100</td>
                                                            <td>Tuberías y Conexiones</td>
                                                            <td>Tubería para distribución</td>
                                                            <td>Acero inoxidable</td>
                                                            <td>2 pulgadas</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Medidor digital</td>
                                                            <td>25</td>
                                                            <td>Medidores de Agua</td>
                                                            <td>Medidor para monitoreo de consumo</td>
                                                            <td>Latón</td>
                                                            <td>50 mm</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="alert alert-light border-warning">
                                            <h6 class="text-warning"><i class="fas fa-lightbulb"></i> Categorías disponibles:</h6>
                                            <div class="row">
                                                @php
                                                    $categories = \App\Models\InventoryCategory::where('locality_id', auth()->user()->locality_id)->get();
                                                @endphp
                                                @foreach($categories->chunk(2) as $chunk)
                                                    <div class="col-md-6">
                                                        <ul class="mb-2">
                                                            @foreach($chunk as $category)
                                                                <li><strong>{{ $category->name }}</strong></li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="skip_errors" name="skip_errors" value="1">
                                            <label class="form-check-label" for="skip_errors">
                                                Omitir filas con errores y continuar con la importación
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- BOTÓN NUEVO: DESCARGAR PLANTILLA --}}
                        <a href="{{ route('inventory.downloadTemplate') }}" class="btn btn-info mr-auto" title="Descargar plantilla CSV">
                            <i class="fas fa-download mr-2"></i>Plantilla
                        </a>
                        
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-file-import mr-2"></i>Importar CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .select2-container .select2-selection--single {
        height: 40px;
        display: flex;
        align-items: center;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }

    .custom-file-input:focus ~ .custom-file-label {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }

    .card-warning {
        border-color: #ffc107;
    }

    .card-warning .card-header {
        background-color: #ffc107;
        border-bottom: 1px solid #ffc107;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Actualizar label del archivo seleccionado
        document.getElementById('csv_file').addEventListener('change', function(e) {
            var fileName = e.target.files[0] ? e.target.files[0].name : 'Selecciona un archivo CSV';
            document.getElementById('csv_file_label').textContent = fileName;
        });

        // Validación básica del archivo
        document.querySelector('form').addEventListener('submit', function(e) {
            var fileInput = document.getElementById('csv_file');
            var file = fileInput.files[0];
            
            if (!file) {
                e.preventDefault();
                alert('Por favor selecciona un archivo CSV.');
                return;
            }

            if (!file.name.toLowerCase().endsWith('.csv')) {
                e.preventDefault();
                alert('Por favor selecciona un archivo con extensión .csv');
                return;
            }

            // Validar tamaño (5MB)
            if (file.size > 5 * 1024 * 1024) {
                e.preventDefault();
                alert('El archivo es demasiado grande. Máximo 5MB permitido.');
                return;
            }
        });
    });
</script>
