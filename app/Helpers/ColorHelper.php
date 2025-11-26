<?php

if (!function_exists('color')) {
    function color(int|string $valor = 0): string
    {
        $colores = [
            'bg-blue',
            'bg-purple',
            'bg-pink',
            'bg-yellow',
            'bg-orange',
            'bg-lime',
            'bg-teal',
            'bg-cyan',
            'bg-navy',
            'bg-primary',
            'bg-success',
            'bg-info',
            'bg-warning',
            'bg-danger',
            'bg-secondary',
            'bg-dark',
            'bg-fuchsia',
            'bg-violet',
            'bg-rose',
            'bg-emerald',
        ];

        if (in_array($valor, ['random'])) {
            return $colores[array_rand($colores)];
        }

        $indice = is_numeric($valor) ? (int)$valor : 0;
        return $colores[$indice % count($colores)] ?? 'bg-secondary';
    }
}
