<?php

namespace Database\Seeders;

use App\Models\Token;
use Illuminate\Database\Seeder;
use App\Models\Locality;
use App\Models\Membership;
use Carbon\Carbon;

class LocalitiesTableSeeder extends Seeder
{
    public function run()
    {
        $memberships = Membership::all();
        
        if ($memberships->isEmpty()) {
            $this->call(MembershipsTableSeeder::class);
            $memberships = Membership::all();
        }

        $localitiesData = [
            [
                'name' => 'Smallville',
                'municipality' => 'Smallville',
                'state' => 'Kansas',
                'zip_code' => '66002',
                'membership_id' => 2,
                'token' => $this->generateTokenData(false)
            ],
            [
                'name' => 'Springfield',
                'municipality' => 'Springfield',
                'state' => 'Oregon',
                'zip_code' => '97477',
                'membership_id' => 3,
                'token' => $this->generateTokenData(false)
            ],
            [
                'name' => 'Dunder Mifflin',
                'municipality' => 'Scranton',
                'state' => 'Pennsylvania',
                'zip_code' => '18503',
                'membership_id' => 1,
                'token' => $this->generateTokenData(true)
            ],
        ];

        foreach ($localitiesData as $data) {
            Locality::updateOrCreate(
                ['name' => $data['name']], 
                $data
            );
        }

        Locality::whereNull('membership_id')->orWhereNull('token')->get()->each(function ($locality) use ($memberships) {
            $updateData = [];
            
            if (is_null($locality->membership_id)) {
                $updateData['membership_id'] = $memberships->random()->id;
            }
            
            if (is_null($locality->token) || empty($locality->token)) {
                $updateData['token'] = $this->generateTokenData(false);
            }
            
            if (!empty($updateData)) {
                $locality->update($updateData);
            }
        });

        Locality::whereNotNull('membership_id')
                ->whereNull('membership_assigned_at')
                ->update(['membership_assigned_at' => now()]);
    }

    private function generateTokenData(bool $isExpired = false): string
    {
        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addYear()->format('Y-m-d');

        if ($isExpired) {
            $startDate = Carbon::now()->subYear()->format('Y-m-d');
            $endDate = Carbon::now()->subDay()->format('Y-m-d');
        }

        return Token::generateTokenForLocality($localityId ?? 1, $startDate, $endDate);
    }
}
