<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class MovementsHistorySeeder extends Seeder
{
    public function run()
    {
        $modules = [
            'payments',
            'debts',
            'costs',
            'general_expenses',
            'general_earnings'
        ];

        $users = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [
                User::ROLE_SUPERVISOR,
                User::ROLE_SECRETARY
            ])
            ->distinct()
            ->pluck('users.id')
            ->toArray();


        $records = [];

        $recordId = 1;

        foreach ($modules as $module) {

            foreach ($users as $userId) {

                for ($i = 0; $i < 2; $i++) {

                    $before = [
                        'id' => $recordId,
                        'amount' => rand(100, 1000),
                        'status' => 'pending'
                    ];

                    $current = [
                        'id' => $recordId,
                        'amount' => rand(100, 1000),
                        'status' => 'paid'
                    ];

                    $records[] = [
                        'alter_by' => $userId,
                        'module' => $module,
                        'action' => 'update',
                        'record_id' => $recordId,
                        'before_data' => json_encode($before),
                        'current_data' => json_encode($current),
                        'created_at' => Carbon::now()->subDays(rand(0, 30)),
                        'updated_at' => Carbon::now(),
                    ];

                    $recordId++;
                }
            }
        }

        DB::table('movements_history')->insert($records);
    }
}
