<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'locality_id',
        'created_by',
        'concept',
        'description',
        'amount',
        'type',
        'expense_date'
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
