<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locality;

class UpdateMembershipAssignedAtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Locality::whereNotNull('membership_id')
                ->whereNull('membership_assigned_at')
                ->update(['membership_assigned_at' => now()]);
    }
}
