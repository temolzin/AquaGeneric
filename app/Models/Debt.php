<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory , SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';
    public const DASHBOARD_EXPIRING_DAYS = 20;

    protected $fillable = [
        'water_connection_id', 'locality_id', 'created_by', 'start_date', 'end_date', 'amount', 'note'
    ];

    public function waterConnection()
    {
        return $this->belongsTo(WaterConnection::class);
    }

    public function customer()
    {
        return $this->hasOneThrough(Customer::class, WaterConnection::class, 'id', 'id', 'water_connection_id', 'customer_id');
    }

    public function hasDependencies()
    {
        return $this->payments()->exists();
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
