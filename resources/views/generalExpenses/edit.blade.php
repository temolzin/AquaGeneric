<div class="modal fade" id="edit{{ $expense->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-warning">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Gasto <small> &nbsp;(*) Campos requeridos</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <form action="{{ route('generalExpenses.update', $expense->id) }}" enctype="multipart/form-data" method="post" id="edit-generalExpense-form-{{ $expense->id }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="concept" class="form-label">Concepto(*)</label>
                                            <input type="text" class="form-control" name="conceptUpdate" placeholder="Ingresa el concepto del gasto" value="{{ $expense->concept }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label">Descripción(*)</label>
                                            <textarea class="form-control" name="descriptionUpdate" placeholder="Ingresa una descripción" required>{{ $expense->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="amount" class="form-label">Monto(*)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-dollar-sign"></i></span>
                                                </div>
                                                <input type="number" min="1" class="form-control" name="amountUpdate"
                                                placeholder="Ingresa el monto" value="{{ $expense->amount }}" required />
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="typeUpdate" class="form-label">Tipo(*)</label>
                                            <select id="typeUpdate" class="form-control select2" name="typeUpdate" required>
                                                <option value="">Selecciona el tipo de gasto</option>
                                                <option value="mainteinence" {{ $expense->type === 'mainteinence' ? 'selected' : '' }}>Mantenimiento</option>
                                                <option value="services" {{ $expense->type === 'services' ? 'selected' : '' }}>Servicios</option>
                                                <option value="supplies" {{ $expense->type === 'supplies' ? 'selected' : '' }}>Insumos</option>
                                                <option value="taxes" {{ $expense->type === 'taxes' ? 'selected' : '' }}>Impuestos</option>
                                                <option value="staff" {{ $expense->type === 'staff' ? 'selected' : '' }}>Personal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="expenseDate" class="form-label">Fecha del gasto(*)</label>
                                            <input type="date" class="form-control" name="expenseDateUpdate" value="{{ $expense->expense_date }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="receiptUpdate" class="form-label">Comprobante del gasto (*)</label>
                                            <div class="d-flex align-items-center">
                                                @if($expense->hasMedia('expenseGallery'))
                                                    <div class="col-lg-6">
                                                        <a href="{{ $expense->getFirstMediaUrl('expenseGallery') }}" target="_blank" class="btn btn-warning">
                                                            <i class="fas fa-eye"></i> Ver recibo actual
                                                        </a>
                                                    </div>
                                                @else
                                                    <p class="mt-2 text-danger">No hay recibo actual.</p>
                                                @endif
                                                <div class="col-lg-6">
                                                    <input type="file" class="form-control" name="receiptUpdate" id="receiptUpdate" accept=".jpg,.jpeg,.png,.pdf"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm({{ $expense->id }})">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('receiptUpdate').addEventListener('change', function (event) {
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
