<?php

namespace Database\Seeders;

use App\Models\Locality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class LocalitiesTableSeeder extends Seeder
{
    public function run()
    {
        $existingLocalities = Locality::whereNull('token')->get();

        foreach ($existingLocalities as $locality) {
            $token = $this->getgenerateLocalityToken($locality->id);
            $locality->token = $token;
            $locality->save();
        }

        $localities = [
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

        foreach ($localities as $data) {
            $locality = Locality::create($data);
            $token = $this->getgenerateLocalityToken($locality->id);
            $locality->token = $token;
            $locality->save();
        }
    }

    private function getgenerateLocalityToken(int $id): string
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYear()->format('Y-m-d');

        $data = [
            'idLocality' => $id,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        $hmacSignature = hash_hmac('sha256', json_encode($data), env('TOKEN_SECRET_KEY'));

        return Crypt::encrypt([
            'data' => $data,
            'hmac' => $hmacSignature,
        ]);
    }
}
