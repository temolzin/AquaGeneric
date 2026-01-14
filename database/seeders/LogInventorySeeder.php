<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LogInventory;
use App\Models\Inventory;
use Carbon\Carbon;
use App\Models\User;
class LogInventorySeeder extends Seeder
{
    public function run()
    {
        $inventories = Inventory::all();

        if ($inventories->isEmpty()) {
            $this->command->error('No hay inventarios registrados. Ejecuta primero InventoryTableSeeder.');
            return;
        }

        $totalLogs = 0;

        foreach ($inventories as $inventory) {
            $originalAmount = $inventory->amount;
            
            $numLogs = rand(1, 3);
            $logsForThisInventory = [];
            
            $currentAmount = $originalAmount;
            
            for ($i = 0; $i < $numLogs; $i++) {
                $change = rand(-30, 20);
                $previousAmount = max(0, $currentAmount - $change);
                
                $descriptions = [
                    'Salida para mantenimiento',
                    'Entrada de compra',
                    'Ajuste de inventario',
                    'Uso en reparación',
                    'Devolución de material',
                    'Transferencia a otra localidad'
                ];
                
                $description = $descriptions[array_rand($descriptions)];
                $daysAgo = ($i + 1) * rand(3, 10);
                $createdAt = Carbon::now()->subDays($daysAgo);
                
                $logsForThisInventory[] = [
                    'inventory_id' => $inventory->id,
                    'locality_id' => $inventory->locality_id,
                    'created_by' => User::inRandomOrder()->first()->id ?? 1,
                    'previous_amount' => $previousAmount,
                    'amount' => $currentAmount,
                    'description' => $description . " (" . ($change >= 0 ? "+" : "") . $change . ")",
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
                
                $currentAmount = $previousAmount;
            }
            
            if (!empty($logsForThisInventory)) {
                $logsForThisInventory = array_reverse($logsForThisInventory);
                LogInventory::insert($logsForThisInventory);
                $totalLogs += count($logsForThisInventory);
                
            }
        }

    }
}
