<div class="modal fade" id="quarterModal" tabindex="-1" role="dialog" aria-labelledby="quarterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom bg-purple">
                <h5 class="modal-title" id="quarterModalLabel">
                    <i class="fas fa-chart-bar mr-2"></i>Reporte Trimestral de Pagos
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="quarterForm" method="GET" action="{{ route('viewCustomerPayments.quarterlyReport') }}" target="_blank">
                    @csrf
                    <div class="form-group">
                        <label for="year"><strong>Año:</strong></label>
                        <select class="form-control" id="year" name="year" required>
                            <option value="">Selecciona un año</option>
                            @php
                                $currentYear = date('Y');
                                $startYear = 2020;
                            @endphp
                            @for($year = $currentYear; $year >= $startYear; $year--)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quarter"><strong>Trimestre:</strong></label>
                        <select class="form-control" id="quarter" name="quarter" required>
                            <option value="">Selecciona un trimestre</option>
                            <option value="1">Enero - Marzo</option>
                            <option value="2">Abril - Junio</option>
                            <option value="3">Julio - Septiembre</option>
                            <option value="4">Octubre - Diciembre</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn bg-purple" onclick="generateQuarterlyReport()">
                    Generar Reporte
                </button>
            </div>
        </div>
    </div>
</div>
