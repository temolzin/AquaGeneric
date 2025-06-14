<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Debt;

class WaterConnection extends Model
{
    use HasFactory,  SoftDeletes;

    protected $fillable = [
        'locality_id',
        'created_by',
        'customer_id',
        'cost_id',
        'name',
        'block',
        'street',
        'exterior_number',
        'interior_number',
        'occupants_number',
        'water_days',
        'has_water_pressure',
        'has_cistern',
        'type',
        'note',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function cost()
    {
        return $this->belongsTo(Cost::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function debts()
    {
        return $this->hasMany(Debt::class, 'water_connection_id');
    }

    public function hasDebt()
    {
        return $this->debts()->where('status', '!=', Debt::STATUS_PAID)->exists();
    }

   public function getCancelDescriptionAttribute()
    {
        return $this->attributes['cancel_description'];
    }
    
    public function setCancelDescriptionAttribute($value)
    {
        $this->attributes['cancel_description'] = $value;
    }
}
