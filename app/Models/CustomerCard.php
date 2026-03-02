<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'alias',
        'openpay_card_id',
        'brand',
        'last_four',
        'holder_name',
        'expiration_month',
        'expiration_year',
        'is_default',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDisplayNameAttribute()
    {
        $name = $this->alias ?: ucfirst($this->brand);
        return "{$name} •••• {$this->last_four}";
    }

    public function getBrandIconAttribute()
    {
        $icons = [
            'visa' => 'fab fa-cc-visa text-primary',
            'mastercard' => 'fab fa-cc-mastercard text-warning',
            'master_card' => 'fab fa-cc-mastercard text-warning',
            'american_express' => 'fab fa-cc-amex text-info',
            'amex' => 'fab fa-cc-amex text-info',
        ];

        return $icons[strtolower($this->brand)] ?? 'fas fa-credit-card text-secondary';
    }

    public function getIsExpiredAttribute()
    {
        $currentYear = (int) date('y');
        $currentMonth = (int) date('m');
        $cardYear = (int) $this->expiration_year;
        $cardMonth = (int) $this->expiration_month;

        if ($cardYear < $currentYear) {
            return true;
        }

        if ($cardYear === $currentYear && $cardMonth < $currentMonth) {
            return true;
        }

        return false;
    }

    public function setAsDefault()
    {
        self::where('customer_id', $this->customer_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function scopeValid($query)
    {
        $currentYear = (int) date('y');
        $currentMonth = (int) date('m');

        return $query->where(function ($q) use ($currentYear, $currentMonth) {
            $q->where('expiration_year', '>', $currentYear)
                ->orWhere(function ($q2) use ($currentYear, $currentMonth) {
                    $q2->where('expiration_year', '=', $currentYear)
                        ->where('expiration_month', '>=', $currentMonth);
                });
        });
    }
}
