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

    /** @test */
    public function it_uses_the_higher_value_between_debt_current_and_the_payments_sum()
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality);
        $customer = $this->createCustomer($locality, $admin);
        $connection = $this->createConnection($locality, $admin, $customer);

        $debt = $this->createDebt($connection, [
            'amount' => 100,
            'debt_current' => 80,
        ]);

        Payment::factory()->create([
            'debt_id' => $debt->id,
            'customer_id' => $customer->id,
            'locality_id' => $locality->id,
            'created_by' => $admin->id,
            'amount' => 30,
        ]);

        $debt = $debt->fresh();

        $this->assertEqualsWithDelta(80, $debt->total_paid, 0.01);
        $this->assertEqualsWithDelta(20, $debt->remaining_amount, 0.01);
    }

    /** @test */
    public function it_knows_when_a_debt_is_fully_paid()
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality);
        $customer = $this->createCustomer($locality, $admin);
        $connection = $this->createConnection($locality, $admin, $customer);

        $unpaid = $this->createDebt($connection, ['amount' => 100]);
        $paid = $this->createDebt($connection, ['amount' => 100, 'debt_current' => 100]);

        $this->assertFalse($unpaid->isPaid());
        $this->assertTrue($paid->isPaid());
    }

    /** @test */
    public function it_soft_deletes_its_payments_when_the_debt_is_deleted()
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality);
        $customer = $this->createCustomer($locality, $admin);
        $connection = $this->createConnection($locality, $admin, $customer);
        $debt = $this->createDebt($connection, ['amount' => 100]);

        $payment = Payment::factory()->create([
            'debt_id' => $debt->id,
            'customer_id' => $customer->id,
            'locality_id' => $locality->id,
            'created_by' => $admin->id,
            'amount' => 100,
        ]);

        $debt->delete();

        $this->assertSoftDeleted('debts', ['id' => $debt->id]);
        $this->assertSoftDeleted('payments', ['id' => $payment->id]);
    }
}
