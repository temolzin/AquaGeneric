<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        $users = [
            [
                'email' => 'jose@gmail.com',
                'locality_id' => null,
                'name' => 'Jose',
                'last_name' => 'Lopez Osorio',
                'phone' => '7798745677',
                'password' => '12345',
                'role' => 'Admin',
            ],
            [
                'email' => 'eri@gmail.com',
                'locality_id' => 2,
                'name' => 'Erika',
                'last_name' => 'Lopez Perez',
                'phone' => '7798745677',
                'password' => '12345',
                'role' => 'Secretaria',
            ],
            [
                'email' => 'juan@gmail.com',
                'locality_id' => 1,
                'name' => 'Juan',
                'last_name' => 'Perez Garcia',
                'phone' => '5512998832',
                'password' => '12345',
                'role' => 'Supervisor',
            ],
            [
                'email' => 'mario@gmail.com',
                'locality_id' => 3,
                'name' => 'Mario',
                'last_name' => 'Gomez Fernandez',
                'phone' => '5588997766',
                'password' => '12345',
                'role' =>'Supervisor',
            ],
            [
                'email' => 'alonso@gmail.com',
                'locality_id' => 1,
                'name' => 'Alonso',
                'last_name' => 'Gutiérrez López',
                'phone' => '5556161351',
                'password' => '12345',
                'role' => 'Cliente',
            ],
        ];

        $payload = collect($users)->map(function ($u) use ($now) {
            return [
                'email' => $u['email'],
                'locality_id' => $u['locality_id'],
                'name' => $u['name'],
                'last_name' => $u['last_name'],
                'phone' => $u['phone'],
                'password' => Hash::make($u['password']), // solo afecta en insert
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->toArray();

        DB::table('users')->upsert(
            $payload,
            ['email'],
            ['locality_id', 'name', 'last_name', 'phone', 'updated_at']
        );

        collect($users)->each(function ($u) {
            $user = User::where('email', $u['email'])->first();
            $user?->syncRoles([$u['role']]);
        });
    }
}

