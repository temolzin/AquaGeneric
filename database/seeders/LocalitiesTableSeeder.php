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
            'locality_name' => 'Sierra Hermosa',
            'municipality' => 'Tecámac',
            'state' => 'Estado de México',
            'zip_code' => '55749',
            
        ]);

        
        Locality::create([
            'locality_name' => 'Ojo de Agua',
            'municipality' => 'Tecámac',
            'state' => 'Estado de México',
            'zip_code' => '55770',
            
        ]);

        Locality::create([
            'locality_name' => 'Viento Nuevo',
            'municipality' => 'Ecatepec',
            'state' => 'Estado de México',
            'zip_code' => '55074',
            
        ]);
    }
}
