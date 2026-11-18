<?php

namespace Tests\Unit;

use App\Models\Debt;
use App\Models\Payment;
use App\Models\WaterConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsWaterScenario;
use Tests\TestCase;

class WaterConnectionStatusTest extends TestCase
{
    use RefreshDatabase, BuildsWaterScenario;

    /** @test */
    public function a_connection_without_debts_is_reported_as_paid()
    {
        $connection = $this->makeConnection();

        $this->assertSame(WaterConnection::VIEW_STATUS_PAID, $connection->status_calculated);
        $this->assertFalse($connection->hasDebt());
        $this->assertEqualsWithDelta(0, $connection->getPendingBalance(), 0.01);
    }

    /** @test */
    public function a_connection_with_a_partially_paid_debt_is_reported_as_in_debt()
    {
        $connection = $this->makeConnection();
        $debt = $this->createDebt($connection, ['amount' => 100]);

        $this->registerPayment($debt, $connection, 50);

        $this->assertSame(WaterConnection::VIEW_STATUS_DEBT, $connection->fresh()->status_calculated);
        $this->assertTrue($connection->fresh()->hasDebt());
        $this->assertEqualsWithDelta(50, $connection->fresh()->getPendingBalance(), 0.01);
    }

    /** @test */
    public function a_connection_with_a_paid_future_debt_is_reported_as_advanced()
    {
        $connection = $this->makeConnection();
        $debt = $this->createDebt($connection, [
            'amount' => 100,
            'debt_current' => 100,
            'status' => Debt::STATUS_PAID,
            'start_date' => now()->addMonthNoOverflow()->startOfMonth()->toDateString(),
            'end_date' => now()->addMonthNoOverflow()->endOfMonth()->toDateString(),
        ]);

        $this->registerPayment($debt, $connection, 100);

        $this->assertSame(WaterConnection::VIEW_STATUS_ADVANCED, $connection->fresh()->status_calculated);
        $this->assertFalse($connection->fresh()->hasDebt());
    }

    /** @test */
    public function a_canceled_connection_is_reported_as_canceled_even_if_it_has_debts()
    {
        $connection = $this->makeConnection(['is_canceled' => true]);
        $debt = $this->createDebt($connection, ['amount' => 100]);

        $this->registerPayment($debt, $connection, 10);

        $this->assertSame(WaterConnection::VIEW_STATUS_CANCELED, $connection->status_calculated);
    }

    /** @test */
    public function it_sums_only_the_pending_amount_of_every_debt()
    {
        $connection = $this->makeConnection();

        $first = $this->createDebt($connection, ['amount' => 100]);
        $this->createDebt($connection, ['amount' => 80]);
        $this->registerPayment($first, $connection, 40);

        $this->assertEqualsWithDelta(140, $connection->fresh()->getPendingBalance(), 0.01);
    }

    /** @test */
    public function canceled_connections_are_hidden_by_the_default_scope()
    {
        $connection = $this->makeConnection(['is_canceled' => true]);

        $this->assertFalse(WaterConnection::whereKey($connection->id)->exists());
        $this->assertTrue(
            WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
                ->whereKey($connection->id)
                ->exists()
        );
    }

    private function makeConnection(array $attributes = []): WaterConnection
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality);
        $customer = $this->createCustomer($locality, $admin);

        return $this->createConnection($locality, $admin, $customer, $attributes);
    }

    private function registerPayment(Debt $debt, WaterConnection $connection, float $amount): Payment
    {
        return Payment::factory()->create([
            'debt_id' => $debt->id,
            'customer_id' => $connection->customer_id,
            'locality_id' => $connection->locality_id,
            'created_by' => $connection->created_by,
            'amount' => $amount,
        ]);
    }
}
