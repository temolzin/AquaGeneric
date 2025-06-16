<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\Debt;

class WaterConnection extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PAID = 'Pagado';
    public const STATUS_DEBT = 'Adeudo';
    public const STATUS_ADVANCED = 'Adelantado';
    public const STATUS_CANCELLED = 'Cancelado';

    protected $fillable = [
        'locality_id',
        'created_by',
        'customer_id',
        'cost_id',
        'name',
        'block',
        'street',
        'exterior_number',
        'interior_number',
        'occupants_number',
        'water_days',
        'has_water_pressure',
        'has_cistern',
        'type',
        'note',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function cost()
    {
        return $this->belongsTo(Cost::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function debts()
    {
        return $this->hasMany(Debt::class, 'water_connection_id');
    }

    public function getStatusCalculatedAttribute()
    {
        $today = Carbon::today();
        $debts = $this->debts;

        $unpaidDebts = $debts->where('status', '!=', Debt::STATUS_PAID);
        $hasDebt = $unpaidDebts->isNotEmpty();

        $futurePaidDebts = $debts->filter(function ($debt) use ($today) {
            return $debt->status === Debt::STATUS_PAID &&
                Carbon::parse($debt->start_date)->gt($today);
        });

        $hasAdvance = $futurePaidDebts->isNotEmpty();

        $statusChecks = [
            self::STATUS_CANCELLED => $this->status === 'cancelled',
            self::STATUS_DEBT => $hasDebt,
            self::STATUS_ADVANCED => $hasAdvance,
        ];

        foreach ($statusChecks as $status => $condition) {
            if ($condition) {
                return $status;
            }
        }

        return self::STATUS_PAID;
    }

    public function getCalculatedStyleAttribute()
    {
        $styles = [
            self::STATUS_PAID => 'background-color: #28a745; color: white;',
            self::STATUS_DEBT => 'background-color: #dc3545; color: white;',
            self::STATUS_ADVANCED => 'background-color: #6f42c1; color: white;',
            self::STATUS_CANCELLED => 'background-color: #6c757d; color: white;',
        ];

        return $styles[$this->statusCalculated] ?? '';
    }
}
