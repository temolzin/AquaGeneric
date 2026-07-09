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
                                                value="{{ \Carbon\Carbon::parse($payment->created_at)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}" readonly />
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
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                                </div>
                                                <input type="number" min="1" class="form-control" name="amount" id="amount-{{ $payment->id }}" value="{{ $payment->amount }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <div class="custom-control custom-checkbox mb-3">
                                                    <input type="number" min="1" class="form-control" name="amount" id="amount-{{ $payment->id }}" value="{{ $payment->amount }}" required>
                                                        {{ $payment->discount_id ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-warning" for="apply_discount-{{ $payment->id }}">
                                                        Aplicar descuento a este pago
                                                    </label>
                                                </div>
                                                <div id="discount_fields-{{ $payment->id }}" style="{{ $payment->discount_id ? '' : 'display:none;' }}">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="form-group">
                                                                <label class="form-label">
                                                                    Descuento(*)
                                                                </label>
                                                                <select class="form-control select2" name="discount_id" id="discount_id-{{ $payment->id }}">
                                                                    <option value="">
                                                                        Selecciona un descuento
                                                                    </option>
                                                                    @foreach($discounts as $discount)
                                                                        <option value="{{ $discount->id }}" data-percentage="{{ $discount->percentage }}" {{ $payment->discount_id == $discount->id ? 'selected' : '' }}>
                                                                            {{ $discount->name }}
                                                                            {{ number_format($discount->percentage,2) }}%
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-label">
                                                                    Porcentaje
                                                                </label>
                                                                <input type="text" class="form-control" id="discount_percentage-{{ $payment->id }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label class="form-label">
                                                                    Descuento
                                                                </label>
                                                                <input type="text" class="form-control bg-light text-success font-weight-bold" id="discount_amount-{{ $payment->id }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label class="form-label">
                                                                    Total con descuento
                                                                </label>
                                                                <input type="text" class="form-control bg-light text-success font-weight-bold" id="amount_with_discount-{{ $payment->id }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <label class="form-label">
                                                                Monto Original
                                                            </label>
                                                            <input type="text" class="form-control" id="original_amount-{{ $payment->id }}" value="${{ number_format($payment->amount,2) }}" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="note" class="form-label">Nota del Pago</label>
                                            <textarea class="form-control" name="note" id="note-{{ $payment->id }}">{{ $payment->note }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancel-button-{{ $payment->id }}">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){

        let paymentId = "{{ $payment->id }}";

        function formatCurrency(value){
            return '$' + Number(value || 0).toFixed(2);
        }


        function updateDiscount(){

            let option = $('#discount_id-' + paymentId + ' option:selected');

            let percentage = parseFloat(option.data('percentage')) || 0;

            let originalAmount = parseFloat("{{ $payment->amount }}");

            let discountAmount = originalAmount * (percentage / 100);

            let finalAmount = originalAmount - discountAmount;


            $('#discount_percentage-' + paymentId)
                .val(percentage.toFixed(2));


            $('#discount_amount-' + paymentId)
                .val(formatCurrency(discountAmount));


            $('#amount_with_discount-' + paymentId)
                .val(formatCurrency(finalAmount));


            if($('#apply_discount-' + paymentId).is(':checked')){

                $('#amount-' + paymentId)
                    .val(finalAmount.toFixed(2));

            }

        }


        $('#apply_discount-' + paymentId).on('change', function(){

            if($(this).is(':checked')){

                $('#discount_fields-' + paymentId).show();

                $('#discount_id-' + paymentId)
                    .prop('required', true);

            }else{

                $('#discount_fields-' + paymentId).hide();

                $('#discount_id-' + paymentId)
                    .prop('required', false)
                    .val('')
                    .trigger('change');


                $('#amount-' + paymentId)
                    .val("{{ $payment->amount }}");

            }


            updateDiscount();

        });


        $('#discount_id-' + paymentId)
            .on('change', updateDiscount);


        updateDiscount();


        $('#cancel-button-' + paymentId).on('click', function(){

            $('#edit-payment-form-' + paymentId)[0].reset();

        });


    });
</script>
