<?php

namespace Tests\Unit;

use App\Models\Debt;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsWaterScenario;
use Tests\TestCase;

class DebtTest extends TestCase
{
    use RefreshDatabase, BuildsWaterScenario;

    /** @test */
    public function it_calculates_the_paid_and_remaining_amount_from_payments()
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality);
        $customer = $this->createCustomer($locality, $admin);
        $connection = $this->createConnection($locality, $admin, $customer);
        $debt = $this->createDebt($connection, ['amount' => 100]);

        Payment::factory()->create([
            'debt_id' => $debt->id,
            'customer_id' => $customer->id,
            'locality_id' => $locality->id,
            'created_by' => $admin->id,
            'amount' => 40,
        ]);
        Payment::factory()->create([
            'debt_id' => $debt->id,
            'customer_id' => $customer->id,
            'locality_id' => $locality->id,
            'created_by' => $admin->id,
            'amount' => 10,
        ]);

        $debt = $debt->fresh();

        $this->assertEqualsWithDelta(50, $debt->total_paid, 0.01);
        $this->assertEqualsWithDelta(50, $debt->remaining_amount, 0.01);
    }
}
