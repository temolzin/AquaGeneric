<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\DiscountHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'locality_id',
        'created_by',
        'name',
        'percentage',
        'color',
        'description',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function hasDependencies()
    {
        return $this->payments()->exists();
        return $this->payments()->exists() || DiscountHistory::where('discount_id', $this->id) ->where('module', 'debt') ->exists();
    }
}
