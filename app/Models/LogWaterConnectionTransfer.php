<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LogWaterConnectionTransfer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'log_water_connection_transfer';

    protected $fillable = [
        'water_connection_id',
        'old_customer_id',
        'new_customer_id',
        'reason',
        'effective_date',
        'note',
        'created_by',
    ];

    public const MEDIA_COLLECTION = 'transferVerificationDocuments';

    public const REQUIRED_DOCUMENT_TYPES = [
        'PROPERTY_OWNERSHIP',
        'DEATH_CERTIFICATE',
        'NEW_OWNER_ID',
        'NO_DEBT_CERTIFICATE',
        'RFC',
    ];

    public static function documentTypeLabels(): array
    {
        return [
            'PROPERTY_OWNERSHIP'  => 'Acreditación de propiedad (Escritura/RPP/Adjudicación)',
            'DEATH_CERTIFICATE'   => 'Acta de defunción del titular anterior',
            'NEW_OWNER_ID'        => 'Identificación oficial del nuevo titular (INE/Pasaporte)',
            'NO_DEBT_CERTIFICATE' => 'Constancia de no adeudo',
            'RFC'                 => 'Cédula de Identificación Fiscal / RFC',
        ];
    }

    public function waterConnection()
    {
        return $this->belongsTo(WaterConnection::class);
    }

    public function oldCustomer()
    {
        return $this->belongsTo(Customer::class, 'old_customer_id');
    }

    public function newCustomer()
    {
        return $this->belongsTo(Customer::class, 'new_customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
