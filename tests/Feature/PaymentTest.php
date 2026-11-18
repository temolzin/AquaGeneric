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
        [$admin, $customer, $connection, $debt] = $this->scenario();

        $this->actingAs($admin)
            ->post(route('payments.store'), $this->paymentPayload($debt, $customer, 40))
            ->assertRedirect(route('payments.index'))
            ->assertSessionHas('success');

        $debt = $debt->fresh();

        $this->assertEqualsWithDelta(40, (float) $debt->debt_current, 0.01);
        $this->assertSame(Debt::STATUS_PARTIAL, $debt->status);
        $this->assertSame(1, Payment::count());
        $this->assertDatabaseHas('payments', [
            'debt_id' => $debt->id,
            'amount' => 40,
            'method' => 'cash',
            'is_future_payment' => false,
        ]);
    }

    /** @test */
    public function a_payment_covering_the_full_amount_marks_the_debt_as_paid()
    {
        [$admin, $customer, $connection, $debt] = $this->scenario();

        $this->actingAs($admin)
            ->post(route('payments.store'), $this->paymentPayload($debt, $customer, 100))
            ->assertSessionHas('success');

        $this->assertSame(Debt::STATUS_PAID, $debt->fresh()->status);
        $this->assertEqualsWithDelta(100, (float) $debt->fresh()->debt_current, 0.01);
    }

    /** @test */
    public function it_rejects_a_payment_bigger_than_the_pending_amount()
    {
        [$admin, $customer, $connection, $debt] = $this->scenario();

        $this->actingAs($admin)
            ->post(route('payments.store'), $this->paymentPayload($debt, $customer, 150))
            ->assertSessionHas('error');

        $this->assertSame(0, Payment::count());
        $this->assertEqualsWithDelta(0, (float) $debt->fresh()->debt_current, 0.01);
    }

    /** @test */
    public function a_future_debt_requires_the_future_payment_flag()
    {
        [$admin, $customer, $connection, $debt] = $this->scenario(true);

        $this->actingAs($admin)
            ->post(route('payments.store'), $this->paymentPayload($debt, $customer, 100))
            ->assertSessionHas('error');

        $this->assertSame(0, Payment::count());

        $this->actingAs($admin)
            ->post(route('payments.store'), array_merge(
                $this->paymentPayload($debt, $customer, 100),
                ['is_future_payment' => 1]
            ))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'debt_id' => $debt->id,
            'is_future_payment' => true,
        ]);
    }

    /** @test */
    public function a_current_debt_cannot_be_paid_as_a_future_payment()
    {
        [$admin, $customer, $connection, $debt] = $this->scenario();

        $this->actingAs($admin)
            ->post(route('payments.store'), array_merge(
                $this->paymentPayload($debt, $customer, 100),
                ['is_future_payment' => 1]
            ))
            ->assertSessionHas('error');

        $this->assertSame(0, Payment::count());
    }

    /**
     * Build the minimum data required by the payment flow.
     *
     * @return array
     */
    private function scenario(bool $futureDebt = false): array
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality, ['viewPayments']);
        $customer = $this->createCustomer($locality, $admin);
        $connection = $this->createConnection($locality, $admin, $customer);

        $dates = $futureDebt
            ? [
                'start_date' => now()->addMonthNoOverflow()->startOfMonth()->toDateString(),
                'end_date' => now()->addMonthNoOverflow()->endOfMonth()->toDateString(),
            ]
            : [
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->endOfMonth()->toDateString(),
            ];

        $debt = $this->createDebt($connection, array_merge(['amount' => 100], $dates));

        return [$admin, $customer, $connection, $debt];
    }

    private function paymentPayload(Debt $debt, $customer, float $amount): array
    {
        return [
            'debt_id' => $debt->id,
            'customer_id' => $customer->id,
            'amount' => $amount,
            'method' => 'cash',
            'note' => 'Pago de prueba',
        ];
    }
}
