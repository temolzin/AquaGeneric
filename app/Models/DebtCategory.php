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

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtener o crear la categoría de "Servicio de Agua"
     * SIEMPRE ligada a una localidad válida.
     */
    public static function getDefaultService()
    {
        $user = auth()->user();

        // 🔴 Protección crítica: evitar categorías globales accidentales
        if (!$user || !$user->locality_id) {
            throw new \Exception('No se puede determinar la localidad para la categoría de servicio.');
        }

        $attributes = [
            'name' => 'Servicio de Agua',
            'locality_id' => $user->locality_id,
        ];

        $values = [
            'description' => 'Categoría por defecto para servicio de agua',
            'color' => 'bg-primary',
            'created_by' => $user->id,
        ];

        return self::firstOrCreate($attributes, $values);
    }

    /**
     * Helper opcional para identificar si es categoría de servicio
     */
    public function isService(): bool
    {
        return strtolower(trim($this->name)) === 'servicio de agua';
    }
}
