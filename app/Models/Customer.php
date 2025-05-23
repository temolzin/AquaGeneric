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
        'locality',
        'state',
        'zip_code',
        'block',
        'street',
        'exterior_number',
        'interior_number',
        'marital_status',
        'status',
        'responsible_name',
        'locality_id',
        'created_by',
        'note',
    ];


    public function Cost()
    {
        return $this->belongsTo(Cost::class);
    }

    public function hasDependencies()
    {
        return $this->waterConnections()->whereHas('debts')->exists();
    }

    public function waterConnections()
    {
        return $this->hasMany(WaterConnection::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($customer) {
            $customer->waterConnections()->each(function ($waterConnection) {
                $waterConnection->debts()->delete();
            });
            $customer->waterConnections()->delete();
        });
    }
}
