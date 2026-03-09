<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incident;
use App\Models\IncidentStatus;
use App\Models\IncidentCategory;
use App\Models\Locality;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncidentSeeder extends Seeder
{
    public function run()
    {
        $this->call(IncidentStatusSeeder::class);
        $this->call(IncidentCategorySeeder::class);

        foreach (Locality::all() as $locality) {
            $createdBy = $this->getUserForLocality($locality->id);
            
            $pendingId = IncidentStatus::where('status', 'Pendiente')
                ->where('locality_id', $locality->id)
                ->value('id');
            $inProgressId = IncidentStatus::where('status', 'En progreso')
                ->where('locality_id', $locality->id)
                ->value('id');

            $plumbingId = IncidentCategory::where('name', 'Plomería')
                ->where('locality_id', $locality->id)
                ->value('id');
            $electricalId = IncidentCategory::where('name', 'Eléctrica')
                ->where('locality_id', $locality->id)
                ->value('id');

            $incidents = [
                [
                    'name' => 'Falla en iluminación',
                    'description' => 'No funcionan las luces del pasillo principal.',
                    'status_id' => $pendingId,
                    'start_date' => Carbon::now()->subDays(3)->toDateString(),
                    'category_id' => $electricalId,
                    'locality_id' => $locality->id,
                    'created_by' => $createdBy,
                ],
                [
                    'name' => 'Fuga en baño',
                    'description' => 'Se reporta fuga de agua en el baño de hombres.',
                    'status_id' => $inProgressId,
                    'start_date' => Carbon::now()->subDays(2)->toDateString(),
                    'category_id' => $plumbingId,
                    'locality_id' => $locality->id,
                    'created_by' => $createdBy,
                ],
            ];

            foreach ($incidents as $incident) {
                Incident::updateOrCreate(
                    [
                        'name' => $incident['name'],
                        'locality_id' => $incident['locality_id'],
                    ],
                    [
                        'description' => $incident['description'],
                        'status_id' => $incident['status_id'],
                        'start_date' => $incident['start_date'],
                        'category_id' => $incident['category_id'],
                        'created_by' => $incident['created_by'],
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    private function getUserForLocality(int $localityId): ?int
    {
        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
            ->where('users.locality_id', $localityId)
            ->distinct()
            ->value('users.id');
    }
}
