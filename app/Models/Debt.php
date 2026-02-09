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

    /**
     * Obtener el monto total pagado para esta deuda
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount') ?? 0;
    }

    /**
     * Obtener el monto pendiente real (calculado dinámicamente)
     * Este es el monto correcto que falta por pagar
     */
    public function getRemainingAmountAttribute()
    {
        $totalPaid = $this->total_paid;
        $remaining = $this->amount - $totalPaid;
        return max(0, $remaining); // Nunca retornar negativo
    }

    /**
     * Verificar si la deuda está completamente pagada
     */
    public function isPaid()
    {
        return $this->remaining_amount <= 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($debt) {
            $debt->payments()->delete();
        });
    }
}
