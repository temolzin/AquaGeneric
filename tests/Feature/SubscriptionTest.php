<?php

namespace Tests\Feature;

use App\Models\Locality;
use App\Models\Membership;
use App\Models\TokenHandler;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_locality_without_a_token_has_no_subscription()
    {
        $locality = Locality::factory()->create();

        $this->assertSame(Locality::SUBSCRIPTION_NONE, $locality->getSubscriptionStatus());
        $this->assertNull($locality->generateMembershipToken());
    }

    /** @test */
    public function generating_a_token_activates_the_locality_subscription()
    {
        $membership = Membership::factory()->create(['term_months' => 12]);
        $locality = Locality::factory()->create([
            'membership_id' => $membership->id,
            'membership_assigned_at' => now()->toDateString(),
        ]);
        $user = User::factory()->create(['locality_id' => $locality->id]);

        $token = $locality->generateMembershipToken();

        $this->assertNotNull($token);
        $this->assertSame($token, $locality->fresh()->token);
        $this->assertSame(Locality::SUBSCRIPTION_ACTIVE, $locality->fresh()->getSubscriptionStatus());

        $validation = TokenHandler::verifyToken($token, $user);

        $this->assertTrue($validation['valid']);
        $this->assertSame($locality->id, $validation['data']['idLocality']);
    }

    /** @test */
    public function an_expired_membership_generates_an_expired_token()
    {
        $membership = Membership::factory()->create(['term_months' => 12]);
        $locality = Locality::factory()->create([
            'membership_id' => $membership->id,
            'membership_assigned_at' => now()->subMonths(13)->toDateString(),
        ]);
        $user = User::factory()->create(['locality_id' => $locality->id]);

        $token = $locality->generateMembershipToken();
        $validation = TokenHandler::verifyToken($token, $user);

        $this->assertSame(Locality::SUBSCRIPTION_EXPIRED, $locality->fresh()->getSubscriptionStatus());
        $this->assertFalse($validation['valid']);
        $this->assertSame('Token expirado.', $validation['error']);
    }

    /** @test */
    public function a_tampered_token_is_rejected()
    {
        $membership = Membership::factory()->create();
        $locality = Locality::factory()->create([
            'membership_id' => $membership->id,
            'membership_assigned_at' => now()->toDateString(),
        ]);
        $user = User::factory()->create(['locality_id' => $locality->id]);

        $token = $locality->generateMembershipToken();
        $tampered = substr($token, 0, -3).'abc';

        $validation = TokenHandler::verifyToken($tampered, $user);

        $this->assertFalse($validation['valid']);
    }

    /** @test */
    public function a_token_from_another_locality_is_rejected()
    {
        $membership = Membership::factory()->create();
        $firstLocality = Locality::factory()->create([
            'membership_id' => $membership->id,
            'membership_assigned_at' => now()->toDateString(),
        ]);
        $secondLocality = Locality::factory()->create([
            'membership_id' => $membership->id,
            'membership_assigned_at' => now()->toDateString(),
        ]);
        $secondUser = User::factory()->create(['locality_id' => $secondLocality->id]);

        $token = $firstLocality->generateMembershipToken();
        $validation = TokenHandler::verifyToken($token, $secondUser);

        $this->assertFalse($validation['valid']);
        $this->assertSame('El token no pertenece a esta localidad.', $validation['error']);
    }
}
