<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Exception;

class Locality extends Model implements HasMedia
{
    const SUBSCRIPTION_ACTIVE = 'Activa';
    const SUBSCRIPTION_EXPIRED = 'Caducada';
    const SUBSCRIPTION_NONE = 'Sin token';

    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'municipality',
        'state',
        'zip_code',
        'membership_id',
        'membership_assigned_at',
        'token'
    ];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function waterConnections()
    {
        return $this->hasMany(WaterConnection::class);
    }

    public function hasDependencies()
    {
        return $this->customers()->exists() || $this->users()->exists() || $this->costs()->exists();
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function costs()
    {
        return $this->hasMany(Cost::class);
    }

    public function mailConfiguration()
    {
        return $this->hasOne(MailConfiguration::class);
    }

    public function getSubscriptionStatus()
    {
        if (!$this->token) {
            return self::SUBSCRIPTION_NONE;
        }

        try {
            $tokenValidation = Crypt::decrypt($this->token);
            $endDate = Carbon::parse($tokenValidation['data']['endDate'])->startOfDay();
            $today = now()->startOfDay();
            
            return $today->lte($endDate) ? self::SUBSCRIPTION_ACTIVE : self::SUBSCRIPTION_EXPIRED;
        } catch (Exception $e) {
            return self::SUBSCRIPTION_NONE;
        }
    }

    public function validateAndUpdateMembership()
    {
        $status = $this->getSubscriptionStatus();
        
        if ($status === self::SUBSCRIPTION_EXPIRED) {
            $this->membership_id = null;
            $this->membership_assigned_at = null;
            $this->save();
        }
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('pdfBackgroundVertical')
            ->useDisk('public')
            ->singleFile();

        $this
            ->addMediaCollection('pdfBackgroundHorizontal')
            ->useDisk('public')
            ->singleFile();
    }
}
