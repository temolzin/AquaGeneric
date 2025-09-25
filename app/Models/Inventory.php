<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory';

    protected $fillable = [
        'locality_id',
        'created_by',
        'name',
        'description',
        'amount',
        'category',
        'material',
        'dimensions',
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = max(0, $value);
    }
}
