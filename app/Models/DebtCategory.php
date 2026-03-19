<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtCategory extends Model
{
    protected $table = 'debt_categories';

    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    public static function getDefaultService()
    {
        return self::firstOrCreate(
            ['name' => 'Servicio de Agua'],
            [
                'description' => 'Categoría por defecto para servicio de agua',
                'color' => 'bg-primary'
            ]
        );
    }
}
