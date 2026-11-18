<?php

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\WaterConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsWaterScenario;
use Tests\TestCase;

class LocalityScopeTest extends TestCase
{
    use RefreshDatabase, BuildsWaterScenario;

    /** @test */
    public function debts_and_connections_are_scoped_to_the_authenticated_user_locality()
    {
        $firstLocality = $this->createLocality();
        $secondLocality = $this->createLocality();

        $firstAdmin = $this->createUserFor($firstLocality);
        $secondAdmin = $this->createUserFor($secondLocality);

        $firstConnection = $this->createConnection(
            $firstLocality,
            $firstAdmin,
            $this->createCustomer($firstLocality, $firstAdmin)
        );
        $secondConnection = $this->createConnection(
            $secondLocality,
            $secondAdmin,
            $this->createCustomer($secondLocality, $secondAdmin)
        );

        $firstDebt = $this->createDebt($firstConnection);
        $secondDebt = $this->createDebt($secondConnection);

        $this->actingAs($firstAdmin);

        $this->assertSame(1, WaterConnection::count());
        $this->assertSame(1, Debt::count());
        $this->assertTrue(WaterConnection::whereKey($firstConnection->id)->exists());
        $this->assertFalse(WaterConnection::whereKey($secondConnection->id)->exists());
        $this->assertTrue(Debt::whereKey($firstDebt->id)->exists());
        $this->assertFalse(Debt::whereKey($secondDebt->id)->exists());
    }
}
