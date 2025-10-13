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
}
