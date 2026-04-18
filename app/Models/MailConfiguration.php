<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MailConfiguration extends Model
{
    use HasFactory;

    public const EXAMPLE_MAILER = 'smtp';
    public const EXAMPLE_HOST = 'smtp.gmail.com';
    public const EXAMPLE_PORT = '587';
    public const EXAMPLE_USERNAME = 'usuario@tudominio.com';
    public const EXAMPLE_PASSWORD = 'tu contraseña segura';
    public const EXAMPLE_ENCRYPTION = 'tls';
    public const EXAMPLE_FROM_NAME = 'Tu Empresa o Servicio';

    protected $fillable = [
        'locality_id',
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_name'
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function isComplete()
    {
        return
            !empty($this->mailer) &&
            !empty($this->host) &&
            !empty($this->port) &&
            !empty($this->username) &&
            !empty($this->password) &&
            !empty($this->encryption);
    }

    public function setPasswordAttribute($value)
    {
        if (blank($value)) {
            return;
        }

        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getPasswordAttribute($value)
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            return $value;
        }
    }
}
