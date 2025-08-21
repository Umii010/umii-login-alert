<?php

namespace Umii\LoginAlert\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Config;
use Umii\LoginAlert\Services\DeviceFingerprintService;
use Umii\LoginAlert\Notifications\LoginAlert;

class SendLoginAlertNotification
{
    public function __construct(protected DeviceFingerprintService $fingerprinter = new DeviceFingerprintService())
    {
    }

    public function handle(Login $event): void
    {
        $user = $event->user;

        // User must be Notifiable for notifications
        if (! method_exists($user, 'notify')) {
            return;
        }

        $ip = request()->ip();
        $agent = request()->userAgent() ?? 'Unknown Agent';

        $device = $this->fingerprinter->describe($agent);
        $hash = $this->fingerprinter->fingerprint($ip, $agent);

        $onlyNew = (bool) Config::get('login-alert.only_new_devices', false);

        // If the User model uses TracksLoginAlerts, we can store/check device hashes
        $shouldSend = true;
        if (in_array('Umii\\LoginAlert\\Traits\\TracksLoginAlerts', class_uses_recursive($user))) {
            if ($onlyNew) {
                $shouldSend = $user->isNewLoginDevice($hash);
            }
            // store a record regardless
            $user->rememberLoginDevice($hash, $ip, $agent);
        } elseif ($onlyNew) {
            // If we cannot track devices but config demands only-new, send nothing
            $shouldSend = false;
        }

        if (! $shouldSend) {
            return;
        }

        $location = null;
        if (Config::get('login-alert.include_location', false)) {
            // By default we do not call external services. Provide a hook (callable) via config.
            $resolver = Config::get('login-alert.location_resolver');
            if (is_callable($resolver)) {
                try {
                    $location = $resolver($ip);
                } catch (\Throwable $e) {
                    // ignore location failure
                    $location = null;
                }
            }
        }

        $channels = Config::get('login-alert.channels', ['mail']);

        $user->notify(new LoginAlert(
            ip: $ip,
            device: $device,
            userAgent: $agent,
            location: $location,
            channels: $channels
        ));
    }
}
