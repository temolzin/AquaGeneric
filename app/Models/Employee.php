<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Employee extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;
    protected $fillable = [
        'name',
        'last_name',
        'locality',
        'zip_code',
        'state',
        'block',
        'street',
        'exterior_number',
        'interior_number',
        'email',
        'phone_number',
        'salary',
        'created_by',
        'locality_id',
        'rol',
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
