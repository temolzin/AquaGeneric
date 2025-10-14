<div class="modal fade" id="deleteDebt{{ $waterConnectionDebt->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="exampleModalLabel">Eliminar Deuda</h5>
                <button type="button" class="close" onclick="closeCurrentModal('#deleteDebt{{ $waterConnectionDebt->id }}')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('debts.destroy', $waterConnectionDebt->id ) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center text-danger">
                    ¿Estás seguro de eliminar la deuda del periodo <strong>{{ \Carbon\Carbon::parse($waterConnectionDebt->start_date)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} - {{ \Carbon\Carbon::parse($waterConnectionDebt->end_date)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} ?</strong>
                    Recuerda que si la deuda tiene pagos asociados se eliminaran.
                </div>
                <input type="hidden" name="modal_id" value="view{{ $debt->customer->user->id }}">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCurrentModal('#deleteDebt{{ $waterConnectionDebt->id }}')">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>
