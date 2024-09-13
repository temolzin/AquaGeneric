<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'customer_id', 'start_date', 'end_date', 'amount', 'note'
    ];
    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'debt_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($debt) {
            $debt->payments()->delete();
        });
    }
    
}
