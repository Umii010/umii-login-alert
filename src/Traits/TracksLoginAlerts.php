<?php

namespace Umii\LoginAlert\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Umii\LoginAlert\Models\LoginAlert as LoginAlertModel;

trait TracksLoginAlerts
{
    public function loginAlerts(): HasMany
    {
        return $this->hasMany(LoginAlertModel::class, 'user_id');
    }

    public function isNewLoginDevice(string $deviceHash): bool
    {
        return ! $this->loginAlerts()->where('device_hash', $deviceHash)->exists();
    }

    public function rememberLoginDevice(string $deviceHash, string $ip, string $userAgent): void
    {
        $this->loginAlerts()->create([
            'device_hash' => $deviceHash,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }
}
