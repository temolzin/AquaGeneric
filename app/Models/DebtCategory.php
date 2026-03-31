<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtCategory extends Model
{
    use HasFactory, SoftDeletes;

    public const NAME_SERVICE = 'Servicio de Agua';

    protected $fillable = [
        'name',
        'description',
        'color',
        'locality_id',
        'created_by',
    ];

    public function isService(): bool
    {
        return $this->name === self::NAME_SERVICE;
    }

    public static function serviceId(): int
    {
        $cat = static::withTrashed()->firstOrCreate(
            ['name' => self::NAME_SERVICE],
            ['description' => 'Categoría global para Servicio de Agua', 'color' => '#007bff']
        );

        return $cat->id;
    }

    protected static function booted()
    {
        parent::booted();

        static::deleting(function ($category) {
            if ($category->name === self::NAME_SERVICE) {
                throw new \Exception('La categoría "' . self::NAME_SERVICE . '" no puede ser eliminada.');
            }
        });

        static::saving(function ($category) {
            if ($category->exists && $category->getOriginal('name') === self::NAME_SERVICE && $category->name !== self::NAME_SERVICE) {
                throw new \Exception('La categoría "' . self::NAME_SERVICE . '" no puede ser modificada.');
            }
        });
    }
}
