<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;

class LogWaterConnectionTransferSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $localityIds = DB::table('localities')->pluck('id')->toArray();

        foreach (range(1, 40) as $index) {
            $locality_id = $faker->randomElement($localityIds);

            $userIds = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                ->where('users.locality_id', $locality_id)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            if (empty($userIds)) {
                continue;
            }

            $waterConnection = DB::table('water_connections')->where('locality_id', $locality_id)->inRandomOrder()->first();
            $oldCustomer = DB::table('customers')->where('status', 0)->where('locality_id', $locality_id)->inRandomOrder()->first();
            $newCustomer = DB::table('customers')->where('status', 1)->where('locality_id', $locality_id)->inRandomOrder()->first();

            if (!$waterConnection || !$oldCustomer || !$newCustomer) {
                continue;
            }

            DB::table('log_water_connection_transfer')->insert([
                'water_connection_id' => $waterConnection->id,
                'old_customer_id' => $oldCustomer->id,
                'new_customer_id' => $newCustomer->id,
                'reason' => $faker->randomElement(['death', 'sale', 'other']),
                'effective_date' => $faker->date(),
                'note' => 'Transferencia de prueba generada por seeder.',
                'created_by' => $faker->randomElement($userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
