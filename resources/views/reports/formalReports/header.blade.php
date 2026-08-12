<header>
    <table style="width:100%; border-collapse:collapse; border:1px solid #666; background:#fff; margin:0 auto;">
        <tr>
            <td style="vertical-align:middle; text-align:left; padding:12px 16px 10px 16px;">
                <div style="font-weight:bold; font-size:18px; color:#111; text-transform:uppercase; line-height:1.2; text-align:left;">
                    COMITÉ DEL SISTEMA DE AGUA POTABLE
                </div>

                @if(!empty($localityLine))
                    <div style="font-size:11px; margin-top:4px; color:#222; text-align:left;">
                        {{ $localityLine }}
                    </div>
                @endif

                <div style="font-size:10px; margin-top:6px; color:#222; text-align:left;">
                    <strong>Reporte:</strong> {{ $reportTitle ?? 'Reporte' }} |
                    <strong>Generado:</strong> {{ $generatedAt ?? now()->format('d/m/Y H:i') }}
                    @if(!empty($generatedBy))
                        | <strong>Por:</strong> {{ $generatedBy }}
                    @endif
                </div>
            </td>
        </tr>
    </table>
</header>
