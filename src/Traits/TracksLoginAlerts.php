<?php

namespace Umii\LoginAlert\Traits;

use Umii\LoginAlert\Models\LoginAlert;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait TracksLoginAlerts
{
    public function loginAlerts(): HasMany
    {
        return $this->hasMany(LoginAlert::class, 'user_id');
    }
}
