<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Connection extends Model
{
    use HasFactory,  SoftDeletes;

    protected $fillable = [
        'customer_id',
        'cost_id',
        'name',
        'occupants_number',
        'water_days',
        'has_water_pressure',
        'has_cistern',
        'type',
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
}
