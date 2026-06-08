<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LogIncident;
use App\Models\Incident;
use App\Models\IncidentStatus;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;

class LogIncidentHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('es_MX');
        $incidents = Incident::all();

        if ($incidents->isEmpty()) {
            $this->command->warn('No hay incidencias registradas. Ejecuta primero IncidentSeeder.');
            return;
        }

        $statusDescriptionMap = [
            'Reportada' => [
                'Incidencia recibida a través de los canales de atención y registrada en el sistema.',
                'Se registró el reporte inicial y se ha turnado al departamento correspondiente.',
                'Anomalía detectada durante el recorrido de vigilancia rutinario por personal de zona.',
            ],
            'Pendiente' => [
                'Se realizó la inspección preliminar y se está a la espera de la llegada de la cuadrilla técnica.',
                'La incidencia ha sido evaluada y se encuentra programada para su pronta atención.',
                'Se requiere equipo especializado para proceder; la solicitud de materiales ha sido enviada.',
            ],
            'En progreso' => [
                'Inician los trabajos de reparación y mantenimiento en el sitio afectado.',
                'La brigada técnica se encuentra realizando maniobras correctivas en la infraestructura.',
                'Se procede con la sustitución de piezas dañadas y la limpieza preventiva del área.',
            ],
            'Terminada' => [
                'Trabajos concluidos satisfactoriamente. El suministro y servicio han sido restablecidos.',
                'Se verificó la reparación final y el área ha sido despejada de escombros y herramientas.',
                'Incidencia resuelta satisfactoriamente. Se cierra el folio tras confirmar el funcionamiento normal.',
            ],
            'default' => [
                'Se actualizó la bitácora de seguimiento con los avances de la jornada.',
                'Personal técnico reporta que se mantiene el monitoreo de la zona tras las acciones realizadas.',
                'Se realizó una inspección de control para validar el estado actual de la incidencia.',
                'Gestión administrativa de la incidencia realizada por el personal de la localidad.',
                'Se anexan detalles adicionales a la descripción técnica para el seguimiento del reporte.'
            ]
        ];

        foreach ($incidents as $incident) {
            $localityId = $incident->locality_id;

            $validStatuses = IncidentStatus::where('locality_id', $localityId)
                ->orWhereNull('locality_id')
                ->pluck('status')
                ->toArray();

            $validEmployees = Employee::where('locality_id', $localityId)->get();

            $validUsers = User::where('locality_id', $localityId)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY]);
                })->get();

            if (empty($validStatuses) || $validEmployees->isEmpty() || $validUsers->isEmpty()) {
                $this->command->info("Saltando incidencia ID {$incident->id}: No hay empleados o usuarios en la localidad {$localityId}");
                continue;
            }

            $numLogs = rand(1, 3);

            for ($i = 0; $i < $numLogs; $i++) {
                $logStatus = $faker->randomElement($validStatuses);
                $possibleDescriptions = $statusDescriptionMap[$logStatus] ?? $statusDescriptionMap['default'];

                LogIncident::create([
                    'incident_id' => $incident->id,
                    'locality_id' => $localityId,
                    'employee_id' => $validEmployees->random()->id,
                    'created_by'  => $validUsers->random()->id,
                    'status'      => $logStatus,
                    'description' => $faker->randomElement($possibleDescriptions),
                ]);
            }
        }
    }
}
