<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralEarning;
use App\Models\EarningType;
use App\Models\Locality;
use App\Models\User;
use Faker\Factory as Faker;

class GeneralEarningsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_MX');
        
        $this->fixExistingEarningsWithoutType();
        $this->createNewEarningsWithType($faker);
    }

    private function fixExistingEarningsWithoutType()
    {
        $earningsWithoutType = DB::table('general_earnings')
            ->whereNull('earning_type_id')
            ->get();

        foreach ($earningsWithoutType as $earning) {
            $earningType = EarningType::where('locality_id', $earning->locality_id)->first();

            if ($earningType) {
                DB::table('general_earnings')
                    ->where('id', $earning->id)
                    ->update([
                        'earning_type_id' => $earningType->id,
                        'updated_at' => now()
                    ]);
            } else {
                $generalType = EarningType::where('name', 'Operación Administrativa')
                    ->whereNull('locality_id')
                    ->first();

                if (!$generalType) {
                    $users = User::pluck('id')->toArray();
                    $userId = !empty($users) ? $users[0] : 1;

                    $generalType = EarningType::create([
                        'name' => 'Operación Administrativa',
                        'description' => 'Ingreso operativos y administrativos generales sin asignación a una localidad específica.',
                        'color' => '#3498db',
                        'locality_id' => null,
                        'created_by' => $userId,
                    ]);
                }

                DB::table('general_earnings')
                    ->where('id', $earning->id)
                    ->update([
                        'earning_type_id' => $generalType->id,
                        'updated_at' => now()
                    ]);
            }
        }
    }

    private function createNewEarningsWithType($faker)
    {
        $localities = Locality::all();

        if ($localities->isEmpty()) {
            $this->command->warn('No localities found. Skipping general earnings creation.');
            return;
        }

        foreach ($localities as $locality) {
            $users = User::where('locality_id', $locality->id)->pluck('id')->toArray();
            
            if (empty($users)) {
                $users = User::pluck('id')->toArray();
                if (empty($users)) {
                    continue;
                }
            }

            $earningTypes = EarningType::where('locality_id', $locality->id)->get();
            
            if ($earningTypes->isEmpty()) {
                $earningTypes = EarningType::whereNull('locality_id')->get();
                
                if ($earningTypes->isEmpty()) {
                    $this->command->warn("No earning types found for locality {$locality->id}. Skipping.");
                    continue;
                }
            }

            // Crear entre 5 y 10 ingresos por localidad
            $numberOfEarnings = rand(5, 10);
            
            for ($i = 0; $i < $numberOfEarnings; $i++) {
                $earningType = $earningTypes->random();
                $userId = $faker->randomElement($users);
                
                $concept = $this->getRandomEarningConcept($faker);
                
                GeneralEarning::create([
                    'locality_id' => $locality->id,
                    'created_by' => $userId,
                    'earning_type_id' => $earningType->id,
                    'concept' => $concept,
                    'description' => $this->getDescriptionByConcept($concept, $faker),
                    'amount' => $this->getAmountByConcept($concept),
                    'earning_date' => $faker->dateTimeBetween('-60 days', 'now'),
                ]);
            }
        }

        $this->command->info('General earnings created successfully.');
    }

    private function getRandomEarningConcept($faker)
    {
        $concepts = [
            'Pago de servicio de agua',
            'Conexión nueva de agua',
            'Reconexión de servicio',
            'Depósito de garantía',
            'Multa por retraso en pago',
            'Cargo por reposición de medidor',
            'Venta de accesorios',
            'Donación para mejoras',
            'Subvención municipal',
            'Pago de cuota especial',
            'Instalación de tubería',
            'Mantenimiento preventivo',
            'Reparación de fuga',
            'Pago de adeudo',
            'Recargo por mora',
        ];

        return $faker->randomElement($concepts);
    }

    private function getDescriptionByConcept($concept, $faker)
    {
        $descriptions = [
            'Pago de servicio de agua' => 'Pago mensual del servicio de agua potable y alcantarillado',
            'Conexión nueva de agua' => 'Instalación de nueva conexión domiciliaria de agua potable',
            'Reconexión de servicio' => 'Reconexión del servicio de agua tras corte por morosidad',
            'Depósito de garantía' => 'Depósito requerido para nueva conexión o por cambio de titular',
            'Multa por retraso en pago' => 'Multa aplicada por pago fuera del período establecido',
            'Cargo por reposición de medidor' => 'Costo por cambio o reparación de medidor dañado',
            'Venta de accesorios' => 'Venta de llaves, tuberías u otros accesorios pluviales',
            'Donación para mejoras' => 'Contribución voluntaria para mejoras en la infraestructura',
            'Subvención municipal' => 'Recurso asignado por el municipio para operaciones',
            'Pago de cuota especial' => 'Cuota extraordinaria para proyectos de ampliación',
            'Instalación de tubería' => 'Trabajos de instalación de tubería principal o secundaria',
            'Mantenimiento preventivo' => 'Servicios de mantenimiento programado de la red',
            'Reparación de fuga' => 'Reparación de fuga reportada en la red de distribución',
            'Pago de adeudo' => 'Liquidación de adeudo pendiente de periodos anteriores',
            'Recargo por mora' => 'Recargo aplicado por pago extemporáneo del servicio',
        ];

        return $descriptions[$concept] ?? $faker->sentence(8);
    }

    private function getAmountByConcept($concept)
    {
        $amounts = [
            'Pago de servicio de agua' => rand(150, 450), // MXN
            'Conexión nueva de agua' => rand(800, 2000),
            'Reconexión de servicio' => rand(200, 500),
            'Depósito de garantía' => rand(500, 1000),
            'Multa por retraso en pago' => rand(50, 200),
            'Cargo por reposición de medidor' => rand(300, 800),
            'Venta de accesorios' => rand(100, 500),
            'Donación para mejoras' => rand(200, 1000),
            'Subvención municipal' => rand(5000, 20000),
            'Pago de cuota especial' => rand(300, 800),
            'Instalación de tubería' => rand(1000, 5000),
            'Mantenimiento preventivo' => rand(800, 3000),
            'Reparación de fuga' => rand(500, 1500),
            'Pago de adeudo' => rand(300, 1200),
            'Recargo por mora' => rand(50, 150),
        ];

        return $amounts[$concept] ?? rand(100, 1000);
    }
}
