<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashClosure extends Model
{
    use HasFactory;

     protected $fillable = [
        'initial_amount',
        'final_amount',
        'opened_at',
        'closed_at',
        'total_sales',
        'total_expenses',
        'created_by',
        'user_id',
        'locality_id',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];


    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class, 'locality_id');
    }
}
