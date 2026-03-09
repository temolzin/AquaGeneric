<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentCategory;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IncidentCategorySeeder extends Seeder
{
    public function run()
    {
        $categoryConfigs = [
            [
                'name' => 'Mantenimiento General',
                'description' => 'Trabajos generales de reparación o conservación del sistema de agua.',
                'color' => color(9),
            ],
            [
                'name' => 'Eléctrica',
                'description' => 'Problemas con instalación o alumbrado eléctrico.',
                'color' => color(3),
            ],
            [
                'name' => 'Plomería',
                'description' => 'Fugas, tuberías dañadas o problemas de agua.',
                'color' => color(0),
            ],
            [
                'name' => 'Infraestructura',
                'description' => 'Daños estructurales como techos, paredes o pisos.',
                'color' => color(4),
            ],
            [
                'name' => 'Tecnología',
                'description' => 'Fallas con equipo de cómputo, redes o sistemas.',
                'color' => color(1),
            ],
        ];

        $localities = Locality::all();

        foreach ($localities as $locality) {
            $createdBy = $this->getUserForLocality($locality->id);
            
            foreach ($categoryConfigs as $categoryConfig) {
                IncidentCategory::updateOrCreate(
                    [
                        'name' => $categoryConfig['name'],
                        'locality_id' => $locality->id,
                    ],
                    [
                        'description' => $categoryConfig['description'],
                        'color' => $categoryConfig['color'],
                        'created_by' => $createdBy,
                    ]
                );
            }
        }
    }

    private function getUserForLocality(int $localityId): ?int
    {
        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
            ->where('users.locality_id', $localityId)
            ->distinct()
            ->value('users.id');
    }
}
