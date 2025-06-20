<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
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
}
