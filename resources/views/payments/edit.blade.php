<div class="modal fade" id="editPayment{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Pago <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('payments.update', $payment->id) }}" method="post" id="edit-payment-form-{{ $payment->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Detalles del Pago</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-8"></div>
                                    <div class="col-lg-4 text-right">
                                        <div class="form-group text-right">
                                            <label for="payment_date_display" class="form-label">Fecha del Pago</label>
                                            <input type="text" class="form-control" id="payment_date_display" 
                                                 value="{{ \Carbon\Carbon::parse($payment->payment_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}" readonly />
                                            <input type="hidden" name="payment_date" value="{{ date('Y-m-d') }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="debt_period" class="form-label">Período de la Deuda</label>
                                            <input type="text" class="form-control" id="debt_period" 
                                            value="{{ \Carbon\Carbon::parse($payment->debt->start_date)->locale('es')->isoFormat('MMMM [/] YYYY') }} - {{ \Carbon\Carbon::parse($payment->debt->end_date)->locale('es')->isoFormat('MMMM [/] YYYY') }} | Monto: {{ $payment->debt->amount }}" readonly />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="amount" class="form-label">Monto del Pago(*)</label>
                                            <input type="number" class="form-control" name="amount" id="amount" value="{{ $payment->amount }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="note" class="form-label">Nota del Pago</label>
                                            <textarea class="form-control" name="note" id="note">{{ $payment->note }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
