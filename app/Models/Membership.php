<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'name', 
        'price',
        'term_months',
        'water_connections_number',
        'users_number'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
