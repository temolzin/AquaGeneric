<?php

namespace Database\Seeders;

use App\Models\Locality;
use Illuminate\Database\Seeder;

class LocalitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Locality::create([
            'locality_name' => 'Smallville',
            'municipality' => 'Smallville',
            'state' => 'Kansas',
            'zip_code' => '66002',
        ]);
        
        Locality::create([
            'locality_name' => 'Springfield',
            'municipality' => 'Springfield',
            'state' => 'Oregon',
            'zip_code' => '97477',
        ]);

        Locality::create([
            'locality_name' => 'Dunder Mifflin',
            'municipality' => 'Scranton',
            'state' => 'Pennsylvania',
            'zip_code' => '18503',
        ]);
    }
}
