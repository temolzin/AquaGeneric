<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(LocalitiesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(CostsTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(WaterConnectionsTableSeeder::class);
        $this->call(DebtsTableSeeder::class);
        $this->call(PaymentsTableSeeder::class);
        $this->call(GeneralExpensesSeeder::class);
    }
}
