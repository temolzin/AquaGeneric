<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cost extends Model
{
    use HasFactory,  SoftDeletes;

    protected $fillable = [
        'locality_id',
        'created_by',
        'category',
        'price',
        'description',
    ];

    public $timestamps = false;

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
