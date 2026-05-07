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
                'id' => 1,
                'email' => 'jose@gmail.com',
                'locality_id' => null,
                'name' => 'Jose',
                'last_name' => 'Lopez Osorio',
                'phone' => '7798745677',
                'password' => '12345',
                'role' => 'Admin',
            ],
            [
                'id' => 2,
                'email' => 'eri@gmail.com',
                'locality_id' => 2,
                'name' => 'Erika',
                'last_name' => 'Lopez Perez',
                'phone' => '7798745677',
                'password' => '12345',
                'role' => 'Secretaria',
            ],
            [
                'id' => 3,
                'email' => 'juan@gmail.com',
                'locality_id' => 1,
                'name' => 'Juan',
                'last_name' => 'Perez Garcia',
                'phone' => '5512998832',
                'password' => '12345',
                'role' => 'Supervisor',
            ],
            [
                'id' => 4,
                'email' => 'mario@gmail.com',
                'locality_id' => 3,
                'name' => 'Mario',
                'last_name' => 'Gomez Fernandez',
                'phone' => '5588997766',
                'password' => '12345',
                'role' =>'Supervisor',
            ],
            [
                'id' => 999,
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
            try {
                $existingUser = User::where('email', $u['email'])->first();
                
                if ($existingUser) {
                    $existingUser->update([
                        'name' => $u['name'],
                        'last_name' => $u['last_name'],
                        'phone' => $u['phone'],
                        'password' => Hash::make($u['password']),
                        'locality_id' => $u['locality_id'],
                        'updated_at' => $now,
                    ]);
                } else {
                    $newUser = User::create([
                        'id' => $u['id'],
                        'email' => $u['email'],
                        'name' => $u['name'],
                        'last_name' => $u['last_name'],
                        'phone' => $u['phone'],
                        'password' => Hash::make($u['password']),
                        'locality_id' => $u['locality_id'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            } catch (\Exception $e) {
                $this->command->error("  Error with user {$u['email']}: " . $e->getMessage());
            }
        }

        foreach ($users as $u) {
            $user = User::where('email', $u['email'])->first();
            if ($user) {
                $user->syncRoles([$u['role']]);
            }
        }
    }
}

