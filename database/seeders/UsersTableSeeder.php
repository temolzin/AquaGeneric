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

        foreach ($users as $u) {
            $user = User::where('email', $u['email'])->first();

            if (!$user) {
                // Crear
                $user = User::create([
                    'locality_id' => $u['locality_id'],
                    'name' => $u['name'],
                    'last_name' => $u['last_name'],
                    'email' => $u['email'],
                    'phone' => $u['phone'],
                    'password' => Hash::make($u['password']),
                ]);
            } else {
                // Actualizar datos “no sensibles” (sin tocar password)
                $user->update([
                    'locality_id' => $u['locality_id'],
                    'name' => $u['name'],
                    'last_name' => $u['last_name'],
                    'phone' => $u['phone'],
                ]);
            }

            // Rol determinístico (evita duplicidad)
            // Si la empresa prefiere no “pisar” roles, se puede dejar assignRole con check.
            $user->syncRoles([$u['role']]);
        }
    }
}
