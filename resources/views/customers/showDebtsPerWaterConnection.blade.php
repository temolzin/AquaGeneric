<div class="modal fade" id="showDebtsPerWaterConnection{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel{{ $customer->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-primary">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Información de Deudas del Cliente</h4>
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
                                        <label>ID del Cliente</label>
                                        <input type="text" disabled class="form-control" value="{{ $customer->id }}" />
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <label>Nombre del Cliente</label>
                                        <input type="text" disabled class="form-control" value="{{ $customer->name }} {{ $customer->last_name }}" />
                                    </div>
                                </div>
                                @php
                                    $unpaidDebts = $customer->waterConnections->flatMap->debts->where('status', '!=', 'paid');
                                    $totalDebt = $unpaidDebts->sum('amount');
                                    $totalPaid = $unpaidDebts->sum('debt_current');
                                    $pendingBalance = $totalDebt - $totalPaid;
                                @endphp
                                <div class="col-lg-12">
                                    @if ($pendingBalance > 0)
                                        <div class="info-box">
                                            <span class="info-box-icon bg-danger"><i class="fa fa-dollar-sign"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Saldo Total Pendiente</span>
                                                <span class="info-box-number">${{ number_format($pendingBalance, 2, '.', ',') }}</span>
                                            </div>
                                        </div>
                                    @else
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fa fa-dollar-sign"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Sin Deudas</span>
                                            <span class="info-box-number">Este cliente no tiene deudas pendientes</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <h5>Deudas Asociadas Por Tomas</h5>
                            <div class="form-group">
                                <input type="text" id="searchDebt{{ $customer->id }}" class="form-control search-input" placeholder="🔍 Buscar por ID, toma, monto, estado o fechas...">
                            </div>
                            <div class="debt-list" style="overflow-y: auto; max-height: 300px; overflow-x: hidden;">
                                @php $connectionCounter = 0; @endphp
                                @foreach ($customer->waterConnections as $waterConnection)
                                    @if ($waterConnection->debts->isNotEmpty())
                                        @php $connectionCounter++; @endphp
                                        @if ($connectionCounter > 1)
                                            <hr style="border: none; border-top: 4px solid rgba(8, 124, 252, 0.8); margin-top: 20px; margin-bottom: 20px; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">
                                        @endif
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>ID de la Toma</label>
                                                    <input type="text" disabled class="form-control" value="{{ $waterConnection->id }}" />
                                                </div>
                                            </div>
                                            <div class="col-lg-10 pl-lg-3">
                                                <div class="form-group">
                                                    <label>Nombre de la Toma</label>
                                                    <input type="text" disabled class="form-control" value="{{ $waterConnection->name }}" />
                                                </div>
                                            </div>
                                        </div>
                                        @foreach ($waterConnection->debts as $waterConnectionDebt)
                                            <div class="debt-item card mb-3">
                                                <div class="card-body">
                                                    <div class="row no-gutters">
                                                        <div class="col-md-1">
                                                            <p><strong>ID:</strong> {{ $waterConnectionDebt->id }}</p>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <p><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($waterConnectionDebt->start_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}</p>
                                                                    <p><strong>Fecha de Fin:</strong> {{ \Carbon\Carbon::parse($waterConnectionDebt->end_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}</p>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <p><strong>Monto:</strong> ${{ number_format($waterConnectionDebt->amount, 2) }}</p>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <p><strong>Pendiente:</strong> ${{ number_format($waterConnectionDebt->amount - $waterConnectionDebt->debt_current, 2) }}</p>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <p><strong>Status:</strong>
                                                                        @if ($waterConnectionDebt->status === 'pending')
                                                                            <button class="btn btn-danger btn-xs">No pagada</button>
                                                                        @elseif ($waterConnectionDebt->status === 'partial')
                                                                            <button class="btn btn-warning btn-xs">Abonada</button>
                                                                        @elseif ($waterConnectionDebt->status === 'paid')
                                                                            <button class="btn btn-success btn-xs">Pagada</button>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="btn-group" role="group" aria-label="Opciones">
                                                                        <button type="button" class="btn btn-info btn-sm mr-2" data-toggle="modal" title="Ver Detalles" data-target="#viewDebt{{ $waterConnectionDebt->id }}">
                                                                            <i class="fas fa-eye"></i>
                                                                        </button>
                                                                        <a type="button" class="btn btn-block bg-gradient-secondary mr-2" target="_blank" title="Generar Historial de Pagos"
                                                                            href="{{ route('reports.paymentHistoryReport', Crypt::encrypt($waterConnectionDebt->id)) }}">
                                                                            <i class="fas fa-file-invoice"></i>
                                                                        </a>
                                                                        @can('deleteDebt')
                                                                            @if($waterConnectionDebt->hasDependencies() && $waterConnectionDebt->status !== 'paid')
                                                                                <button type="button" class="btn btn-secondary mr-2" data-toggle="modal" title="Eliminación no permitida: Existen datos relacionados con este registro." disabled>
                                                                                    <i class="fas fa-trash-alt"></i>
                                                                                </button>
                                                                            @else
                                                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" title="Eliminar Registro" data-target="#deleteDebt{{ $waterConnectionDebt->id }}">
                                                                                    <i class="fas fa-trash-alt"></i>
                                                                                </button>
                                                                            @endif
                                                                        @endcan
                                                                    </div>
                                                                </div>
                                                                @include('debts.delete', ['debt' => $waterConnectionDebt])
                                                                @include('debts.show', ['debt' => $waterConnectionDebt])
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                                @if ($customer->waterConnections->every(fn($wc) => $wc->debts->isEmpty()))
                                    <p>No hay deudas asociadas a ninguna de las tomas.</p>
                                @endif
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
    document.getElementById('searchDebt{{ $customer->id }}').addEventListener('input', function() {
        let searchValue = this.value.toLowerCase();
        let debtItems = document.querySelectorAll('#showDebtsPerWaterConnection{{ $customer->id }} .debt-item');

        debtItems.forEach(function(item) {
            let id = item.querySelector('.col-md-1 p').innerText.toLowerCase();
            let amount = item.querySelector('.col-md-2 p').innerText.toLowerCase();
            let status = item.querySelector('.col-md-2 p button').innerText.toLowerCase();
            let startDate = item.querySelector('.col-md-4 p').innerText.toLowerCase();
            let endDate = item.querySelector('.col-md-4 p + p').innerText.toLowerCase();

            if (id.includes(searchValue) || amount.includes(searchValue) || status.includes(searchValue) || startDate.includes(searchValue) || endDate.includes(searchValue)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
