<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Cost;

class Customer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'last_name',
        'block',
        'street',
        'interior_number',
        'marital_status',
        'partner_name',
        'has_water_connection',
        'has_store',
        'has_all_payments',
        'has_water_day_night',
        'occupants_number',
        'water_days',
        'has_water_pressure',
        'has_cistern',
        'cost_id',
        'status',
        'responsible_name',
    ];

    public $timestamps = false;

    public function Cost()
    {
        return $this->belongsTo(Cost::class);
    }
   
    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->debts->each(function ($debt) {
                $debt->payments()->delete();
            });
            $user->debts()->delete();
        });
    }

}
