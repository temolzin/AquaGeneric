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
        'status',
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

    public function latestLog()
    {
        return $this->hasOne(LogIncident::class, 'incident_id')->latestOfMany();
    }

    public function logs()
    {
        return $this->hasMany(LogIncident::class, 'incident_id')->latest();
    }

    public function latestDescription()
    {
        $incidentDescription = $this->description;
        $incidentUpdatedAt = $this->updated_at;

        $lastLog = $this->latestLog;

        if ($lastLog && !empty($lastLog->description)) {
            $logUpdatedAt = $lastLog->updated_at;

            if ($logUpdatedAt > $incidentUpdatedAt) {
                return $lastLog->description;
            }
        }

        return $incidentDescription;
    }

    public function latestStatus()
    {
        $incidentStatus = $this->status;
        $incidentUpdatedAt = $this->updated_at;

        $lastLog = $this->latestLog;

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
        $unique = collect();
        $seen = [];

        foreach ($this->logs as $log) {
            if ($log->employee && !in_array($log->employee->id, $seen)) {
                $seen[] = $log->employee->id;
                $unique->push($log->employee);
            }
        }

        return $unique;
    }
}
