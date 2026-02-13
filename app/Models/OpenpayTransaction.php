<?php

namespace App\Models;
use App\Models\Payment;
use App\Models\Locality;

use Illuminate\Database\Eloquent\Model;

class OpenpayTransaction extends Model
{
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
        
    }
}
