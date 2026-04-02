<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpenPayWebhookVerification extends Model
{
    protected $table = 'openpay_webhook_verifications';

    protected $fillable = [
        'verification_code',
        'openpay_event_id',
        'event_date',
        'ip_address',
        'user_agent',
        'copied',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'copied' => 'boolean',
    ];
}
