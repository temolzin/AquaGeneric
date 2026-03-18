<?php

namespace Database\Seeders;

use App\Models\Token;
use Illuminate\Database\Seeder;
use App\Models\Locality;
use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;

class LocalitiesTableSeeder extends Seeder
{
    public function run()
    {
        if (Membership::count() === 0) {
            $this->call(MembershipsTableSeeder::class);
        }

        $localitiesData = [
            [
                'name' => 'Smallville',
                'municipality' => 'Smallville',
                'state' => 'Kansas',
                'zip_code' => '66002',
                'membership_name' => 'Premium Plan - 6 Months',
                'token_expired' => false,
            ],
            [
                'name' => 'Springfield',
                'municipality' => 'Springfield',
                'state' => 'Oregon',
                'zip_code' => '97477',
                'membership_name' => 'Enterprise Plan - 12 Months',
                'token_expired' => false,
            ],
            [
                'name' => 'Dunder Mifflin',
                'municipality' => 'Scranton',
                'state' => 'Pennsylvania',
                'zip_code' => '18503',
                'membership_name' => 'Basic Plan - 3 Months',
                'token_expired' => true,
            ],
        ];

        $defaultMembershipId = Membership::orderBy('id')->value('id');
        if (is_null($defaultMembershipId)) {
            throw new \Exception('No memberships available when seeding localities');
        }

        foreach ($localitiesData as $data) {
            $membership = Membership::firstOrCreate(
                ['name' => $data['membership_name']],
                [
                    'price' => 0,
                    'term_months' => 0,
                    'water_connections_number' => 0,
                    'users_number' => 0,
                    'created_by' => User::whereHas('roles', fn($q) => $q->where('name', User::ROLE_SUPERVISOR))->orderBy('id')->value('id'),
                ]
            );

            $locality = Locality::updateOrCreate(
                ['name' => $data['name']],
                [
                    'name' => $data['name'],
                    'municipality' => $data['municipality'],
                    'state' => $data['state'],
                    'zip_code' => $data['zip_code'],
                    'membership_id' => $membership->id,
                ]
            );

            $locality->token = $this->generateTokenData($locality->id, (bool) $data['token_expired']);
            $locality->save();
        }

        Locality::whereNull('membership_id')->get()->each(function ($locality) use ($defaultMembershipId) {
            $locality->membership_id = $defaultMembershipId;
            $locality->save();
        });

        Locality::whereNull('token')->orWhere('token', '')->get()->each(function ($locality) {
            $locality->token = $this->generateTokenData($locality->id, false);
            $locality->save();
        });

        Locality::whereNotNull('membership_id')
            ->whereNull('membership_assigned_at')
            ->update(['membership_assigned_at' => now()]);

        // Validar y actualizar membresías expiradas
        Locality::all()->each(function ($locality) {
            $locality->validateAndUpdateMembership();
        });
    }

    private function generateTokenData(int $localityId, bool $isExpired = false): string
    {
        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addYear()->format('Y-m-d');

        if ($isExpired) {
            $startDate = Carbon::now()->subYear()->format('Y-m-d');
            $endDate = Carbon::now()->subDay()->format('Y-m-d');
        }

        return Token::generateTokenForLocality($localityId, $startDate, $endDate);
    }
}
