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
        'debt_id',
        'amount',
        'payment_date',
        'note',
    ];

    public $timestamps = false;

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}
