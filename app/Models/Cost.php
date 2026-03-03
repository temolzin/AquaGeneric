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

    protected static function booted()
    {
        parent::booted();

        static::addGlobalScope('byUserLocality', function ($query) {
            $user = auth()->user();
            if ($user && $user->locality_id) {
                $query->where('costs.locality_id', $user->locality_id)
                      ->orWhereNull('costs.locality_id');
            }
        });
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByUserLocality($query)
    {
        $user = auth()->user();
        if ($user && $user->locality_id) {
            return $query->where('locality_id', $user->locality_id)
                         ->orWhereNull('locality_id');
        }
        return $query;
    }
}
