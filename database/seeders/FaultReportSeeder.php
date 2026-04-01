<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;

class FaultReportSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_MX');

        $localityIds = DB::table('localities')->pluck('id')->toArray();
        $statuses = ['pending', 'in_review', 'completed'];

        $titles = [
            'Fuga en tubería principal',
            'Sin presión en la red',
            'Bomba sin funcionar',
            'Toma de agua obstruida',
            'Falta de agua en sector',
            'Tubería rota en calle',
            'Medidor dañado',
            'Agua turbia o con mal olor',
            'Fuga en conexión domiciliaria',
            'Calle inundada por rotura'
        ];

        $descriptions = [
            'Se detectó una fuga en la esquina de la calle. Urge reparación inmediata.',
            'Sin presión de agua desde las 7:00 AM. Varias casas afectadas.',
            'La bomba principal está apagada y no arranca automáticamente.',
            'El medidor no gira. Posible daño interno o atoro.',
            'Cliente reporta agua con mal olor y color turbio.',
            'Toma de agua completamente obstruida. No sale agua.',
            'Fuga masiva en tubería principal. Calle inundada.',
            'Sin servicio de agua desde ayer por la noche.',
            'Fuga en conexión domiciliaria. Agua sale por la banqueta.',
            'Presión intermitente. Solo sale agua por ratos.'
        ];

        foreach ($localityIds as $localityId) {
            $userIds = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', User::ROLE_CUSTOMER)
                ->where('users.locality_id', $localityId)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            if (empty($userIds)) {
                continue;
            }

            foreach (range(1, 2) as $i) {
                DB::table('fault_report')->insert([ 
                    'created_by'    => $faker->randomElement($userIds),  
                    'locality_id'   => $localityId,
                    'title'         => $faker->randomElement($titles),
                    'description'   => $faker->randomElement($descriptions),
                    'status'        => $faker->randomElement($statuses),
                    'date_report'   => $faker->dateTimeBetween('-6 months', 'now'),
                    'created_at'    => now(),
                    'deleted_at'    => null,
                ]);
            }
        }
    }
}
