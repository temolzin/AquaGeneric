<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckSubscription;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\BuildsWaterScenario;
use Tests\TestCase;

class DebtDeletionTest extends TestCase
{
    use RefreshDatabase, BuildsWaterScenario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(CheckSubscription::class);
    }

    /** @test */
    public function deleting_a_debt_soft_deletes_it_and_records_the_audit_trail()
    {
        $locality = $this->createLocality();
        $admin = $this->createUserFor($locality, ['viewDebts']);
        $customer = $this->createCustomer($locality, $admin);
        $connection = $this->createConnection($locality, $admin, $customer);
        $debt = $this->createDebt($connection, ['amount' => 100]);

        Payment::factory()->create([
            'debt_id' => $debt->id,
            'customer_id' => $customer->id,
            'locality_id' => $locality->id,
            'created_by' => $admin->id,
            'amount' => 100,
        ]);

        $this->actingAs($admin)
            ->from('/debts')
            ->delete(route('debts.destroy', $debt->id))
            ->assertRedirect('/debts')
            ->assertSessionHas('success');

        $this->assertSoftDeleted('debts', ['id' => $debt->id]);
        $this->assertSoftDeleted('payments', ['debt_id' => $debt->id]);
        $this->assertDatabaseHas('movements_history', [
            'alter_by' => $admin->id,
            'module' => 'deudas',
            'action' => 'delete',
            'record_id' => $debt->id,
        ]);

        $history = DB::table('movements_history')->latest('id')->first();
        $before = json_decode($history->before_data, true);

        $this->assertEqualsWithDelta(100, (float) $before['amount'], 0.01);
    }
}
