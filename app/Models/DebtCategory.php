<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'debt_categories';

    protected $fillable = [
        'name',
        'description',
        'color',
        'created_by',
        'locality_id',
    ];

    public const SERVICE_NAME = 'Servicio de Agua';

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getDefaultService(int $localityId, ?int $userId = null): self
    {
        return self::firstOrCreate(
            [
                'name' => self::SERVICE_NAME,
                'locality_id' => $localityId,
            ],
            [
                'description' => 'Categoría por defecto para servicio de agua',
                'color' => 'bg-primary',
                'created_by' => $userId,
            ]
        );
    }

    public static function resolveCategory(?int $categoryId, int $localityId, ?int $userId = null): self
    {
        return self::find($categoryId)
            ?? self::getDefaultService($localityId, $userId);
    }

    public function isService(): bool
    {
        return strcasecmp(trim($this->name), self::SERVICE_NAME) === 0;
    }
}
