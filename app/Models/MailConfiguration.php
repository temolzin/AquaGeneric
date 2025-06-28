<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'locality_id',
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
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
            !empty($this->encryption) &&
            !empty($this->from_address);
    }
}
