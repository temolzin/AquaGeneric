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
        User::firstOrCreate([
            'locality_id' => null,
            'name' => 'Jose',
            'last_name' => 'Lopez Osorio',
            'email' => 'jose@gmail.com',
            'phone' => '7798745677',
            'password' => Hash::make('12345'),
        ])->assignRole('Admin');

        User::firstOrCreate([
            'locality_id' => 2,
            'name' => 'Erika',
            'last_name' => 'Lopez Perez',
            'email' => 'eri@gmail.com',
            'phone' => '7798745677',
            'password' => Hash::make('12345'),
        ])->assignRole('Secretaria');

        User::firstOrCreate([
            'locality_id' => 1,
            'name' => 'Juan',
            'last_name' => 'Perez Garcia',
            'email' => 'juan@gmail.com',
            'phone' => '5512998832',
            'password' => Hash::make('12345'),
        ])->assignRole('Supervisor');

        User::firstOrCreate([
            'locality_id' => 3,
            'name' => 'Mario',
            'last_name' => 'Gomez Fernandez',
            'email' => 'mario@gmail.com',
            'phone' => '5588997766',
            'password' => Hash::make('12345'),
        ])->assignRole('Supervisor');

        User::firstOrCreate([
            'locality_id' => 1,
            'name' => 'Alonso',
            'last_name' => 'Gutiérrez López',
            'email' => 'alonso@gmail.com',
            'phone' => '5556161351',
            'password' => Hash::make('12345'),
        ])->assignRole('Cliente');
    }
}
