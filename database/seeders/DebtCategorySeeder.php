<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DebtCategory;

class DebtCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Servicio de Agua',
                'description' => 'Pago correspondiente al suministro de agua.',
                'color' => 'bg-primary',
            ],
            [
                'name' => 'Mantenimiento',
                'description' => 'Cuotas para mantenimiento general.',
                'color' => 'bg-warning',
            ],
            [
                'name' => 'Multas',
                'description' => 'Sanciones por incumplimientos.',
                'color' => 'bg-danger',
            ],
            [
                'name' => 'Servicios Generales',
                'description' => 'Pagos de servicios comunes.',
                'color' => 'bg-info',
            ],
            [
                'name' => 'Cuota Extraordinaria',
                'description' => 'Pagos adicionales no recurrentes.',
                'color' => 'bg-secondary',
            ],
        ];

        foreach ($categories as $category) {
            DebtCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
