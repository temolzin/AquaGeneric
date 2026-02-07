<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Debt;
use App\Models\Customer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'locality_id',
        'created_by',
        'debt_id',
        'method',
        'amount',
        'note',
        'is_future_payment',
        'openpay_transaction_id',
        'openpay_order_id',
        'openpay_authorization',
        'openpay_status',
        'openpay_error_message',
        'openpay_card_data',
        'openpay_processed_at'
    ];

    protected $casts = [
        'is_future_payment' => 'boolean',
        'openpay_card_data' => 'array',
        'openpay_processed_at' => 'datetime',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function openPayLogs()
    {
        return $this->hasMany(OpenPayLog::class);
    }

    public function isOpenPayPayment()
    {
        return $this->method === 'openpay';
    }
    
    public function isOpenPayCompleted()
    {
        return $this->isOpenPayPayment() && $this->openpay_status === 'completed';
    }

    public function isOpenPayPending()
    {
        return $this->isOpenPayPayment() && $this->openpay_status === 'in_progress';
    }

    public function isOpenPayFailed()
    {
        return $this->isOpenPayPayment() && in_array($this->openpay_status, ['failed', 'cancelled']);
    }
}
