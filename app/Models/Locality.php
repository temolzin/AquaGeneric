<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Locality extends Model implements HasMedia
{
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
}
