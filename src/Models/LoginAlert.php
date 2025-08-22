<?php

namespace Umii\LoginAlert\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAlert extends Model
{
    protected $fillable = [
        'user_id', 'ip', 'device', 'user_agent', 'location', 'fingerprint'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
