<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\WaterConnection;
use App\Models\Debt;
use App\Models\Payment;
use App\Models\Locality;
use App\Models\User;
use Carbon\Carbon;

class AlonsoGutierrezSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::find(5);
        
        if (!$user) {
            return;
        }

        $locality = Locality::where('name', 'Smallville')->first();

        if (!$locality) {
            return;
        }

        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Alonso',
                'last_name' => 'Gutiérrez López',
                'email' => $user->email,
                'locality' => 'Smallville',
                'locality_id' => $locality->id,
                'state' => 'Kansas',
                'zip_code' => '66002',
                'block' => '1',
                'street' => 'Calle Principal',
                'exterior_number' => '123',
                'interior_number' => '',
                'marital_status' => 0,
                'status' => 1,
                'created_by' => 1,
            ]
        );

        if ($customer->waterConnections()->count() === 0) {
            $waterConnection1 = WaterConnection::create([
                'customer_id' => $customer->id,
                'locality_id' => $locality->id,
                'cost_id' => 1,
                'created_by' => 1,
                'name' => 'Toma de Agua 1',
                'block' => 'Tecamac',
                'street' => 'Calle Principal',
                'exterior_number' => '123',
                'interior_number' => 'A',
                'occupants_number' => 4,
                'water_days' => json_encode(['monday', 'wednesday']),
                'has_water_pressure' => true,
                'has_cistern' => false,
                'type' => 'residencial',
                'is_canceled' => false,
            ]);

            $waterConnection2 = WaterConnection::create([
                'customer_id' => $customer->id,
                'locality_id' => $locality->id,
                'cost_id' => 1,
                'created_by' => 1,
                'name' => 'Toma de Agua 2',
                'block' => 'Tecamac',
                'street' => 'Calle Principal',
                'exterior_number' => '147',
                'interior_number' => '89',
                'occupants_number' => 2,
                'water_days' => json_encode(['tuesday', 'thursday']),
                'has_water_pressure' => true,
                'has_cistern' => true,
                'type' => 'residencial',
                'is_canceled' => false,
            ]);

            $startDate = Carbon::now()->subMonths(2);
            $endDate = Carbon::now()->subMonths(1);

            $debt1 = Debt::create([
                'water_connection_id' => $waterConnection1->id,
                'locality_id' => $locality->id,
                'created_by' => 1,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'amount' => 500.00,
                'note' => 'Deuda del mes anterior',
            ]);

            $debt2 = Debt::create([
                'water_connection_id' => $waterConnection2->id,
                'locality_id' => $locality->id,
                'created_by' => 1,
                'start_date' => $endDate->copy()->addDay(),
                'end_date' => Carbon::now(),
                'amount' => 350.00,
                'note' => 'Deuda actual',
            ]);

            Payment::create([
                'customer_id' => $customer->id,
                'locality_id' => $locality->id,
                'created_by' => 1,
                'debt_id' => $debt1->id,
                'method' => 'cash',
                'amount' => 250.00,
                'note' => 'Pago parcial',
                'is_future_payment' => false,
            ]);

            Payment::create([
                'customer_id' => $customer->id,
                'locality_id' => $locality->id,
                'created_by' => 1,
                'debt_id' => $debt2->id,
                'method' => 'transfer',
                'amount' => 350.00,
                'note' => 'Pago completo',
                'is_future_payment' => false,
            ]);
        }
    }
}
