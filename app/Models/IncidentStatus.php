<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'description',
        'created_by',
        'locality_id',
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class, 'status_id', 'id');
    }

    public function hasDependencies()
    {
        return $this->incidents()->exists();
    }

}
