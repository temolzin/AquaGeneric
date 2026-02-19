<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo contacto</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; color: #222;">
        <div style="text-align:center; margin-bottom: 20px;">
                <img src="{{ $logoCid ?? asset('img/logo.png') }}" alt="Logo" style="max-height:60px;">
                <h2>Nuevo mensaje desde el formulario de contacto</h2>
            </div>

        <p><strong>Nombre:</strong> {{ $name ?? '' }}</p>
        <p><strong>Correo:</strong> {{ $email ?? '' }}</p>
        <p><strong>Mensaje:</strong></p>
        <div style="border:1px solid #eee; padding:10px; background:#fafafa;">{!! nl2br(e($contactMessage ?? '')) !!}</div>

        <hr>
        <div style="text-align:center; margin-top:20px;">
            <img src="{{ $footerCid ?? asset('img/rootheim.png') }}" alt="" style="max-height:40px;">
        </div>
    </div>
</body>
</html>
