<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckSubscription;
use App\Models\Cost;
use App\Models\Debt;
use App\Models\DebtCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsWaterScenario;
use Tests\TestCase;

class DebtAssignmentTest extends TestCase
{
    use RefreshDatabase, BuildsWaterScenario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(CheckSubscription::class);
    }

    /** @test */
    public function it_assigns_the_service_fee_to_every_water_connection_of_the_locality()
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality, ['viewDebts']);
        $cost = Cost::factory()->create([
            'locality_id' => $locality->id,
            'created_by' => $admin->id,
            'price' => 150,
        ]);

        $firstCustomer = $this->createCustomer($locality, $admin);
        $secondCustomer = $this->createCustomer($locality, $admin);
        $this->createConnection($locality, $admin, $firstCustomer, ['cost_id' => $cost->id]);
        $this->createConnection($locality, $admin, $secondCustomer, ['cost_id' => $cost->id]);

        $this->actingAs($admin);

        $response = $this->from('/debts')->post(route('debts.assignAll'), [
            'start_date' => '2026-06',
            'end_date' => '2026-06',
            'note' => 'Cuota de junio',
        ]);

        $response->assertRedirect('/debts');
        $response->assertSessionHas('success');

        $this->assertSame(2, Debt::count());

        Debt::all()->each(function (Debt $debt) use ($locality, $admin) {
            $this->assertSame($locality->id, $debt->locality_id);
            $this->assertSame($admin->id, $debt->created_by);
            $this->assertEqualsWithDelta(150, (float) $debt->amount, 0.01);
            $this->assertSame(DebtCategory::serviceId(), $debt->debt_category_id);
            $this->assertSame('2026-06-01', $debt->start_date);
            $this->assertSame('2026-06-30', $debt->end_date);
            $this->assertSame(Debt::STATUS_PENDING, $debt->status);
        });
    }

    /** @test */
    public function it_does_not_duplicate_the_service_debt_for_the_same_period()
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality, ['viewDebts']);
        $cost = Cost::factory()->create([
            'locality_id' => $locality->id,
            'created_by' => $admin->id,
            'price' => 150,
        ]);

        $customer = $this->createCustomer($locality, $admin);
        $withDebt = $this->createConnection($locality, $admin, $customer, ['cost_id' => $cost->id]);
        $withoutDebt = $this->createConnection($locality, $admin, $customer, ['cost_id' => $cost->id]);

        $this->createDebt($withDebt, [
            'amount' => 150,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
        ]);

        $this->actingAs($admin);

        $this->from('/debts')
            ->post(route('debts.assignAll'), [
                'start_date' => '2026-06',
                'end_date' => '2026-06',
            ])
            ->assertSessionHas('success');

        $this->assertSame(2, Debt::count());
        $this->assertSame(1, Debt::where('water_connection_id', $withDebt->id)->count());
        $this->assertSame(1, Debt::where('water_connection_id', $withoutDebt->id)->count());
    }

    /** @test */
    public function it_informs_when_every_connection_already_has_a_debt_for_the_period()
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality, ['viewDebts']);
        $cost = Cost::factory()->create([
            'locality_id' => $locality->id,
            'created_by' => $admin->id,
            'price' => 150,
        ]);

        $customer = $this->createCustomer($locality, $admin);
        $connection = $this->createConnection($locality, $admin, $customer, ['cost_id' => $cost->id]);

        $this->createDebt($connection, [
            'amount' => 150,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
        ]);

        $this->actingAs($admin);

        $this->from('/debts')
            ->post(route('debts.assignAll'), [
                'start_date' => '2026-06',
                'end_date' => '2026-06',
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, Debt::count());
    }

    /** @test */
    public function a_user_without_the_view_debts_permission_cannot_assign_debts()
    {
        $locality = $this->createLocality();
        $user = $this->createUserFor($locality);

        $this->actingAs($user)
            ->post(route('debts.assignAll'), [
                'start_date' => '2026-06',
                'end_date' => '2026-06',
            ])
            ->assertForbidden();
    }
}
