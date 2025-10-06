<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Cost;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Customer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
        'user_id',
    ];


    public function Cost()
    {
        return $this->belongsTo(Cost::class);
    }

    public function hasDependencies()
    {
        return $this->waterConnections()->exists() || $this->waterConnections()->whereHas('debts')->exists();
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
    
    public function waterConnectionsAll()
    {
        return $this->hasMany(WaterConnection::class)->withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNameAttribute()
    {
        return $this->user ? $this->user->name : null;
    }

    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : null;
    }

    public function getLastNameAttribute()
    {
        return $this->user ? $this->user->last_name : null;
    }
}
