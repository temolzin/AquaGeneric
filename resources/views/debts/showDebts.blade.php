<div class="modal fade" id="viewDebts{{ $debt->customer->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $debt->customer->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-primary">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información de Deudas del Usuario</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                        <label>ID del Usuario</label>
                                        <input type="text" disabled class="form-control" value="{{ $debt->customer->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <label>Nombre del Usuario</label>
                                        <input type="text" disabled class="form-control" value="{{ $debt->customer->name }} {{ $debt->customer->last_name }}" />
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h5>Deudas Asociadas</h5>

                            <div class="form-group">
                                <input type="text" id="searchDebt{{ $debt->customer->id }}" class="form-control search-input" placeholder="🔍 Buscar por ID, monto, estado o fechas...">
                            </div>
                            <div class="debt-list overflow-auto" style="max-height: 300px;">
                                @foreach ($debt->customer->debts as $customerDebt)
                                    <div class="debt-item card mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <p><strong>ID:</strong> {{ $customerDebt->id }}</p>  
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($customerDebt->start_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}
                                                            <p><strong>Fecha de Fin:</strong> {{ \Carbon\Carbon::parse($customerDebt->end_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}</p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <p><strong>Monto:</strong> ${{ number_format($customerDebt->amount, 2) }}</p>
                                                        </div>                                              
                                                        <div class="col-md-2">
                                                            <p><strong>Status:</strong> 
                                                                @if ($customerDebt->status === 'pending')
                                                                    <button class="btn btn-danger btn-xs">No pagada</button>
                                                                @elseif ($customerDebt->status === 'partial')
                                                                    <button class="btn btn-warning btn-xs">Abonada</button>
                                                                @elseif ($customerDebt->status === 'paid')
                                                                    <button class="btn btn-success btn-xs">Pagada</button>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="col-md-2 text-right">
                                                            <div class="btn-group" role="group" aria-label="Opciones">
                                                                <button type="button" class="btn btn-info btn-sm mr-2" data-toggle="modal" title="Ver Detalles" data-target="#view{{ $customerDebt->id }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @can('deleteDebt')
                                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" title="Eliminar Registro" data-target="#delete{{ $customerDebt->id }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                                @endcan
                                                            </div>
                                                        </div>
                                                        @include('debts.delete')
                                                        @include('debts.show')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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

<style>
    .search-input:focus {
        border-color: #80bdff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
        background-color: #ffffff;
    }
</style>

<script>
    document.getElementById('searchDebt{{ $debt->customer->id }}').addEventListener('input', function() {
        let searchValue = this.value.toLowerCase();
        let debtItems = document.querySelectorAll('#viewDebts{{ $debt->customer->id }} .debt-item');

        debtItems.forEach(function(item) {
            let id = item.querySelector('.col-md-1 p').innerText.toLowerCase();
            let amount = item.querySelector('.col-md-2 p').innerText.toLowerCase();
            let status = item.querySelector('.col-md-2 p button').innerText.toLowerCase();
            let startDate = item.querySelector('.col-md-6 p').innerText.toLowerCase();
            let endDate = item.querySelector('.col-md-6 p + p').innerText.toLowerCase();

            if (id.includes(searchValue) || amount.includes(searchValue) || status.includes(searchValue) || startDate.includes(searchValue) || endDate.includes(searchValue)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
