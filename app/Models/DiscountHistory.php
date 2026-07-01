<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\WaterConnection;

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
        'discount_percentage',
        'final_amount',
        'created_by',
    ];

    protected static function booted()
    {
        parent::booted();

        static::addGlobalScope('byUserLocality', function ($query) {
            $user = auth()->user();
            if ($user && $user->locality_id) {
                $query->where('discount_histories.locality_id', $user->locality_id)
                      ->orWhereNull('discount_histories.locality_id');
            }
        });
    }

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

    public function record()
    {
        return $this->morphTo(__FUNCTION__, 'module', 'record_id');
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
