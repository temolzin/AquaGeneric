<?php

if (!function_exists('color')) {
    function color(int|string $value = 0): string
    {
        $colors = [
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

        if (in_array($value, ['random'])) {
            return $colors[array_rand($colors)];
        }

        $index = is_numeric($value) ? (int)$value : 0;
        return $colors[$index % count($colors)] ?? 'bg-secondary';
    }
}

    if (!function_exists('pdf_color')) {
        function pdf_color(string $bgClass = 'bg-secondary'): string
        {
            $colorMap = [
                'bg-blue'       => '#0d6efd',
                'bg-cyan'       => '#0dcaf0',
                'bg-danger'     => '#dc3545',
                'bg-dark'       => '#212529',
                'bg-emerald'    => '#10b981',
                'bg-fuchsia'    => '#d63384',
                'bg-info'       => '#0dcaf0',
                'bg-lime'       => '#84cc16',
                'bg-navy'       => '#0B1C80',
                'bg-orange'     => '#fd7e14',
                'bg-pink'       => '#d63384',
                'bg-primary'    => '#0d6efd',
                'bg-purple'     => '#6f42c1',
                'bg-rose'       => '#e91e63',
                'bg-secondary'  => '#6c757d',
                'bg-success'    => '#198754',
                'bg-teal'       => '#20c997',
                'bg-violet'     => '#6610f2',
                'bg-warning'    => '#ffc107',
                'bg-yellow'     => '#ffc107',
            ];

            return $colorMap[$bgClass] ?? '#6c757d';
        }
    }
