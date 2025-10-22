<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MigrateCustomersToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = DB::table('customers')->whereNull('user_id')->get();

        foreach ($customers as $customer) {
            $userId = DB::table('users')->insertGetId([
                'name'        => $customer->name,
                'last_name'   => $customer->last_name,
                'email'       => $customer->email,
                'password'    => Hash::make('12345'),
                'phone'       => null,
                'locality_id' => $customer->locality_id ?? null,
                'created_at'  => null,
                'updated_at'  => null,
                'deleted_at'  => null,
            ]);

            DB::table('customers')
                ->where('id', $customer->id)
                ->update(['user_id' => $userId]);
        }

        $alonsoUser = DB::table('users')->where('email', 'alonso@gmail.com')->first();
        
        if ($alonsoUser) {
            $existingCustomer = DB::table('customers')->where('user_id', $alonsoUser->id)->first();
            
            if (!$existingCustomer) {
                $sampleCustomer = DB::table('customers')->first();
                
                if ($sampleCustomer) {
                    $customerData = [
                        'user_id' => $alonsoUser->id,
                        'locality_id' => $alonsoUser->locality_id,
                        'name' => $alonsoUser->name,
                        'last_name' => $alonsoUser->last_name,
                        'email' => $alonsoUser->email,
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    foreach ($sampleCustomer as $field => $value) {
                        if ($field !== 'id' && $field !== 'user_id' && !array_key_exists($field, $customerData)) {
                            $customerData[$field] = $value;
                        }
                    }

                    DB::table('customers')->insert($customerData);
                } else {
                    $customerData = [
                        'user_id' => $alonsoUser->id,
                        'locality_id' => $alonsoUser->locality_id,
                        'name' => $alonsoUser->name,
                        'last_name' => $alonsoUser->last_name,
                        'email' => $alonsoUser->email,
                        'locality' => 'Default Locality',
                        'zip_code' => '00000',
                        'state' => 'Default State',
                        'street' => 'Default Street',
                        'exterior_number' => '0',
                        'interior_number' => null,
                        'block' => '0',
                        'city' => 'Default City',
                        'suburb' => 'Default Suburb',
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    DB::table('customers')->insert($customerData);
                }
            }

            $userModel = User::find($alonsoUser->id);
            if ($userModel && !$userModel->hasRole('Cliente')) {
                $userModel->assignRole('Cliente');
            }
        }
    }
}
