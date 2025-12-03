<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FaultReportSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_MX');

        $localityIds = DB::table('localities')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();
        $statuses = ['Earring', 'In process', 'Resolved', 'Closed'];

        foreach (range(1, 20) as $i) {
            DB::table('fault_report')->insert([
                'customer_id'   => $faker->numberBetween(1, 10), 
                'created_by'    => $faker->randomElement($userIds),  
                'locality_id'   => $faker->randomElement($localityIds),
                'title'         => $faker->randomElement([
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
                ]),
                'description'   => $faker->randomElement([
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
                ]),
                'status'        => $faker->randomElement($statuses),
                'date_report'   => $faker->dateTimeBetween('-6 months', 'now'),
                'created_at'    => now(),
                'updated_at'    => now(),
                'deleted_at'    => null,
            ]);
        }
    }
}
