<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckSubscription;
use App\Models\Debt;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsWaterScenario;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase, BuildsWaterScenario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(CheckSubscription::class);
    }

    /** @test */
    public function a_partial_payment_marks_the_debt_as_partial()
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality, ['viewPayments']);
        $customer = $this->createCustomer($locality, $admin);
        $connection = $this->createConnection($locality, $admin, $customer);
        $debt = $this->createDebt($connection, ['amount' => 100]);

        $this->actingAs($admin)
            ->post(route('payments.store'), [
                'debt_id' => $debt->id,
                'customer_id' => $customer->id,
                'amount' => 40,
                'method' => 'cash',
                'note' => 'Pago de prueba',
            ])
            ->assertSessionHas('success');

        $this->assertSame(Debt::STATUS_PARTIAL, $debt->fresh()->status);
        $this->assertEqualsWithDelta(40, (float) $debt->fresh()->debt_current, 0.01);
        $this->assertSame(1, Payment::count());
    }
}
