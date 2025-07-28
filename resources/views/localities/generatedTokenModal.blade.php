<div class="modal fade" id="generatedTokenModal" tabindex="-1" role="dialog" aria-labelledby="generatedTokenLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #fd7e14; color: white;">
                <h5 class="modal-title" id="generatedTokenLabel">Token Generado</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Localidad:</strong> {{ $localityName }}</p>
                <label>Token generado:</label>
                <textarea class="form-control" id="generatedToken" rows="4" readonly>{{ $token }}</textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button class="btn" style="background-color: #fd7e14; color: white;" onclick="copyToken()">Copiar Token</button>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToken() {
        const tokenField = document.getElementById("generatedToken");
        tokenField.select();
        tokenField.setSelectionRange(0, 99999);
        document.execCommand("copy");

        Swal.fire({
            icon: 'success',
            title: 'Copiado',
            text: 'El token ha sido copiado al portapapeles.',
            confirmButtonText: 'Aceptar'
        });
    }
</script>
