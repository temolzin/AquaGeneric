<?php

namespace Tests\Concerns;

use App\Models\Cost;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\Locality;
use App\Models\User;
use App\Models\WaterConnection;
use Spatie\Permission\Models\Permission;

trait BuildsWaterScenario
{
    protected function createLocality(array $attributes = []): Locality
    {
        return Locality::factory()->create($attributes);
    }

    protected function createUserFor(Locality $locality, array $permissions = []): User
    {
        $user = User::factory()->create(['locality_id' => $locality->id]);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['description' => $permission]
            );
            $user->givePermissionTo($permission);
        }

        return $user;
    }

    protected function createCustomer(Locality $locality, User $creator, array $attributes = []): Customer
    {
        return Customer::factory()->create(array_merge([
            'locality_id' => $locality->id,
            'created_by' => $creator->id,
        ], $attributes));
    }

    protected function createConnection(
        Locality $locality,
        User $creator,
        Customer $customer,
        array $attributes = []
    ): WaterConnection {
        if (!isset($attributes['cost_id'])) {
            $attributes['cost_id'] = Cost::factory()->create([
                'locality_id' => $locality->id,
                'created_by' => $creator->id,
            ])->id;
        }

        return WaterConnection::factory()->create(array_merge([
            'locality_id' => $locality->id,
            'created_by' => $creator->id,
            'customer_id' => $customer->id,
        ], $attributes));
    }

    protected function createDebt(WaterConnection $connection, array $attributes = []): Debt
    {
        return Debt::factory()->create(array_merge([
            'water_connection_id' => $connection->id,
            'locality_id' => $connection->locality_id,
            'created_by' => $connection->created_by,
        ], $attributes));
    }
}
