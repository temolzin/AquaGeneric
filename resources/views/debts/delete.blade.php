<div class="modal fade" id="delete{{ $customerDebt->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="exampleModalLabel">Eliminar Deuda</h5>
                <button type="button" class="close" onclick="closeCurrentModal('#delete{{ $customerDebt->id }}')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('debts.destroy', $customerDebt->id ) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center text-danger">
                    ¿Estás seguro de eliminar la deuda del periodo <strong>{{ strftime('%d de %B de %Y', strtotime($customerDebt->start_date)) }} - {{ strftime('%d de %B de %Y', strtotime($customerDebt->end_date)) }} ?</strong>
                    Recuerda que si tiene pagos asociados las deuda se eliminaran.
                </div>
                <input type="hidden" name="modal_id" value="view{{ $debt->customer->id }}">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCurrentModal('#delete{{ $customerDebt->id }}')">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>
