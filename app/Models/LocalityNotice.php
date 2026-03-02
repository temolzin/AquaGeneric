<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LocalityNotice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_EXPIRED = 'expired';

    protected $table = 'locality_notices';

    protected $fillable = [
        'created_by',
        'locality_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'attachment_url'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('notice_attachments')
            ->singleFile();
    }

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    public function getStatusAttribute()
    {
        $now = now();
        
        if ($this->end_date < $now) {
            return self::STATUS_EXPIRED;
        }
        
        if ($this->start_date > $now) {
            return self::STATUS_SCHEDULED;
        }
        
        return self::STATUS_ACTIVE;
    }

    public function isActive(): bool
    {
        return $this->getStatusAttribute() === self::STATUS_ACTIVE && $this->is_active;
    }

    public function getLocalityNameAttribute()
    {
        return $this->locality ? $this->locality->name : 'N/A';
    }

    public function getCreatorNameAttribute()
    {
        return $this->creator ? $this->creator->name : 'Usuario no encontrado';
    }
}
