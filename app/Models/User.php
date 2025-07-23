<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    /**
     * @method bool hasRole(string|array $roles)
     */
    
    protected $fillable=['locality_id','name','last_name','phone','email','password'];
    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hasDependencies()
    {
        return $this->locality()->exists() || $this->customers()->exists() || $this->costs()->exists() || $this->debts()->exists() || $this->payments()->exists();
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class, 'locality_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'created_at');
    }

    public function costs()
    {
        return $this->hasMany(Cost::class, 'created_at');
    }

    public function debts()
    {
        return $this->hasMany(Debt::class, 'created_at');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'created_at');
    }

    public function tokenIsValid()
    {
        if (!$this->locality || !$this->locality->token) {
            return false;
        }

        try {
            $decrypted = Crypt::decrypt($this->locality->token);
            $data = $decrypted['data'] ?? null;

            if ($data && isset($data['endDate'])) {
                $expiration = Carbon::parse($data['endDate'])->startOfDay();
                $today = now()->startOfDay();
                $daysRemaining = $today->diffInDays($expiration, false);
                return $daysRemaining >= 0;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
