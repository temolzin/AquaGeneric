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
                                <small class="text-light">Peso máximo: 2MB<br>
                            Dimensiones máximas: No mayor a 1600 x 2000
                                </small>
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
                                <small class="text-light">Peso máximo: 2MB<br>
                            Dimensiones máximas: No mayor a 2000 x 1600
                                </small>
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
    const input = event.target;
    const file = input.files[0];

    if (!file) return;

    const maxSize = 2 * 1024 * 1024;
    const maxVertical = { width: 1600, height: 2000 };
    const maxHorizontal = { width: 2000, height: 1600 };

    if (!file.type.startsWith('image/')) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo no válido',
            text: 'Por favor, sube un archivo de imagen (JPG, PNG, etc.)',
            confirmButtonText: 'Aceptar'
        });
        input.value = '';
        return;
    }

    if (file.size > maxSize) {
        Swal.fire({
            icon: 'error',
            title: 'Tamaño excedido',
            text: 'El archivo no debe superar los 2 MB.',
            confirmButtonText: 'Aceptar'
        });
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const width = img.width;
            const height = img.height;

            if (type === 'vertical') {
                if (width > maxVertical.width || height > maxVertical.height) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Dimensiones inválidas',
                        text: `El fondo vertical no debe exceder ${maxVertical.width}x${maxVertical.height}px. Imagen cargada: ${width}x${height}px.`,
                        confirmButtonText: 'Aceptar'
                    });
                    input.value = '';
                    return;
                }
            }

            if (type === 'horizontal') {
                if (width > maxHorizontal.width || height > maxHorizontal.height) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Dimensiones inválidas',
                        text: `El fondo horizontal no debe exceder ${maxHorizontal.width}x${maxHorizontal.height}px. Imagen cargada: ${width}x${height}px.`,
                        confirmButtonText: 'Aceptar'
                    });
                    input.value = '';
                    return;
                }
            }

            document.getElementById('preview-' + type + '-' + id).src = e.target.result;
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function resetForm(id) {
    document.getElementById('edit-locality-form-' + id).reset();
    document.getElementById('preview-vertical-' + id).src = "{{ asset('img/backgroundReport.png') }}";
    document.getElementById('preview-horizontal-' + id).src = "{{ asset('img/customersBackgroundHorizontal.png') }}";
}
</script>
