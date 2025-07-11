<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $employees = [
            [
                'name' => 'Juan',
                'last_name' => 'Pérez',
                'rol' => 'Encargado',
                'salary' => '8000',
                'phone_number' => '5512345670',
                'email' => 'juan.perez@example.com',
                'state' => 'Estado de México',
                'locality' => 'Nezahualcóyotl',
                'block' => 'San Agustín',
                'zip_code' => '57100',
                'street' => 'Avenida Central',
                'exterior_number' => '45',
                'interior_number' => '2B',
                'created_by' => 3,
                'locality_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laura',
                'last_name' => 'García',
                'rol' => 'Recepcionista',
                'salary' => '7500',
                'phone_number' => '5523456781',
                'email' => 'laura.garcia@example.com',
                'state' => 'CDMX',
                'locality' => 'Iztapalapa',
                'block' => 'El Rosario',
                'zip_code' => '09090',
                'street' => 'Calle 20',
                'exterior_number' => '123',
                'interior_number' => '1A',
                'created_by' => 3,
                'locality_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos',
                'last_name' => 'Ramírez',
                'rol' => 'Seguridad',
                'salary' => '7000',
                'phone_number' => '5534567890',
                'email' => 'carlos.ramirez@example.com',
                'state' => 'Jalisco',
                'locality' => 'Guadalajara',
                'block' => 'Centro',
                'zip_code' => '44100',
                'street' => 'Juárez',
                'exterior_number' => '200',
                'interior_number' => '3C',
                'created_by' => 3,
                'locality_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($employees as $empData) {
            $id = DB::table('employees')->insertGetId($empData);

            $employee = Employee::find($id);
            if ($employee && file_exists(public_path('img/userDefault.png'))) {
                $employee->addMedia(public_path('img/userDefault.png'))
                        ->preservingOriginal()
                        ->toMediaCollection('employeeGallery');
            }
        }
    }
}
