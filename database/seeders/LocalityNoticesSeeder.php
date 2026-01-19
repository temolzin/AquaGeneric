<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\User;

class LocalityNoticesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_MX');

        $localityIds = DB::table('localities')->pluck('id')->toArray();

        foreach (range(1, 150) as $index) {
            $locality_id = $faker->randomElement($localityIds);

            $userIds = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', User::ROLE_SUPERVISOR)
                ->where('users.locality_id', $locality_id)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            if (!empty($userIds)) {
                foreach (range(1, 2) as $i) {
                    $startDate = $faker->dateTimeBetween('-1 month', '+1 month');
                    $endDate = Carbon::instance($startDate)->addDays($faker->numberBetween(1, 7));
                
                    DB::table('locality_notices')->insert([
                        'title' => $faker->randomElement([
                            'Corte programado de agua potable',
                            'Interrupción del servicio eléctrico',
                            'Recolección especial de poda y escombros',
                            'Jornada de vacunación antirrábica gratuita',
                            'Inicio de obras de repavimentación',
                            'Feria artesanal en la Plaza Principal',
                            'Campaña de descacharrado contra el dengue',
                            'Limpieza intensiva de calles',
                            'Taller gratuito de compostaje doméstico',
                            'vencimiento de tasa municipal',
                            'Mejoras en el alumbrado público',
                            'Corte de tránsito por evento cultural'
                        ]),
                        'description' => $faker->randomElement([
                            'Por mantenimiento en la red principal se suspenderá el suministro de agua el día ' . $startDate->format('d/m/Y') . ' de 08:00 a 16:00 hs.',
                            'La empresa eléctrica realizará trabajos de mejora. Corte programado el ' . $startDate->format('d/m/Y') . ' entre las 09:00 y 14:00 hs.',
                            'El próximo ' . $startDate->format('d/m/Y') . ' recolectaremos ramas, muebles viejos y escombros. Depositar en la vereda la noche anterior.',
                            'Vacuna antirrábica gratuita para perros y gatos el ' . $startDate->format('d/m/Y') . ' de 09:00 a 13:00 hs en la Plaza Principal.',
                            'A partir del ' . $startDate->format('d/m/Y') . ' comenzamos la repavimentación de Av. Principal. Duración estimada: 10 días.',
                            'Te invitamos a la feria de artesanos y productores locales el ' . $startDate->format('d/m/Y') . ' desde las 10:00 hs. ¡Entrada libre!',
                            'Para prevenir el dengue, recolectaremos recipientes en desuso el ' . $startDate->format('d/m/Y') . '. Sacar todo antes de las 08:00 hs.',
                            'El ' . $startDate->format('d/m/Y') . ' realizaremos hidrolavado y desinfección de calles. No estacionar de 07:00 a 14:00 hs.',
                            'Taller práctico de compostaje en casa el ' . $startDate->format('d/m/Y') . ' a las 18:00 hs en el Centro Cultural. Cupo limitado.',
                            'Recordamos que el ' . $startDate->format('d/m/Y') . ' vence la cuota municipal. Pague online o en el municipio y evite recargos.',
                            'Estamos instalando nuevas luminarias LED en todo el barrio. Trabajos nocturnos durante esta semana.',
                            'Por el festival cultural habrá corte total de tránsito en el centro el ' . $startDate->format('d/m/Y') . ' de 18:00 a 00:00 hs.'
                        ]),
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'is_active' => $faker->boolean(80),
                        'locality_id' => $locality_id,
                        'created_by' => $faker->randomElement($userIds),
                        'attachment_url' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}