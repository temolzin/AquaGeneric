<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'color',
        'created_by',
        'locality_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function generalExpenses()
    {
        return $this->hasMany(GeneralExpense::class);
    }

    public function scopeByUserLocality($query)
    {
        $user = auth()->user();
        if ($user && $user->locality_id) {
            return $query->where('locality_id', $user->locality_id);
        }
        return $query;
    }
}
