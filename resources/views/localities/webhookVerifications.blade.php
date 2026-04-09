@extends('adminlte::page')

@section('title', 'Códigos de Verificación Webhook')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-key text-primary"></i> Códigos de Verificación Webhook</h1>
        <div>
            <button type="button" class="btn btn-info" id="btn-refresh">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Instrucciones:</strong> Cuando configures un webhook en OpenPay, el sistema enviará un código de verificación a esta URL.
                Los códigos capturados aparecerán aquí. Copia el código y pégalo en el panel de OpenPay para completar la verificación.
            </div>
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-link"></i> URL del Webhook
                    </h3>
                </div>
                <div class="card-body">
                    <p class="mb-2">Usa esta URL al configurar el webhook en el panel de OpenPay:</p>
                    <div class="input-group">
                        <input type="text" class="form-control" id="webhook-url" value="{{ route('openpay.webhook') }}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="btn-copy-url">
                                <i class="fas fa-copy"></i> Copiar URL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Códigos Recibidos
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger btn-sm" id="btn-clear-all" {{ $verifications->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-trash"></i> Limpiar Todo
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($verifications->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No hay códigos de verificación capturados.</p>
                            <p class="text-muted small">Configura un webhook en OpenPay y el código aparecerá aquí.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="verifications-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 200px;">Código de Verificación</th>
                                        <th>Fecha/Hora del Evento</th>
                                        <th>Recibido</th>
                                        <th style="width: 150px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($verifications as $verification)
                                        <tr data-id="{{ $verification->id }}">
                                            <td>
                                                <code class="verification-code" style="font-size: 1.1em; background: #e9ecef; padding: 5px 10px; border-radius: 4px;">
                                                    {{ $verification->verification_code }}
                                                </code>
                                            </td>
                                            <td>
                                                @if($verification->event_date)
                                                    {{ $verification->event_date->format('d/m/Y H:i:s') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span title="{{ $verification->created_at->format('d/m/Y H:i:s') }}">
                                                    {{ $verification->created_at->diffForHumans() }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <button type="button" class="btn btn-success btn-sm btn-copy mr-1" data-code="{{ $verification->verification_code }}" title="Copiar código" style="min-width: 90px;">
                                                        <i class="fas fa-copy"></i> Copiar
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $verification->id }}" title="Eliminar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom-pagination.css') }}">
    <style>
        .verification-code {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 2px;
            font-weight: bold;
        }

        .btn-copy.copied {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .btn-copy.copied i {
            animation: pulse 0.3s;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.btn-copy', function() {
                var btn = $(this);
                var code = btn.data('code');

                navigator.clipboard.writeText(code).then(function() {
                    var originalHtml = btn.html();
                    btn.html('<i class="fas fa-check"></i> Copiado!').addClass('copied');

                    setTimeout(function() {
                        btn.html(originalHtml).removeClass('copied');
                    }, 2000);
                }).catch(function(err) {
                    var tempInput = $('<input>');
                    $('body').append(tempInput);
                    tempInput.val(code).select();
                    document.execCommand('copy');
                    tempInput.remove();

                    var originalHtml = btn.html();
                    btn.html('<i class="fas fa-check"></i> Copiado!').addClass('copied');
                    setTimeout(function() {
                        btn.html(originalHtml).removeClass('copied');
                    }, 2000);
                });
            });

            $('#btn-copy-url').on('click', function() {
                var btn = $(this);
                var url = $('#webhook-url').val();

                navigator.clipboard.writeText(url).then(function() {
                    var originalHtml = btn.html();
                    btn.html('<i class="fas fa-check"></i> Copiado!');
                    setTimeout(function() {
                        btn.html(originalHtml);
                    }, 2000);
                });
            });

            $(document).on('click', '.btn-delete', function() {
                var btn = $(this);
                var id = btn.data('id');
                var row = btn.closest('tr');

                $.ajax({
                    url: '{{ url("openpay/webhook-verifications") }}/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function() {
                        row.fadeOut(300, function() { 
                            $(this).remove();
                            if ($('#verifications-table tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                    },
                    error: function() {
                        alert('Error al eliminar');
                    }
                });
            });

            $('#btn-clear-all').on('click', function() {
                if (!confirm('¿Estás seguro de eliminar todos los códigos de verificación?')) {
                    return;
                }

                $.ajax({
                    url: '{{ route("openpay.webhook.verifications.clear") }}',
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function() {
                        location.reload();
                    },
                    error: function() {
                        alert('Error al limpiar');
                    }
                });
            });

            $('#btn-refresh').on('click', function() {
                location.reload();
            });

            setInterval(function() {
                $.ajax({
                    url: '{{ route("openpay.webhook.verifications.api") }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.verifications && response.verifications.length > 0) {
                            var currentCount = $('#verifications-table tbody tr').length;
                            if (response.verifications.length !== currentCount) {
                                location.reload();
                            }
                        }
                    }
                });
            }, 10000);
        });
    </script>
@endsection
