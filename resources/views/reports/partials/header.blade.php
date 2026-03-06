<header>
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:80px; vertical-align:top;">
                @if(!empty($logoPath) && file_exists($logoPath))
                    <img src="file://{{ $logoPath }}" style="width:70px; height:70px; border-radius:50%;">
                @else
                    <div style="width:70px; height:70px; border:1px solid #ccc; border-radius:50%; text-align:center; line-height:70px;">
                        LOGO
                    </div>
                @endif
            </td>
            <td style="vertical-align:top;">
                <div style="font-weight:bold; font-size:12px;">
                    COMITÉ DEL SISTEMA DE AGUA POTABLE
                </div>

                @if(!empty($localityLine))
                    <div style="font-size:10px; margin-top:2px;">
                        {{ $localityLine }}
                    </div>
                @endif

                <div style="font-size:10px; margin-top:6px;">
                    <strong>Reporte:</strong> {{ $reportTitle ?? 'Reporte' }} |
                    <strong>Generado:</strong> {{ $generatedAt ?? now()->format('d/m/Y H:i') }}
                    @if(!empty($generatedBy))
                        | <strong>Por:</strong> {{ $generatedBy }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <hr style="border:none; border-top:1px solid #d0d0d0; margin:12px 0 0;">
</header>
