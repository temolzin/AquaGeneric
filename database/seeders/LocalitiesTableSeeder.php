<?php

namespace Database\Seeders;

use App\Models\Token;
use Illuminate\Database\Seeder;
use App\Models\Locality;
use Carbon\Carbon;

class LocalitiesTableSeeder extends Seeder
{
    public function run()
    {
        $memberships = \App\Models\Membership::all();
        
        if ($memberships->isEmpty()) {
            $this->call(MembershipsTableSeeder::class);
            $memberships = \App\Models\Membership::all();
        }

        $localitiesData = [
            [
                'name' => 'Smallville',
                'municipality' => 'Smallville',
                'state' => 'Kansas',
                'zip_code' => '66002',
                'membership_id' => $memberships->where('name', 'Premium Plan - 6 Months')->first()->id ?? $memberships->first()->id,
            ],
            [
                'name' => 'Springfield',
                'municipality' => 'Springfield',
                'state' => 'Oregon',
                'zip_code' => '97477',
                'membership_id' => $memberships->where('name', 'Enterprise Plan - 12 Months')->first()->id ?? $memberships->first()->id,
            ],
            [
                'name' => 'Dunder Mifflin',
                'municipality' => 'Scranton',
                'state' => 'Pennsylvania',
                'zip_code' => '18503',
                'membership_id' => $memberships->where('name', 'Basic Plan - 3 Months')->first()->id ?? $memberships->first()->id,
                'expired' => true
            ],
        ];

        foreach ($localitiesData as $data) {
            $isExpired = $data['expired'] ?? false;
            unset($data['expired']);

            $locality = Locality::updateOrCreate(
                ['name' => $data['name']], 
                $data
            );

            $startDate = Carbon::now()->format('Y-m-d');
            $endDate = Carbon::now()->addYear()->format('Y-m-d');

            if ($isExpired) {
                $startDate = Carbon::now()->subYear()->format('Y-m-d');
                $endDate = Carbon::now()->subDay()->format('Y-m-d');
            }    
            
            $locality->token = Token::generateTokenForLocality($locality->id, $startDate, $endDate);
            $locality->save();
        }

        Locality::whereNull('membership_id')->get()->each(function ($locality) use ($memberships) {
            $locality->update([
                'membership_id' => $memberships->random()->id
            ]);
        });
    }
}
