<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenPayLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'transaction_id',
        'event_type',
        'status',
        'request_data',
        'response_data',
        'error_message',
        'ip_address',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
