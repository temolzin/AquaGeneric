<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogWaterConnectionTransfer extends Model
{
    use HasFactory;

    protected $table = 'log_water_connection_transfer';

    protected $fillable = [
        'water_connection_id',
        'old_customer_id',
        'new_customer_id',
        'reason',
        'effective_date',
        'note',
        'created_by',
    ];

    public function waterConnection()
    {
        return $this->belongsTo(WaterConnection::class);
    }

    public function oldCustomer()
    {
        return $this->belongsTo(Customer::class, 'old_customer_id');
    }

    public function newCustomer()
    {
        return $this->belongsTo(Customer::class, 'new_customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
