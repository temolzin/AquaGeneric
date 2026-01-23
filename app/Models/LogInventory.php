<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogInventory extends Model
{
    use HasFactory;

    protected $table = 'log_inventory';
    
    protected $fillable = [
        'locality_id',
        'created_by',
        'inventory_id',
        'previous_amount',
        'amount',
        'description'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }
}
