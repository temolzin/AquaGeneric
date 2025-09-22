<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class LocalityNoticesSeeder extends Seeder
{
    private const MAX_RECORDS = 15;
    private const NOTICE_TYPES = ['event', 'maintenance', 'alert', 'general'];
    private const PRIORITIES = ['low', 'medium', 'high'];

    public function run(): void
    {
        $faker = Faker::create();
        $today = Carbon::today();

        $localityIds = DB::table('localities')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        for ($i = 1; $i <= self::MAX_RECORDS; $i++) {
            $startDate = $today->copy()->addDays(rand(-5, 5));
            $endDate   = $startDate->copy()->addDays(rand(1, 7));

            DB::table('locality_notices')->insert([
                'title' => 'Aviso de mantenimiento',
                'description' => 'Se suspenderá el servicio por mantenimiento de red.',
                'start_date' => now()->addDays(2),
                'end_date' => now()->addDays(4),
                'is_active' => true,
                'locality_id' => 1, 
                'created_by' => 1,  
                'attachment_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
]);

        }
    }
}
