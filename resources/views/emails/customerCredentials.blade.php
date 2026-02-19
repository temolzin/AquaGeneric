<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Credenciales de acceso</title>
</head>
<body style="font-family: Arial, sans-serif; margin:0; padding:0; background:#f5f6f8;">
    <div style="max-width: 720px; margin: 0 auto; background:#ffffff; padding: 28px;">

        @if(!empty($logoCid))
            <div style="text-align:center; margin-bottom: 18px;">
                <img src="{{ $logoCid }}" alt="Logo" style="height: 60px;">
            </div>
        @endif

        <h2 style="margin:0 0 8px 0; color:#0b2a6f;">AquaControl</h2>
        <h3 style="margin:0 0 18px 0; color:#111;">Credenciales de acceso</h3>
        <p style="color:#222;">
            Estimado(a) <strong>{{ $user->name }} {{ $user->last_name }}</strong>,
        </p>
        <p style="color:#222;">
            Se han generado/actualizado sus credenciales para el acceso al sistema.
            Adjuntamos el documento <strong>“Datos del Usuario”</strong> en formato PDF.
        </p>
        <div style="border:1px solid #e5e7eb; padding:14px; border-radius:8px; margin:18px 0;">
            <p style="margin:0 0 6px 0;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin:0;"><strong>Acceso:</strong> Consulte el PDF adjunto para ver sus credenciales.</p>
        </div>
        <div style="border:1px solid #fecaca; background:#fff5f5; color:#991b1b; padding:12px; border-radius:8px;">
            <strong>Recomendación:</strong>
            De preferencia, actualice constantemente su contraseña para mantener la seguridad de su cuenta.
        </div>
        <p style="color:#222; margin-top: 18px;">
            Atentamente,<br>
            <strong>El equipo de administración</strong>
        </p>
        <hr style="border:none; border-top:1px solid #e5e7eb; margin: 22px 0;">
        <p style="font-size: 12px; color:#555; margin:0;">
            Contacto: {{ $senderEmail }}<br>
            Teléfono: {{ $senderPhone }}
        </p>

        @if(!empty($footerCid))
            <div style="text-align:center; margin-top: 16px;">
                <img src="{{ $footerCid }}" alt="Footer" style="height: 28px;">
            </div>
        @endif

    </div>
</body>
</html>
