<?php

namespace Database\Seeders;
use App\Models\GeneralExpense;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;

class GeneralExpensesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_MX');
        
        DB::table('general_expenses')->truncate();
        
        $this->createNewExpensesWithType($faker);
    }

    private function createNewExpensesWithType($faker)
    {
        $localities = DB::table('localities')->get(['id']);
        
        if ($localities->isEmpty()) {
            return;
        }

        $concepts = [
            'Mantenimiento de Equipos',
            'Material de Oficina',
            'Pago de Luz',
            'Servicio de Internet',
            'Combustible',
            'Limpieza',
            'Papelería',
            'Herramientas',
            'Transporte',
            'Agua'
        ];

        $descriptions = [
            'Pago del servicio mensual',
            'Compra de materiales necesarios',
            'Mantenimiento preventivo realizado',
            'Factura del periodo correspondiente',
            'Adquisición de herramientas',
            'Servicio contratado para el mes',
            'Insumos para operaciones diarias',
            'Reparación de equipo dañado',
            'Pago correspondiente al mes actual',
            'Material administrativo'
        ];

        $recordsToCreate = 5;
        $recordsCreated = 0;
        
        foreach ($localities as $locality) {
            if ($recordsCreated >= $recordsToCreate) {
                break;
            }
            
            $users = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                ->where('users.locality_id', $locality->id)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            if (empty($users)) {
                continue;
            }

            $expenseTypes = DB::table('expense_types')
                ->where('locality_id', $locality->id)
                ->get(['id', 'name']);

            if ($expenseTypes->isEmpty()) {
                continue;
            }

            $expenseTypesArray = $expenseTypes->toArray();
            $remaining = $recordsToCreate - $recordsCreated;
            $createForThis = min(2, $remaining);
            
            for ($i = 0; $i < $createForThis; $i++) {
                $expenseType = $faker->randomElement($expenseTypesArray);
                
                DB::table('general_expenses')->insert([
                    'locality_id' => $locality->id,
                    'created_by' => $faker->randomElement($users),
                    'expense_type_id' => $expenseType->id,
                    'concept' => $concepts[array_rand($concepts)],
                    'description' => $descriptions[array_rand($descriptions)],
                    'amount' => mt_rand(10, 50) * 100,
                    'expense_date' => $faker->dateTimeBetween('-60 days', 'now'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $recordsCreated++;
            }
        }
    }
}
