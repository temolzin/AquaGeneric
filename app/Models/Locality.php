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
        'token',
        'openpay_merchant_id',
        'openpay_private_key',
        'openpay_public_key',
        'openpay_webhook_user',
        'openpay_webhook_password',
        'openpay_sandbox',
        'openpay_enabled'
    ];

    protected $casts = [
        'openpay_sandbox' => 'boolean',
        'openpay_enabled' => 'boolean',
        'last_reminder_sent_at' => 'datetime',
    ];

    public function setOpenpayPrivateKeyAttribute($value)
    {
        $this->attributes['openpay_private_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getOpenpayPrivateKeyAttribute($value)
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (Exception $e) {
            return $value;
        }
    }

    public function setOpenpayWebhookPasswordAttribute($value)
    {
        $this->attributes['openpay_webhook_password'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getOpenpayWebhookPasswordAttribute($value)
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (Exception $e) {
            return $value;
        }
    }
    public function hasOpenPayEnabled(): bool
    {
        return $this->openpay_enabled
            && !empty($this->openpay_merchant_id)
            && !empty($this->openpay_private_key)
            && !empty($this->openpay_public_key);
    }

    public function getOpenPayCredentials(): array
    {
        return [
            'merchant_id' => $this->openpay_merchant_id,
            'private_key' => $this->openpay_private_key,
            'public_key' => $this->openpay_public_key,
            'webhook_user' => $this->openpay_webhook_user,
            'webhook_password' => $this->openpay_webhook_password,
            'sandbox' => $this->openpay_sandbox,
        ];
    }

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

    public function generateMembershipToken()
    {
        if (!$this->membership || !$this->membership_assigned_at) {
            return null;
        }

        $startDate = Carbon::parse($this->membership_assigned_at)->startOfDay();
        $endDate = $startDate->copy()->addMonths($this->membership->term_months)->endOfDay();

        $data = [
            'idLocality' => $this->id,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
        ];

        $hmac = hash_hmac('sha256', json_encode($data), env('TOKEN_SECRET_KEY'));
        $tokenData = [
            'data' => $data,
            'hmac' => $hmac
        ];

        $token = Crypt::encrypt($tokenData);

        $this->token = $token;
        $this->saveQuietly();

        return $token;
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
