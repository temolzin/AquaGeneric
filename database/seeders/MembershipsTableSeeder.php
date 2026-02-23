<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MembershipsTableSeeder extends Seeder
{
    public function run()
    {
        // Admin estable (si no hay rol Admin, usa el menor id disponible)
        $adminId = User::whereHas('roles', function ($q) {
                $q->where('name', 'Admin');
            })
            ->orderBy('id')
            ->value('id');

        if (!$adminId) {
            $adminId = User::orderBy('id')->value('id'); // fallback estable
        }

        // Si aún así no hay usuarios, evita crashear con SQL inválido
        // (en tu flujo normal ya existen porque UsersTableSeeder corre antes)
        if (!$adminId) {
            // Puedes lanzar excepción o simplemente salir; aquí salimos para no romper el seed completo
            return;
        }

        $memberships = [
            [
                'name' => 'Basic Plan - 3 Months',
                'price' => 299.00,
                'term_months' => 3,
                'water_connections_number' => 1000,
                'users_number' => 1,
            ],
            [
                'name' => 'Premium Plan - 6 Months',
                'price' => 499.00,
                'term_months' => 6,
                'water_connections_number' => 4000,
                'users_number' => 3,
            ],
            [
                'name' => 'Enterprise Plan - 12 Months',
                'price' => 899.00,
                'term_months' => 12,
                'water_connections_number' => 10000,
                'users_number' => 5,
            ]
        ];

        foreach ($memberships as $m) {
            $existing = DB::table('memberships')
                ->where('name', $m['name'])
                ->first();

            if ($existing){
                // Update sin tocar created_by
                DB::table('memberships')
                    ->where('id', $existing->id)
                    ->update([
                    'price' => $m['price'],
                    'term_months' => $m['term_months'],
                    'water_connections_number' => $m['water_connections_number'],
                    'users_number' => $m['users_number'],
                    'updated_at' => now(),
                ]);

                // Opcional: si existe pero created_by está null, lo rellenamos 1 vez (idempotente)
                DB::table('memberships')
                    ->where('id', $existing->id)
                    ->whereNull('created_by')
                    ->update([
                        'created_by' => $adminId,
                        'updated_at' => now(),
                    ]);
            } else {
                // Insert con created_by
                DB::table('memberships')->insert([
                    'name' => $m['name'],
                    'price' => $m['price'],
                    'term_months' => $m['term_months'],
                    'water_connections_number' => $m['water_connections_number'],
                    'users_number' => $m['users_number'],
                    'created_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Mantener tu corrección de defaults, si sigue aplicando al negocio
        DB::table('memberships')
            ->where('water_connections_number', 0)
            ->orWhere('users_number', 0)
            ->update([
                'water_connections_number' => 1000,
                'users_number' => 1,
                'updated_at' => now(),
            ]);
    }
}
