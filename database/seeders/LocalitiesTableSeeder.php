<?php

namespace Database\Seeders;

use App\Models\Locality;
use Illuminate\Database\Seeder;

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
            ],
        ];

        foreach ($localitiesData as $data) {
            $locality = Locality::create($data);
            $locality->token = Locality::generateTokenForLocality($locality->id);
            $locality->save();
        }

        Locality::where(function ($query) {
            $query->whereNull('token')->orWhere('token', '');
        })->get()->each(function ($locality) {
            $locality->token = Locality::generateTokenForLocality($locality->id);
            $locality->save();
        });
    }
}
