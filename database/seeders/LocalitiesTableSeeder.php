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
        // Asegurar que existan memberships antes de asignarlos
        if (Membership::count() === 0) {
             $this->call(MembershipsTableSeeder::class);
        }

        // Map determinístico: localidad -> nombre del plan
        // (No depende de autoincrement IDs)
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

        // Fallback determinístico: primer membership por id (NO random)
        $defaultMembershipId = Membership::orderBy('id')->value('id');

        foreach ($localitiesData as $data) {
            $membershipId = Membership::where('name', $data['membership_name'])->value('id') ?: $defaultMembershipId;

            // 1) Crear/actualizar localidad SIN token primero (token requiere locality_id real)
            $locality = Locality::updateOrCreate(
                ['name' => $data['name']],
                [
                    'name' => $data['name'],
                    'municipality' => $data['municipality'],
                    'state' => $data['state'],
                    'zip_code' => $data['zip_code'],
                    'membership_id' => $membershipId,
                ]
            );

            // 2) Token determinístico usando el ID real de la localidad
            $locality->token = $this->generateTokenData($locality->id, (bool) $data['token_expired']);
            $locality->save();
        }

        // Completar datos faltantes de forma determinística (no random)
        Locality::whereNull('membership_id')->get()->each(function ($locality) use ($defaultMembershipId) {
            $locality->membership_id = $defaultMembershipId;
            $locality->save();
        });

        Locality::whereNull('token')->orWhere('token', '')->get()->each(function ($locality) {
            $locality->token = $this->generateTokenData($locality->id, false);
            $locality->save();
        });

        // Mantener tu lógica de asignación de fecha si aplica en tu schema
        Locality::whereNotNull('membership_id')
            ->whereNull('membership_assigned_at')
            ->update(['membership_assigned_at' => now()]);
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
