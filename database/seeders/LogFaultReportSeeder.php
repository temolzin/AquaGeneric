<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LogFaultReport;

class LogFaultReportSeeder extends Seeder
{
    public function run(): void
    {
        LogFaultReport::create([
            'fault_report_id' => 1,
            'locality_id' => 1,
            'created_by' => 1,
            'status' => LogFaultReport::STATUS_PENDING,
            'description' => 'Primer registro de log (pendiente)'
        ]);

        LogFaultReport::create([
            'fault_report_id' => 1,
            'locality_id' => 1,
            'created_by' => 1,
            'status' => LogFaultReport::STATUS_IN_REVIEW,
            // description omitido
        ]);

        LogFaultReport::create([
            'fault_report_id' => 1,
            'locality_id' => 1,
            'created_by' => 1,
            'status' => LogFaultReport::STATUS_COMPLETED,
            'description' => 'Registro finalizado correctamente.'
        ]);
    }
}
