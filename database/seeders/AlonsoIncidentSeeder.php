<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\IncidentStatus;
use Carbon\Carbon;

class AlonsoIncidentSeeder extends Seeder
{
    public function run(): void
    {
        $alonso = User::where('email', 'alonso@gmail.com')->first();

        if (!$alonso) {
            $this->command->error("Usuario Alonso no encontrado. Asegúrate de haber ejecutado UsersTableSeeder.");
            return;
        }

        $category = IncidentCategory::where('locality_id', $alonso->locality_id)->first() 
                    ?? IncidentCategory::whereNull('locality_id')->first();
                    
        $status = IncidentStatus::where('status', 'Pendiente')->where('locality_id', $alonso->locality_id)->first()
                  ?? IncidentStatus::where('status', 'Reportada')->first();

        Incident::create([
            'name'        => 'Fuga de agua en toma principal',
            'description' => 'El cliente Alonso reporta una fuga constante de agua en la banqueta, frente a su domicilio.',
            'start_date'  => Carbon::now()->toDateString(),
            'category_id' => $category ? $category->id : null,
            'status_id'   => $status ? $status->id : null,
            'locality_id' => $alonso->locality_id,
            'created_by'  => $alonso->id,
        ]);
    }
}
