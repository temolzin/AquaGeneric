<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LocalityNoticesSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        $localityIds = DB::table('localities')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        $notices = [
            [
                'title' => 'Mantenimiento de agua programado',
                'description' => 'Se suspenderá temporalmente el suministro de agua en varias zonas por mantenimiento de la red principal.',
                'start_date' => $today->copy()->addDays(2),
                'end_date' => $today->copy()->addDays(3),
            ],
            [
                'title' => 'Revisión de medidores',
                'description' => 'Personal autorizado realizará la revisión anual de medidores en todas las localidades durante esta semana.',
                'start_date' => $today->copy()->addDays(1),
                'end_date' => $today->copy()->addDays(2),
            ],
            [
                'title' => 'Corte de energía en zona norte',
                'description' => 'Debido a trabajos de la CFE, habrá interrupción del suministro eléctrico que puede afectar la presión del agua.',
                'start_date' => $today->copy()->addDays(3),
                'end_date' => $today->copy()->addDays(4),
            ],
            [
                'title' => 'Aviso de limpieza de cisternas',
                'description' => 'Se realizará limpieza de cisternas comunitarias en varias localidades. Se recomienda almacenamiento previo de agua.',
                'start_date' => $today->copy()->addDays(1),
                'end_date' => $today->copy()->addDays(3),
            ],
            [
                'title' => 'Restablecimiento de servicio',
                'description' => 'El servicio de agua ha sido restablecido tras los trabajos de mantenimiento en la red principal.',
                'start_date' => $today->copy()->subDay(),
                'end_date' => $today->copy()->addDay(),
            ],
        ];

        foreach ($notices as $notice) {
            DB::table('locality_notices')->insert([
                'title' => $notice['title'],
                'description' => $notice['description'],
                'start_date' => $notice['start_date'],
                'end_date' => $notice['end_date'],
                'is_active' => true,
                'locality_id' => $localityIds[array_rand($localityIds)],
                'created_by' => $userIds[array_rand($userIds)],
                'attachment_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
