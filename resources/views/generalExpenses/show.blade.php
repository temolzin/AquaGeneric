<div class="modal fade" id="view{{ $expense->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $expense->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información del Gasto</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>ID</label>
                                        <input type="text" disabled class="form-control" value="{{ $expense->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <label>Concepto</label>
                                        <input type="text" disabled class="form-control" value="{{ $expense->concept }}" />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <textarea disabled class="form-control">{{ $expense->description }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        @switch($expense->type)
                                            @case('mainteinence')
                                            <input type="text" disabled class="form-control" value="Mantenimiento" />
                                                @break
                                            @case('services')
                                            <input type="text" disabled class="form-control" value="Servicios" />
                                                @break
                                            @case('supplies')
                                            <input type="text" disabled class="form-control" value="Insumos" />
                                                @break
                                            @case('taxes')
                                            <input type="text" disabled class="form-control" value="Impuestos" />
                                                @break
                                            @case('staff')
                                            <input type="text" disabled class="form-control" value="Personal" />
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="amount" class="form-label">Monto(*)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="number" disabled class="form-control" value="{{ $expense->amount }}" />
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fecha del gasto</label>
                                        <input type="text" disabled class="form-control" value="{{ $expense->expense_date }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Comprobante del Gasto</label>
                                        @if ($expense->hasMedia('expenseGallery'))
                                            <form action="{{ $expense->getFirstMediaUrl('expenseGallery') }}" method="get" target="_blank">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-eye"></i> Ver recibo
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary" disabled>
                                                <i class="fas fa-eye-slash"></i> Sin recibo disponible
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Registrado por</label>
                                        <input type="text" disabled class="form-control" value="{{ $expense->creator->name ?? 'Desconocido' }} {{ $expense->creator->last_name ?? '' }}" />
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
