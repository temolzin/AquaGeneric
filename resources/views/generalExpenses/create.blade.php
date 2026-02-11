
<div class="modal fade" id="createGeneralExpenses" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-success">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Ingrese los Datos del Gasto <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{ route('generalExpenses.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="concept" class="form-label">Concepto(*)</label>
                                            <input type="text" class="form-control" name="concept" placeholder="Ingresa el concepto del gasto" value="{{ old('concept') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción(*)</label>
                                            <textarea class="form-control" name="description" placeholder="Ingresa una descripción del gasto" value="{{ old('description') }}" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="amount" class="form-label">Monto(*)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                                </div>
                                                <input type="number" min="1" class="form-control" name="amount" placeholder="Ingresa el monto" value="{{ old('amount') }}" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="expense_type_id" class="form-label">Tipo(*)</label>
                                            <select id="expense_type_id" class="form-control select2" name="expense_type_id" required>
                                                <option value="">Selecciona el tipo de gasto</option>
                                                @foreach($expenseTypes as $expenseType)
                                                    <option value="{{ $expenseType->id }}" style="color: {{ $expenseType->color }};">
                                                        {{ $expenseType->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="expenseDate" class="form-label">Fecha del gasto(*)</label>
                                            <input type="date" class="form-control" name="expenseDate" value="{{ old('expense_date') }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="receipt" class="form-label">Comprobante del gasto(*)</label>
                                            <input type="file" class="form-control" name="receipt" id="receipt" accept=".jpg,.jpeg,.png,.pdf" required />
                                        </div>
                                    </div>
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
    document.getElementById('receipt').addEventListener('change', function (event) {
        const input = event.target;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        const file = input.files[0];

        if (file) {
            const fileType = file.type;
            const fileName = file.name.toLowerCase();
            const validExtensions = ['.jpg', '.jpeg', '.png', '.pdf'];
            const isValidExtension = validExtensions.some(ext => fileName.endsWith(ext));

            if (!allowedTypes.includes(fileType) || !isValidExtension) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor, sube un archivo PDF o una imagen (jpg, jpeg, png).',
                    confirmButtonText: 'Aceptar'
                });
                input.value = '';
                return;
            }
        }
    });
</script>
