@foreach($localities as $locality)
    <div class="modal fade" id="mailConfigModal{{ $locality->id }}" tabindex="-1" role="dialog" aria-labelledby="mailConfigLabel{{ $locality->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="card-success">
                    <div class="card-header bg-purple">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <h4 class="card-title">
                                Configuración de Correo - {{ $locality->name }}
                                <small>&nbsp;(*) Campos requeridos</small>
                            </h4>
                            <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('mailConfigurations.createOrUpdate', $locality->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="card">
                                <div class="card-header py-2 bg-secondary">
                                    <h3 class="card-title">Ingrese los Datos de Configuración de Correo</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @php
                                            $config = $locality->mailConfiguration;
                                        @endphp
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="mailer{{ $locality->id }}">Mailer(*)</label>
                                                <input type="text" name="mailer" class="form-control" id="mailer{{ $locality->id }}" placeholder="Ingresa el mailer" value="{{ old('mailer', $config?->mailer) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="host{{ $locality->id }}">Host(*)</label>
                                                <input type="text" name="host" class="form-control" id="host{{ $locality->id }}" placeholder="Ingresa el host" value="{{ old('host', $config?->host) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="port{{ $locality->id }}">Puerto(*)</label>
                                                <input type="number" name="port" class="form-control" id="port{{ $locality->id }}" placeholder="Ingresa el puerto" value="{{ old('port', $config?->port) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="username{{ $locality->id }}">Usuario(*)</label>
                                                <input type="text" name="username" class="form-control" id="username{{ $locality->id }}" placeholder="Ingresa el usuario" value="{{ old('username', $config?->username) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="password{{ $locality->id }}">Contraseña(*)</label>
                                                <input type="text" name="password" class="form-control" id="password{{ $locality->id }}" placeholder="Ingresa la contraseña" value="{{ old('password', $config?->password) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="encryption{{ $locality->id }}">Encriptación(*)</label>
                                                <input type="text" name="encryption" class="form-control" id="encryption{{ $locality->id }}" placeholder="Ingresa el tipo de encriptación" value="{{ old('encryption', $config?->encryption) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="fromAddress{{ $locality->id }}">Correo de Envío(*)</label>
                                                <input type="email" name="from_address" class="form-control" id="fromAddress{{ $locality->id }}" placeholder="Ingresa el correo de envío" value="{{ old('from_address', $config?->from_address) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="fromName{{ $locality->id }}">Nombre del Remitente</label>
                                                <input type="text" name="from_name" class="form-control" id="fromName{{ $locality->id }}" placeholder="Ingresa el nombre del remitente" value="{{ old('from_name', $config?->from_name) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-3 collapsed-card">
                                <div class="card-header py-2 bg-secondary">
                                    <h3 class="card-title">
                                        Ejemplos de Llenado de Configuración de Correo
                                    </h3>
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
                                                <label>Mailer</label>
                                                <input type="text" class="form-control" value="{{ $mailExamples['mailer'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Host</label>
                                                <input type="text" class="form-control" value="{{ $mailExamples['host'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Puerto</label>
                                                <input type="text" class="form-control" value="{{ $mailExamples['port'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Usuario</label>
                                                <input type="text" class="form-control" value="{{ $mailExamples['username'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Contraseña</label>
                                                <input type="text" class="form-control" value="{{ $mailExamples['password'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Encriptación</label>
                                                <input type="text" class="form-control" value="{{ $mailExamples['encryption'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Correo de Envío</label>
                                                <input type="email" class="form-control" value="{{ $mailExamples['from_address'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Nombre del Remitente</label>
                                                <input type="text" class="form-control" value="{{ $mailExamples['from_name'] }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn bg-purple mr-2">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
