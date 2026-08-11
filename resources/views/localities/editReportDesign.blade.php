<div class="modal fade" id="editReportDesign{{ $locality->id }}" tabindex="-1" role="dialog" aria-labelledby="editReportDesignLabel{{ $locality->id }}" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="card-header" style="background-color: #4169E1; color: #ffffff; border-radius: 4px 4px 0 0;">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Diseño de Reportes <small> &nbsp;</small></h4>
                    <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form action="{{ route('localities.updateReportDesign', $locality->id) }}" method="POST" id="report-design-form-{{ $locality->id }}">
                @csrf
                <div class="card-body text-center">
                    <p class="text-muted">Elige si esta localidad usará el diseño nuevo de reportes o el diseño estándar del sistema.</p>
                    <div class="mb-3 d-flex justify-content-end">
                        <label class="switch">
                            <input type="checkbox" id="report-switch-{{ $locality->id }}" name="report_switch"
                                {{ $locality->use_new_report_design ? 'checked' : '' }} onchange="handleReportSwitchChange({{ $locality->id }})">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <img id="report-preview-{{ $locality->id }}" src="{{ $locality->use_new_report_design ? asset('img/backgroundnewreport.png') . '?t=' . time() : asset('img/backgroundReport.png') . '?t=' . time() }}" alt="Vista previa diseño" style="width:260px; height:auto; border-radius:8px;">
                    </div>
                    <input type="hidden" name="use_new_report_design" id="use-new-report-design-{{ $locality->id }}" value="{{ $locality->use_new_report_design ? '1' : '0' }}">
                    <p class="text-secondary">Al activar el switch se aplicará el nuevo diseño de reportes para esta localidad. Al desactivarlo se usará el diseño por defecto del sistema.</p>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="report-submit-{{ $locality->id }}" class="btn" style="background-color: #4169E1; color: #fff; border: none;">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}
.switch input { display:none; }
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 34px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider { background-color: #4169E1; }
input:checked + .slider:before { transform: translateX(26px); }
</style>

<script>
function handleReportSwitchChange(id) {
    const checkbox = document.getElementById('report-switch-' + id);
    const isActive = !!checkbox.checked;
    const img = document.getElementById('report-preview-' + id);
    const hidden = document.getElementById('use-new-report-design-' + id);

    const newSrc = isActive ? '{{ asset('img/backgroundnewreport.png') }}' : '{{ asset('img/backgroundReport.png') }}';
    img.src = newSrc + '?t=' + Date.now();

    hidden.value = isActive ? '1' : '0';
}

$('#editReportDesign{{ $locality->id }}').on('shown.bs.modal', function () {
    handleReportSwitchChange({{ $locality->id }});
});
</script>
