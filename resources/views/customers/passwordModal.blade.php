<div class="modal fade" id="passwordModal{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel{{ $customer->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header bg-green">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Asignar Contraseña <small>&nbsp;(*) Campo requerido</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form method="POST" action="{{ route('customers.assignPassword', $customer->id) }}">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-light">
                                <h3 class="card-title">Contraseña del Cliente</h3>
                            </div>
                            <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="passwordInput{{ $customer->id }}" class="form-label">
                                        Ingrese una contraseña para el inicio de sesión (*)
                                    </label>
                                    <div class="input-group">
                                        <input id="passwordInput{{ $customer->id }}" type="password" name="password" class="form-control" placeholder="Contraseña" required minlength="6">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-default border" id="generatePass{{ $customer->id }}">
                                                Generar Contraseña
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn bg-green text-white">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-show-password@1.2.1/dist/bootstrap-show-password.min.js"></script>

<script>
    document.addEventListener('click', function(e) {

        if (e.target && e.target.id.startsWith('generatePass')) {
            const customerId = e.target.id.replace('generatePass', '');
            const input = document.getElementById('passwordInput' + customerId);
            const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$!";
            let pw = "";
            for (let i = 0; i < 10; i++) {
                pw += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            input.value = pw;
        }
    });

    @if (session('pdf_url'))
            if (!window.pdfOpened) {
                window.pdfOpened = true;
                window.open("{{ session('pdf_url') }}", "_blank");
            }
    @endif
    </script>
