<div class="modal fade" id="editPdfBackground{{ $locality->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-navy">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title">Editar Fondos de Reportes <small> &nbsp;</small></h4>
                        <button type="button" class="close d-sm-inline-block text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <form action="{{ route('localities.updatePdfBackground', $locality->id) }}" enctype="multipart/form-data" method="POST" id="edit-locality-form-{{ $locality->id }}">
                    @csrf
                    @method('POST')
                    <div class="card-body">
                        <div class="card mb-3">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Fondo Reporte Vertical</h3><br>
                                <small class="text-light">Formato sugerido</small>
                            </div>
                            <div class="card-body text-center">
                                <img id="preview-vertical-{{ $locality->id }}"
                                    src="{{ $locality->getFirstMediaUrl('pdfBackgroundVertical') ?: asset('img/backgroundReport.png') }}"
                                    alt="Fondo Vertical" style="width: 200px; height: 280px; border-radius: 10px; margin-bottom: 10px;">
                                <input type="file" accept="image/*" name="pdf_background_vertical"
                                    class="form-control" onchange="previewImageEdit(event, 'vertical', {{ $locality->id }})">
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header py-2 bg-secondary">
                                <h3 class="card-title">Fondo Reporte Horizontal </h3><br>
                                <small class="text-light">Formato sugerido</small>
                            </div>
                            <div class="card-body text-center">
                                <img id="preview-horizontal-{{ $locality->id }}"
                                    src="{{ $locality->getFirstMediaUrl('pdfBackgroundHorizontal') ?: asset('img/customersBackgroundHorizontal.png') }}"
                                    alt="Fondo Horizontal" style="width: 280px; height: 200px; border-radius: 10px; margin-bottom: 10px;">
                                <input type="file" accept="image/*" name="pdf_background_horizontal"
                                    class="form-control" onchange="previewImageEdit(event, 'horizontal', {{ $locality->id }})">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetForm({{ $locality->id }})">Cancelar</button>
                        <button type="submit" class="btn bg-navy">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function previewImageEdit(event, type, id) {
    var input = event.target;
    var file = input.files[0];
    var reader = new FileReader();

    if (!file.type.startsWith('image/')) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, sube un archivo de imagen',
            confirmButtonText: 'Aceptar'
        });
        input.value = '';
        return;
    }

    reader.onload = function() {
        document.getElementById('preview-' + type + '-' + id).src = reader.result;
    };
    reader.readAsDataURL(file);
}

function resetForm(id) {
    document.getElementById('edit-locality-form-' + id).reset();
    document.getElementById('preview-vertical-' + id).src = "{{ asset('img/backgroundReport.png') }}";
    document.getElementById('preview-horizontal-' + id).src = "{{ asset('img/customersBackgroundHorizontal.png') }}";
}
</script>
