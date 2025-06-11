<div class="modal fade" id="waterConnections{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="waterConnectionsModalLabel{{ $customer->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-info">
                <div class="card-header card-header-custom bg-purple">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Tomas De Agua De {{ $customer->name }} {{ $customer->last_name }}</h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 text-center mb-4">
                                    <div class="form-group">
                                        @if ($customer->getFirstMediaUrl('customerGallery'))
                                        <img src="{{ $customer->getFirstMediaUrl('customerGallery') }}" alt="Foto del Cliente" class="img-fluid rounded-circle" style="width: 120px; height: 120px;">
                                        @else
                                        <img src="{{ asset('img/userDefault.png') }}" alt="Foto del Usuario" class="img-fluid rounded-circle" style="width: 120px; height: 120px;">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>ID</label>
                                        <input type="text" class="form-control" value="{{ $customer->id }}" disabled />
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" value="{{ $customer->name }} {{ $customer->last_name }}" disabled />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <hr>
                                    <h5><strong>Tomas de Agua Asociadas</strong></h5><br>
                                </div>
                            </div>
                            @if (count($customer->waterConnections) <= 0)
                            <div class="row">
                                <div class="col-lg-12">
                                    Este cliente aún no tiene tomas de agua asociadas.
                                </div>
                            </div>
                            @endif
                            <div class="water-connection-list" style="overflow-y: auto; max-height: 300px; overflow-x: hidden;">
                                @php $connectionCounter = 0; @endphp
                                @foreach ($customer->waterConnections as $waterConnection)
                                    @if ($waterConnection)
                                        @php $connectionCounter++; 
                                            $today = \Carbon\Carbon::today();

                                            $debts = $waterConnection->debts;
                                            $unpaidDebts = $debts->where('status', '!=', \App\Models\Debt::STATUS_PAID);
                                            $hasDebt = $unpaidDebts->isNotEmpty();

                                            $futurePaidDebts = $debts->filter(function ($debt) use ($today) {
                                                return $debt->status === \App\Models\Debt::STATUS_PAID && \Carbon\Carbon::parse($debt->start_date)->gt($today);
                                            });
                                            $hasAdvance = $futurePaidDebts->isNotEmpty();

                                            if ($waterConnection->status === 'suspendido') {
                                                $status = 'suspender';
                                            } elseif ($hasDebt) {
                                                $status = 'adeudo';
                                            } elseif ($hasAdvance) {
                                                $status = 'adelantado';
                                            } else {
                                                $status = 'pagado';
                                            }

                                            $statusLabels = [
                                                'pagado' => 'Pagado',
                                                'adeudo' => 'Adeudo',
                                                'adelantado' => 'Adelantado',
                                                'suspender' => 'Suspendida',
                                            ];

                                            $statusStyles = [
                                                'pagado' => 'background-color: #28a745; color: white;',
                                                'adeudo' => 'background-color: #dc3545; color: white;',
                                                'adelantado' => 'background-color: #6f42c1; color: white;',
                                                'suspender' => 'background-color: #6c757d; color: white;',
                                            ];
                                        @endphp

                                        @if ($connectionCounter > 1)
                                            <hr style="border: none; border-top: 2px solid rgba(112, 68, 196, 0.8); box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">
                                        @endif

                                        <div class="row no-gutters align-items-center">
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>ID de la Toma</label>
                                                    <p>{{ $waterConnection->id }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <div class="form-group">
                                                    <label>Nombre de la Toma</label>
                                                    <p>{{ $waterConnection->name }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Estatus</label>
                                                    <div>
                                                        <div style="display: inline-block; padding: 5px 10px; border-radius: 8px; font-weight: bold; font-size: 0.85rem; text-align: center; {!! $statusStyles[$status] !!}">
                                                            {{ $statusLabels[$status] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group" aria-label="Opciones">
                                                    <button type="button" class="btn bg-purple btn-sm mr-2" data-toggle="modal" title="Ver Detalles" data-target="#waterConnectionDetails{{ $waterConnection->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @include('customers.waterConnectionDetails', ['waterConnection' => $waterConnection])
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
