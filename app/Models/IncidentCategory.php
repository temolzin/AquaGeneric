<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
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
        return $this->hasMany(Incident::class, 'category_id');
    }

    public function hasDependencies()
    {
        return $this->incidents()->exists();
    }
}
