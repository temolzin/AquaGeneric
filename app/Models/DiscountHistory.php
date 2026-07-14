<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'locality_id',
        'discount_id',
        'customer_id',
        'module',
        'record_id',
        'original_amount',
        'discount_amount',
        'discount_percentage',
        'final_amount',
        'created_by',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function scopeByUserLocality($query)
    {
        $user = auth()->user();
        if ($user && $user->locality_id) {
            return $query->where(function ($q) use ($user) {
                $q->where('locality_id', $user->locality_id)
                  ->orWhereNull('locality_id');
            });
        }
        return $query;
    }
}
