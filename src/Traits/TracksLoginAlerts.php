<?php

namespace Umii\LoginAlert\Traits;

use Illuminate\Support\Facades\DB;
use Umii\LoginAlert\Notifications\LoginAlertNotification;

trait TracksLoginAlerts
{
    public static function bootTracksLoginAlerts(): void
    {
        static::created(function ($user) {
            // nothing special on creation
        });
    }

    public function loginAlerts()
    {
        return $this->hasMany(\Umii\LoginAlert\Models\LoginAlert::class);
    }
}
