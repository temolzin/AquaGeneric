<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class LocalityNoticesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $localityIds = DB::table('localities')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        foreach ($localityIds as $localityId) {
            foreach (range(1, 2) as $i) {
                $startDate = $faker->dateTimeBetween('-1 month', '+1 month');
                $endDate = Carbon::instance($startDate)->addDays($faker->numberBetween(1, 7));
                
                DB::table('locality_notices')->insert([
                    'title' => $faker->sentence(6),
                    'description' => $faker->paragraph(3),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'is_active' => $faker->boolean(80),
                    'locality_id' => $localityId,
                    'created_by' => $faker->randomElement($userIds),
                    'attachment_url' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}