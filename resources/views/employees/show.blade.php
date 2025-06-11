<div class="modal fade" id="view{{ $employee->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $employee->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información del Empleado</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group text-center">
                                        @if ($employee->getFirstMediaUrl('employeeGallery'))
                                            <img src="{{ $employee->getFirstMediaUrl('employeeGallery') }}" alt="Foto del Empleado" class="img-fluid" 
                                            style="width: 120px; height: 120px; border-radius: 50%; margin-bottom: 5px;">
                                        @else
                                            <img src="{{ asset('img/userDefault.png') }}" alt="Foto del Usuario" class="img-fluid" 
                                            style="width: 120px; height: 120px; border-radius: 50%; margin-bottom: 5px;">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>ID</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->name }} {{ $employee->last_name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Calle</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->street }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Colonia</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->block }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Localidad</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->locality ?? 'Desconocido' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->state ?? 'Desconocido' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Código Postal</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->zip_code ?? 'Desconocido' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Número Exterior</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->exterior_number }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Número Interior</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->interior_number }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Correo Electrónico</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->email }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Número telefónico</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->phone_number }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Salario</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->salary }}" />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Registrado por</label>
                                        <input type="text" disabled class="form-control" value="{{ $employee->creator->name ?? 'Desconocido' }} {{ $employee->creator->last_name ?? '' }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
