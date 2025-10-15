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
        'zip_code'
    ];

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

        $decrypted = Crypt::decrypt($this->token);
        $endDate = Carbon::parse($decrypted['data']['endDate'])->startOfDay();
        $today = now()->startOfDay();

        return $today->lte($endDate) ? self::SUBSCRIPTION_ACTIVE : self::SUBSCRIPTION_EXPIRED;
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
