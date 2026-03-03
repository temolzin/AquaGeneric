<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Cost;
use App\Models\User;

class CostsTableSeeder extends Seeder
{
    public function run()
    {
        $localityIds = DB::table('localities')->pluck('id')->toArray();

        $costs = [
            [
                'locality_id' => null,
                'category' => 'Precio Estándar',
                'price' => 130.00,
                'description' => 'Tarifa Estándar',
            ],
            [
                'locality_id' => 1,
                'category' => 'Publico en general',
                'price' => 150.00,
                'description' => 'Tarifa para publico en general',
            ],
            [
                'locality_id' => 2,
                'category' => 'Adultos Mayores',
                'price' => 100.00,
                'description' => 'Tarifa para Adultos Mayores que cuenten con INAPAM',
            ],
            [
                'locality_id' => 3,
                'category' => 'Usuarios Nuevos',
                'price' => 120.00,
                'description' => 'Tarifa para Usuarios Nuevos',
            ],
            [
                'locality_id' => 1,
                'category' => 'Madres Solteras',
                'price' => 100.00,
                'description' => 'Tarifas para Madres Solteras',
            ],
        ];

        foreach ($costs as $cost) {
            $createdBy = $this->getUserForLocality($cost['locality_id']);
            
            if (!$createdBy) {
                continue;
            }

            Cost::updateOrCreate(
                [
                    'locality_id' => $cost['locality_id'],
                    'category' => $cost['category'],
                ],
                [
                    'price' => $cost['price'],
                    'description' => $cost['description'],
                    'created_by' => $createdBy,
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function getUserForLocality(?int $localityId): ?int
    {
        if ($localityId === null) {
            return DB::table('users')
                ->where('email', 'jose@gmail.com')
                ->value('id');
        }

        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
            ->where('users.locality_id', $localityId)
            ->distinct()
            ->value('users.id');
    }
}

