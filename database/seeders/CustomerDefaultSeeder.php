<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\WaterConnection;
use App\Models\Locality;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class CustomerDefaultSeeder extends Seeder
{
    private const MIN_AMOUNT = 50;
    private const MAX_AMOUNT = 300;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $alonso = $this->createAlonsoUser();
        
        if (!$alonso) {
            return;
        }

        $alonsoCustomer = $this->createAlonsoCustomer($alonso);
        
        if (!$alonsoCustomer) {
            return;
        }

        $waterConnections = $this->createAlonsoWaterConnections($alonso, $alonsoCustomer);
        
        if (empty($waterConnections)) {
            return;
        }

        $this->createAlonsoDebts($alonsoCustomer, $waterConnections);

        $this->createAlonsoPayments($alonso, $alonsoCustomer, $waterConnections);
    }

    private function createAlonsoUser()
    {
        $now = now();

        $alonsoData = [
            'id' => 5,
            'email' => 'alonso@gmail.com',
            'locality_id' => 1,
            'name' => 'Alonso',
            'last_name' => 'Gutiérrez López',
            'phone' => '5556161351',
            'password' => '12345',
            'role' => 'Cliente',
        ];

        try {
            $existingUser = User::find(5) ?? User::where('email', $alonsoData['email'])->first();

            if ($existingUser) {
                $existingUser->update([
                    'email' => $alonsoData['email'],
                    'name' => $alonsoData['name'],
                    'last_name' => $alonsoData['last_name'],
                    'phone' => $alonsoData['phone'],
                    'password' => Hash::make($alonsoData['password']),
                    'locality_id' => $alonsoData['locality_id'],
                    'updated_at' => $now,
                ]);
                $user = $existingUser;
            } else {
                $user = User::create([
                    'id' => $alonsoData['id'],
                    'email' => $alonsoData['email'],
                    'name' => $alonsoData['name'],
                    'last_name' => $alonsoData['last_name'],
                    'phone' => $alonsoData['phone'],
                    'password' => Hash::make($alonsoData['password']),
                    'locality_id' => $alonsoData['locality_id'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            }

            $user->syncRoles([$alonsoData['role']]);

            return $user;
        } catch (\Exception $e) {
            $this->command->error("  Error al crear/actualizar usuario Alonso: " . $e->getMessage());
            return null;
        }
    }

    private function createAlonsoCustomer($alonso)
    {
        try {
            $locality = Locality::where('name', 'Smallville')->first();

            if (!$locality) {
                return null;
            }

            $customer = Customer::updateOrCreate(
                ['user_id' => $alonso->id],
                [
                    'name' => 'Alonso',
                    'last_name' => 'Gutiérrez López',
                    'email' => $alonso->email,
                    'locality' => 'Smallville',
                    'state' => 'Kansas',
                    'zip_code' => '66002',
                    'block' => '1',
                    'street' => 'Calle Principal',
                    'exterior_number' => '123',
                    'interior_number' => '',
                    'marital_status' => 0,
                    'status' => 1,
                    'responsible_name' => null,
                    'locality_id' => $locality->id,
                    'created_by' => 1,
                ]
            );

            return $customer;
        } catch (\Exception $e) {
            $this->command->error("  Error al crear cliente para Alonso: " . $e->getMessage());
            return null;
        }
    }

    private function createAlonsoWaterConnections($alonso, $alonsoCustomer)
    {
        try {
            $smallvilleLocality = Locality::where('name', 'Smallville')->first();

            if (!$smallvilleLocality) {
                return [];
            }

            $costs = DB::table('costs')->where('locality_id', $smallvilleLocality->id)->pluck('id')->toArray();

            if (empty($costs)) {
                $allCosts = DB::table('costs')->pluck('id')->toArray();
                $costs = !empty($allCosts) ? $allCosts : [];
            }

            $users = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                ->where('users.locality_id', $smallvilleLocality->id)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            $sections = DB::table('sections')->where('locality_id', $smallvilleLocality->id)->pluck('id')->toArray();

            if (empty($costs) || empty($users) || empty($sections)) {
                return [];
            }

            $connections = [];

            $cierraCerrada = WaterConnection::where('customer_id', $alonsoCustomer->id)
                ->where('name', 'Cierra Hermosa')
                ->first();

            $casasJaver = WaterConnection::where('customer_id', $alonsoCustomer->id)
                ->where('name', 'Casas Javer')
                ->first();

            if ($cierraCerrada && $casasJaver) {
                return [$cierraCerrada->toArray(), $casasJaver->toArray()];
            }

            if (!$cierraCerrada) {
                $connection1 = WaterConnection::create([
                    'customer_id' => $alonsoCustomer->id,
                    'locality_id' => $smallvilleLocality->id,
                    'cost_id' => $costs[0],
                    'created_by' => $users[0],
                    'name' => 'Cierra Hermosa',
                    'block' => 'Tecamac',
                    'street' => 'Calle Principal',
                    'exterior_number' => '123',
                    'interior_number' => 'A',
                    'occupants_number' => 4,
                    'water_days' => json_encode(['monday', 'wednesday']),
                    'has_water_pressure' => true,
                    'has_cistern' => false,
                    'type' => 'residencial',
                    'section_id' => $sections[0],
                ]);

                $connections[] = $connection1->toArray();
            } else {
                $connections[] = $cierraCerrada->toArray();
            }

            if (!$casasJaver) {
                $connection2 = WaterConnection::create([
                    'customer_id' => $alonsoCustomer->id,
                    'locality_id' => $smallvilleLocality->id,
                    'cost_id' => $costs[0],
                    'created_by' => $users[0],
                    'name' => 'Casas Javer',
                    'block' => 'Tecamac',
                    'street' => 'Calle Principal',
                    'exterior_number' => '147',
                    'interior_number' => '89',
                    'occupants_number' => 2,
                    'water_days' => json_encode(['tuesday', 'thursday']),
                    'has_water_pressure' => true,
                    'has_cistern' => true,
                    'type' => 'residencial',
                    'section_id' => $sections[0],
                ]);

                $connections[] = $connection2->toArray();
            } else {
                $connections[] = $casasJaver->toArray();
            }

            return $connections;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function createAlonsoDebts($alonsoCustomer, $waterConnections)
    {
        try {
            $faker = Faker::create();
            $startDate = Carbon::now()->subMonths(2)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $serviceId = DB::table('debt_categories')->where('name', 'Servicio de agua')->value('id') ?? 1;

            foreach ($waterConnections as $waterConnection) {
                $existingDebts = DB::table('debts')
                    ->where('water_connection_id', $waterConnection['id'] ?? $waterConnection->id)
                    ->count();

                if ($existingDebts > 0) {
                    continue;
                }

                $connectionId = $waterConnection['id'] ?? $waterConnection->id;
                $localityId = $waterConnection['locality_id'] ?? $waterConnection->locality_id;

                $createdBy = $this->getUserForLocality($localityId);

                if (empty($createdBy)) {
                    continue;
                }

                $debtStartDate = $faker->dateTimeBetween($startDate, $endDate);
                $debtDuration = $faker->numberBetween(1, 12);
                $debtEndDate = Carbon::instance($debtStartDate)->addMonths($debtDuration);
                $debtEndDate = min($debtEndDate, $endDate);

                $amount = rand(self::MIN_AMOUNT, self::MAX_AMOUNT);
                $paymentAmount = rand(0, $amount);
                $debtCurrent = $amount - $paymentAmount;

                $status = $this->determineDebtStatus($paymentAmount, $debtCurrent);

                DB::table('debts')->insert([
                    'water_connection_id' => $connectionId,
                    'locality_id' => $localityId,
                    'created_by' => $createdBy,
                    'debt_category_id' => $serviceId,
                    'start_date' => $debtStartDate,
                    'end_date' => $debtEndDate,
                    'amount' => $amount,
                    'debt_current' => $debtCurrent,
                    'status' => $status,
                    'note' => 'Deuda generada de prueba para Alonso',
                    'deleted_at' => null,
                    'created_at' => now(),
                ]);
            }
        } catch (\Exception $e) {

        }
    }

    private function createAlonsoPayments($alonso, $alonsoCustomer, $waterConnections)
    {
        try {
            $faker = Faker::create();

            foreach ($waterConnections as $waterConnection) {
                $connectionId = $waterConnection['id'] ?? $waterConnection->id;

                $debts = DB::table('debts')
                    ->where('water_connection_id', $connectionId)
                    ->get();

                foreach ($debts as $debt) {
                    $existingPayments = DB::table('payments')
                        ->where('debt_id', $debt->id)
                        ->count();

                    if ($existingPayments > 0) {
                        continue;
                    }

                    $localityUserIds = DB::table('users')
                        ->where('locality_id', $debt->locality_id)
                        ->where('id', '!=', $alonso->id)
                        ->whereIn('id', DB::table('model_has_roles')
                            ->whereIn('role_id', DB::table('roles')
                                ->whereIn('name', ['Supervisor', 'Secretaria'])
                                ->pluck('id')
                            )
                            ->pluck('model_id')
                        )
                        ->pluck('id')
                        ->toArray();

                    if (empty($localityUserIds)) {
                        $localityUserIds = [1];
                    }

                    $amount = $debt->debt_current > 0 ? $debt->debt_current : $debt->amount;
                    $createdAt = $faker->dateTimeBetween($debt->start_date, $debt->end_date);

                    DB::table('payments')->insert([
                        'customer_id' => $alonsoCustomer->id,
                        'debt_id' => $debt->id,
                        'created_by' => $faker->randomElement($localityUserIds),
                        'amount' => $amount,
                        'locality_id' => $debt->locality_id,
                        'method' => $faker->randomElement(['cash', 'card', 'transfer']),
                        'note' => 'Pago correspondiente a la deuda #' . $debt->id,
                        'deleted_at' => null,
                        'created_at' => $createdAt,
                    ]);
                }
            }
        } catch (\Exception $e) {

        }
    }

    private function getUserForLocality($localityId)
    {
        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
            ->where('users.locality_id', $localityId)
            ->distinct()
            ->value('users.id');
    }

    private function determineDebtStatus($paymentAmount, $debtCurrent)
    {
        if ($paymentAmount == 0) {
            return 'pending';
        } elseif ($debtCurrent == 0) {
            return 'paid';
        } else {
            return 'partial';
        }
    }
}
