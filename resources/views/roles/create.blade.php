<div class="modal fade" id="createRoleModal" tabindex="-1" role="dialog" aria-labelledby="createRoleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Crear nuevo rol <small>&nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Datos del rol</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Nombre</label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="Ingresar nombre" value="{{ old('name') }}" required>
                                </div>
                                <label for="name">Permisos</label>
                                <div id="accordion">
                                    @foreach ($permissions as $module => $modulePermissions)
                                        @php
                                            $moduleTitle = $moduleNames[$module] ?? ucfirst($module);
                                        @endphp
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <button class="btn btn-link p-0 text-left"
                                                            data-toggle="collapse"
                                                            data-target="#collapse{{ Str::slug($module) }}"
                                                            aria-expanded="true"
                                                            aria-controls="collapse{{ Str::slug($module) }}">
                                                        <strong>{{ $moduleTitle }}</strong>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm select-all-btn"
                                                        style="background-color: #f2f2f2; border: 1px solid #d9d9d9; border-radius: 20px;
                                                            padding: 3px 14px; font-size: 12px; color: #4d4d4d;"
                                                        data-target="module-{{ Str::slug($module) }}">
                                                        Seleccionar todo
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="collapse{{ Str::slug($module) }}" class="collapse show"
                                                aria-labelledby="heading{{ Str::slug($module) }}">
                                                <div class="card-body p-0">
                                                    <div class="table-responsive">
                                                        <table
                                                            class="table table-sm table-bordered table-striped mb-0 module-{{ Str::slug($module) }}">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 100px;">Seleccionar</th>
                                                                    <th>Permiso</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($modulePermissions as $permission)
                                                                    <tr>
                                                                        <td>
                                                                            <div
                                                                                class="custom-control custom-checkbox custom-checkbox-lg">
                                                                                <input type="checkbox"
                                                                                    name="permissions[]"
                                                                                    value="{{ $permission->id }}"
                                                                                    class="custom-control-input"
                                                                                    id="permission{{ $permission->id }}">
                                                                                <label class="custom-control-label"
                                                                                    for="permission{{ $permission->id }}"></label>
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ $permission->description }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('#createRoleModal form');

        form.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.select-all-btn').forEach(button => {

            button.addEventListener('click', function (event) {
                event.stopPropagation();
                const moduleClass = this.getAttribute('data-target');
                const checkboxes = document.querySelectorAll('.' + moduleClass + ' input[type="checkbox"]');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
                this.textContent = allChecked ? 'Seleccionar todo' : 'Deseleccionar todo';
            });
        });
    });
</script>
