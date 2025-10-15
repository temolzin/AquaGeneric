<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'locality_id',
        'created_by',
        'name',
        'zip_code',
        'color',
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function waterConnections()
    {
        return $this->hasMany(\App\Models\WaterConnection::class, 'section_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
