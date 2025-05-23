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
                                <input type="text" id="searchDebt{{ $customer->id }}" class="form-control search-input" placeholder="🔍 Buscar por ID o nombre de la toma...">
                            </div>
                            <div class="debt-list" style="overflow-y: auto; max-height: 300px; overflow-x: hidden;">
                                @foreach ($customer->waterConnections as $waterConnection)
                                    @if ($waterConnection->debts->isNotEmpty())
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>ID de la Toma</label>
                                                    <input type="text" disabled class="form-control" value="{{ $waterConnection->id }}" />
                                                </div>
                                            </div>
                                            <div class="col-lg-8 pl-lg-3">
                                                <div class="form-group">
                                                    <label>Nombre de la Toma</label>
                                                    <input type="text" disabled class="form-control" value="{{ $waterConnection->name }}" />
                                                </div>
                                            </div>
                                            <div class="col-lg-2 text-right">
                                                <button class="btn btn-sm btn-primary toggle-debts" title="Ver Deudas" data-target="#debts-{{ $waterConnection->id }}">
                                                    <i class="fas fa-chevron-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="debt-details" id="debts-{{ $waterConnection->id }}" style="display: none; margin-left: 15px;">
                                            @foreach ($waterConnection->debts as $waterConnectionDebt)
                                                <div class="debt-item card mb-3 mx-0">
                                                    <div class="card-body">
                                                        <div class="row no-gutters">
                                                            <div class="col-12 col-md-1 mb-2 mb-md-0">
                                                                <p><strong>ID:</strong> {{ $waterConnectionDebt->id }}</p>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-12 col-md-4">
                                                                        <p><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($waterConnectionDebt->start_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}</p>
                                                                        <p><strong>Fecha de Fin:</strong> {{ \Carbon\Carbon::parse($waterConnectionDebt->end_date)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}</p>
                                                                    </div>
                                                                    <div class="col-6 col-md-2">
                                                                        <p><strong>Monto:</strong> ${{ number_format($waterConnectionDebt->amount, 2) }}</p>
                                                                    </div>
                                                                    <div class="col-6 col-md-2">
                                                                        <p><strong>Pendiente:</strong> ${{ number_format($waterConnectionDebt->amount - $waterConnectionDebt->debt_current, 2) }}</p>
                                                                    </div>
                                                                    <div class="col-6 col-md-2">
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
                                                                    <div class="col-12 col-md-2 text-right">
                                                                        <div class="btn-group" role="group" aria-label="Opciones">
                                                                            <button type="button" class="btn btn-info btn-sm mr-2" data-toggle="modal" title="Ver Detalles" data-target="#viewDebt{{ $waterConnectionDebt->id }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </button>
                                                                            <a type="button" class="btn btn-block bg-gradient-secondary btn-sm mr-2" target="_blank" title="Generar Historial de Pagos"
                                                                                href="{{ route('reports.paymentHistoryReport', Crypt::encrypt($waterConnectionDebt->id)) }}">
                                                                                <i class="fas fa-file-invoice"></i>
                                                                            </a>
                                                                            @can('deleteDebt')
                                                                                @if($waterConnectionDebt->hasDependencies() && $waterConnectionDebt->status !== 'paid')
                                                                                    <button type="button" class="btn btn-secondary btn-sm mr-2 data-toggle="modal" title="Eliminación no permitida: Existen datos relacionados con este registro." disabled>
                                                                                        <i class="fas fa-trash-alt"></i>
                                                                                    </button>
                                                                                @else
                                                                                    <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" title="Eliminar Registro" data-target="#deleteDebt{{ $waterConnectionDebt->id }}">
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
                                        </div>
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
    document.getElementById('searchDebt{{ $customer->id }}').addEventListener('input', function () {
        let searchValue = this.value.toLowerCase();
        let connections = document.querySelectorAll('#showDebtsPerWaterConnection{{ $customer->id }} .row.no-gutters.align-items-center');

        connections.forEach(function (connection) {
            let connectionId = connection.querySelector('.col-lg-2 input')?.value.toLowerCase() || '';
            let connectionName = connection.querySelector('.col-lg-8 input')?.value.toLowerCase() || '';

            let debtItems = connection.nextElementSibling.querySelectorAll('.debt-item');
            let hasMatch = false;

            debtItems.forEach(function (item) {
                let id = item.querySelector('.col-md-1 p')?.textContent?.toLowerCase() || '';
                let amount = item.querySelector('.col-md-2 p')?.textContent?.toLowerCase() || '';
                let status = item.querySelector('.col-md-2 p button')?.textContent?.toLowerCase() || '';
                let startDate = item.querySelector('.col-md-4 p')?.textContent?.toLowerCase() || '';
                let endDate = item.querySelector('.col-md-4 p + p')?.textContent?.toLowerCase() || '';

                if (
                    id.includes(searchValue) ||
                    amount.includes(searchValue) ||
                    status.includes(searchValue) ||
                    startDate.includes(searchValue) ||
                    endDate.includes(searchValue) ||
                    connectionId.includes(searchValue) ||
                    connectionName.includes(searchValue)
                ) {
                    item.style.display = '';
                    hasMatch = true;
                } else {
                    item.style.display = 'none';
                }
            });

            if (hasMatch || connectionId.includes(searchValue) || connectionName.includes(searchValue)) {
                connection.style.display = '';
                connection.nextElementSibling.style.display = 'none';
                connection.querySelector('button.toggle-debts i').classList.remove('fa-chevron-up');
                connection.querySelector('button.toggle-debts i').classList.add('fa-chevron-down');
            } else {
                connection.style.display = 'none';
                connection.nextElementSibling.style.display = 'none';
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.debt-details').forEach(function (details) {
            details.style.display = 'none';
        });
    });

    document.querySelector('#showDebtsPerWaterConnection{{ $customer->id }}').addEventListener('click', function(event) {
        const button = event.target.closest('button.toggle-debts');
        if (button) {
            const targetId = button.getAttribute('data-target');
            const details = document.querySelector(targetId);
            const icon = button.querySelector('i');

            if (details.style.display === 'none') {
                details.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                details.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    });
</script>
