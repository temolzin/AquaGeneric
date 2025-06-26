
<div class="modal fade" id="createResponsible" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header bg-purple">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Cambiar Estatus <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('logsIncidents.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title"></h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="employee" class="form-label">Responsable(*)</label>
                                            <select class="form-control" name="employee" required>
                                                <option value="">Selecciona una opción</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}">
                                                        {{ $employee->name }} - {{ $employee->rol }}
                                                    </option>
                                                @endforeach
                                            </select>    
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Estatus(*)</label>
                                            <select class="form-control" name="status" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="Terminado">Terminado</option>
                                                <option value="Proceso">Proceso</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción</label>
                                            <input type="text" class="form-control" name="description" placeholder="Agrega una descripción">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="incident" class="form-label">Incidencia</label>
                                            <select class="form-control" name="incidentId" required>
                                                <option value="">Selecciona una opción</option>
                                                @foreach ($incidents as $incident)
                                                    <option value="{{ $incident->id }}">{{ $incident->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="save" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
