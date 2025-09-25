<div class="modal fade" id="delete{{ $notice->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="exampleModalLabel">Eliminar aviso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('localityNotices.destroy', $notice->id) }}" method="post" enctype="multipart/form-data" onsubmit="return handleDeleteSubmit(this, {{ $notice->id }})">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center text-danger">
                    ¿Estás seguro de eliminar el aviso <strong>{{ $notice->title }}?</strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="confirmBtn{{ $notice->id }}">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function handleDeleteSubmit(form, noticeId) {
    var submitBtn = document.getElementById('confirmBtn' + noticeId);
    
    if (submitBtn.disabled) {
        return false;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Eliminando...`;

    return true;
}
</script>