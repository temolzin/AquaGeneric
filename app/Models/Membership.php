<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'name',
        'price',
        'term_months'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
