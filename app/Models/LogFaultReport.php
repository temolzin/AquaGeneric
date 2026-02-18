<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LogFaultReport extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'locality_id',
        'created_by',
        'status',
        'comentario',
        'fault_report_id'
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function faultReport()
    {
        return $this->belongsTo(FaultReport::class, 'fault_report_id');
    }
}
