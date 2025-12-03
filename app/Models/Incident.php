<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Incident extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    protected $fillable = [
        'locality_id',
        'created_by',
        'name',
        'start_date',
        'description',
        'category_id',
        'status_id',
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function incidentCategory()
    {
        return $this->belongsTo(IncidentCategory::class, 'category_id');
    }

    public function getstatusChangeLogs()
    {
        return $this->hasMany(LogIncident::class, 'incident_id')->orderBy('created_at', 'desc');
    }

    public function getLatestDescription()
    {
        $incidentDescription = $this->description;
        $incidentUpdatedAt = $this->updated_at;
        $lastLog = $this->getstatusChangeLogs->first();

        if ($lastLog && !empty($lastLog->description)) {
            if ($lastLog->updated_at > $incidentUpdatedAt) {
                return $lastLog->description;
            }
        }

        return $incidentDescription;
    }

    public function getLatestStatus()
    {
        $incidentStatus = $this->status ? $this->status->status : null;
        $incidentUpdatedAt = $this->updated_at;
        $lastLog = $this->getstatusChangeLogs()->first();

        if ($lastLog && !empty($lastLog->status)) {
            $logUpdatedAt = $lastLog->updated_at;

            if ($logUpdatedAt > $incidentUpdatedAt) {
                return $lastLog->status;
            }
        }

        return $incidentStatus;
    }

    public function getResponsibleEmployeesAttribute()
    {
        $uniqueEmployees = collect();
        $employeeIdsAlreadySeen = [];

        foreach ($this->getstatusChangeLogs as $log) {
            if ($log->employee && !in_array($log->employee->id, $employeeIdsAlreadySeen)) {
                $employeeIdsAlreadySeen[] = $log->employee->id;
                $uniqueEmployees->push($log->employee);
            }
        }

        return $uniqueEmployees;
    }

    public function status()
    {
        return $this->belongsTo(IncidentStatus::class, 'status_id');
    }
}
