<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MigrateCustomersToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = DB::table('customers')->whereNull('user_id')->get();

        foreach ($customers as $customer) {
            $userId = DB::table('users')->insertGetId([
                'name'        => $customer->name,
                'last_name'   => $customer->last_name,
                'email'       => $customer->email,
                'password'    => Hash::make('12345'),
                'phone'       => null,
                'locality_id' => $customer->locality_id ?? null,
                'created_at'  => null,
                'updated_at'  => null,
                'deleted_at'  => null,
            ]);

            DB::table('customers')
                ->where('id', $customer->id)
                ->update(['user_id' => $userId]);
        }
    }
}
