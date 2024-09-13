<div class="modal fade" id="view{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $payment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información del Pago</h4>
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
                                        <label>ID del pago</label>
                                        <input type="text" disabled class="form-control" value="{{ $payment->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <label>Deuda</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" disabled class="form-control" 
                                            value="{{ \Carbon\Carbon::parse($payment->debt->start_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }} - {{ \Carbon\Carbon::parse($payment->debt->end_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }} | Monto: {{ $payment->debt->amount }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Monto del pago</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" disabled class="form-control" value="{{ $payment->amount }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fecha de Pago</label>
                                        <input type="text" disabled class="form-control" value="{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}" />
                                    </div>
                                </div>                                
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Nota del Pago</label>
                                        <textarea disabled class="form-control">{{ $payment->note }}</textarea>
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
