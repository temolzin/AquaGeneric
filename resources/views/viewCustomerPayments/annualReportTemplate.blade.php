<div class="modal fade" id="annualModal" tabindex="-1" role="dialog" aria-labelledby="annualModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom bg-success">
                <h5 class="modal-title" id="annualModalLabel">
                    <i class="fas fa-chart-line mr-2"></i>Reporte Anual de Pagos
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="annualForm" method="GET" action="{{ route('viewCustomerPayments.annualReportCustomerPayments') }}" target="_blank">
                    @csrf
                    <div class="form-group">
                        <label for="annualYear"><strong>Año:</strong></label>
                        <select class="form-control" id="annualYear" name="year" required>
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
                    <p class="text-muted small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Este reporte mostrará todos los pagos realizados durante el año seleccionado.
                    </p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn btn-success" onclick="generateAnnualReport()">
                    <i class="fas fa-file-pdf mr-1"></i>Generar Reporte
                </button>
            </div>
        </div>
    </div>
</div>
