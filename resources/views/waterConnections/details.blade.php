<div class="modal fade"
     id="details{{ $connection->id }}"
     tabindex="-1"
     role="dialog"
     aria-hidden="true"
     data-connection-id="{{ $connection->id }}">

    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="card-info mb-0">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">
                            Detalles de la toma
                            <small class="d-block text-white-50">
                                Toma: {{ $connection->name }} | ID: {{ $connection->id }}
                            </small>
                        </h4>

                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="modal-body">

                    {{-- Tabs --}}
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active"
                               id="history-tab-{{ $connection->id }}"
                               data-toggle="tab"
                               href="#history-{{ $connection->id }}"
                               role="tab">
                                Historial
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link"
                               id="debts-tab-{{ $connection->id }}"
                               data-toggle="tab"
                               href="#debts-{{ $connection->id }}"
                               role="tab">
                                Deudas
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">
                        {{-- Historial --}}
                        <div class="tab-pane fade show active"
                             id="history-{{ $connection->id }}"
                             role="tabpanel">
                            <div id="historyContent{{ $connection->id }}">
                                <div class="text-muted">
                                    Cargando historial...
                                </div>
                            </div>
                        </div>

                        {{-- Deudas (placeholder para PR futuro) --}}
                        <div class="tab-pane fade"
                             id="debts-{{ $connection->id }}"
                             role="tabpanel">
                            <div class="alert alert-secondary mb-0">
                                Próximamente: aquí se mostrará el tab de deudas.
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
