<?php

namespace Umii\LoginAlert\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAlert extends Model
{
    protected $table = 'login_alerts';

    protected $fillable = [
        'user_id',
        'device_hash',
        'ip_address',
        'user_agent',
    ];
}
