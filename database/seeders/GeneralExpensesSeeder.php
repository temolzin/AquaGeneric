<?php

namespace Database\Seeders;
use App\Models\GeneralExpense;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class GeneralExpensesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $this->fixExistingExpensesWithoutType();
        
        $this->createNewExpensesWithType($faker);
    }

    private function fixExistingExpensesWithoutType()
    {
        $expensesWithoutType = DB::table('general_expenses')
            ->whereNull('expense_type_id')
            ->get();

        foreach ($expensesWithoutType as $expense) {
            $expenseType = DB::table('expense_types')
                ->where('locality_id', $expense->locality_id)
                ->first();

            if ($expenseType) {
                DB::table('general_expenses')
                    ->where('id', $expense->id)
                    ->update([
                        'expense_type_id' => $expenseType->id,
                        'updated_at' => now()
                    ]);
            } else {
                $existingGeneralType = DB::table('expense_types')
                    ->where('locality_id', $expense->locality_id)
                    ->where('name', 'Gastos Generales')
                    ->first();

                if ($existingGeneralType) {
                    $expenseTypeId = $existingGeneralType->id;
                } else {
                    $expenseTypeId = DB::table('expense_types')->insertGetId([
                        'name' => 'Gastos Generales',
                        'description' => 'Gastos varios de operación',
                        'color' => '#95a5a6',
                        'locality_id' => $expense->locality_id,
                        'created_by' => $expense->created_by ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('general_expenses')
                    ->where('id', $expense->id)
                    ->update([
                        'expense_type_id' => $expenseTypeId,
                        'updated_at' => now()
                    ]);
            }
        }
    }

    private function createNewExpensesWithType($faker)
    {
        $localities = DB::table('localities')->get(['id']);
        
        if ($localities->isEmpty()) {
            return;
        }

        foreach ($localities as $locality) {
            $users = DB::table('users')
                ->where('locality_id', $locality->id)
                ->pluck('id')
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

            $numberOfExpenses = rand(3, 5);
            
            for ($i = 0; $i < $numberOfExpenses; $i++) {
                $expenseType = $faker->randomElement($expenseTypes->toArray());
                
                DB::table('general_expenses')->insert([
                    'locality_id' => $locality->id,
                    'created_by' => $faker->randomElement($users),
                    'expense_type_id' => $expenseType->id, 
                    'concept' => $faker->word(),
                    'description' => $faker->sentence(),
                    'amount' => mt_rand(10, 50) * 100,
                    'expense_date' => $faker->dateTimeBetween('-60 days', 'now'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
