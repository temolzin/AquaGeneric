<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'alter_by',
        'module',
        'action',
        'record_id',
        'created_by',
        'before_data',
        'current_data',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'before_data' => 'array',
        'current_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'alter_by');
    }
}
