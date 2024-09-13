<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Jose',
            'last_name' => 'Lopez Osorio',
            'email' => 'jose@gmail.com',
            'phone' => '7798745677',
            'password' => Hash::make('12345'),
        ])->assignRole('Admin');

        User::create([
            'name' => 'Erika',
            'last_name' => 'Lopez perez',
            'email' => 'eri@gmail.com',
            'phone' => '7798745677',
            'password' => Hash::make('12345'),
        ])->assignRole('secretariat');
    }
}
