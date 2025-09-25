<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LocalityNotice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeByLocality($query, $localityId)
    {
        return $query->where('locality_id', $localityId);
    }

    public function getIsCurrentlyActiveAttribute()
    {
        return $this->is_active && 
               $this->start_date <= now() && 
               $this->end_date >= now();
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
