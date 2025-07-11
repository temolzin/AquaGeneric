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

    public const VIEW_STATUS_PAID = 'Pagado';
    public const VIEW_STATUS_DEBT = 'Adeudo';
    public const VIEW_STATUS_ADVANCED = 'Adelantado';
    public const VIEW_STATUS_CANCELED = 'Cancelado';
    public const SCOPE_NOT_CANCELED = 'notCanceled';

    protected static function booted()
    {
        static::addGlobalScope(self::SCOPE_NOT_CANCELED, function ($query) {
            $query->where('is_canceled', false);
        });
    }

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
            self::VIEW_STATUS_CANCELED => $this->is_canceled,
            self::VIEW_STATUS_DEBT => $hasDebt,
            self::VIEW_STATUS_ADVANCED => $hasAdvance,
        ];

        foreach ($statusChecks as $status => $condition) {
            if ($condition) {
                return $status;
            }
        }

        return self::VIEW_STATUS_PAID;
    }

    public function getCalculatedStyleAttribute()
    {
        return [
            self::VIEW_STATUS_PAID => 'background-color: #28a745; color: white;',
            self::VIEW_STATUS_DEBT => 'background-color: #dc3545; color: white;',
            self::VIEW_STATUS_ADVANCED => 'background-color: #6f42c1; color: white;',
            self::VIEW_STATUS_CANCELED => 'background-color: #6c757d; color: white;',
        ][$this->getStatusCalculatedAttribute()] ?? '';
    }
    
    public function hasDebt()
    {
        return $this->debts()->where('status', '!=', Debt::STATUS_PAID)->exists();
    }

    public function getCancelDescriptionAttribute()
    {
        return $this->attributes['cancel_description'];
    }
    
    public function setCancelDescriptionAttribute($value)
    {
        $this->attributes['cancel_description'] = $value;
    }
}
