<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MovementsHistorySeeder extends Seeder
{
    public function run()
    {
        $modules = [
            'pagos',
            'deudas',
            'costos',
            'general_expenses',
            'general_earnings'
        ];

        $users = DB::table('users')->pluck('id')->toArray();

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
