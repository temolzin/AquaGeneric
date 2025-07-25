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
        $localitiesData = [
            [
                'name' => 'Smallville',
                'municipality' => 'Smallville',
                'state' => 'Kansas',
                'zip_code' => '66002',
            ],
            [
                'name' => 'Springfield',
                'municipality' => 'Springfield',
                'state' => 'Oregon',
                'zip_code' => '97477',
            ],
            [
                'name' => 'Dunder Mifflin',
                'municipality' => 'Scranton',
                'state' => 'Pennsylvania',
                'zip_code' => '18503',
                'expired' => true
            ],
        ];

        foreach ($localitiesData as $data) {
            $isExpired = $data['expired'] ?? false;
            unset($data['expired']);

            $locality = Locality::updateOrCreate(['name' => $data['name']], $data);

            if ($isExpired) {
                $startDate = Carbon::now()->subYear()->format('Y-m-d');
                $endDate = Carbon::now()->subDay()->format('Y-m-d');
            }

            if (!$isExpired) {
                $startDate = Carbon::now()->format('Y-m-d');
                $endDate = Carbon::now()->addYear()->format('Y-m-d');
            }     
            
            $locality->token = Token::generateTokenForLocality($locality->id, $startDate, $endDate);
            $locality->save();
        }

        Locality::where(function ($query) {
            $query->whereNull('token')->orWhere('token', '');
        })->get()->each(function ($locality) {
            $locality->token = Token::generateTokenForLocality($locality->id);
            $locality->save();
        });
    }
}
