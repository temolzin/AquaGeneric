<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'term_months',
        'water_connections_number',
        'users_number'
    ];
}
