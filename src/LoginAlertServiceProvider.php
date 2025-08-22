<?php

namespace Umii\LoginAlert;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Umii\LoginAlert\Resolvers\IpApiLocationResolver;
use Umii\LoginAlert\Notifications\LoginAlertNotification;
use Umii\LoginAlert\Models\LoginAlert;

class LoginAlertServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/login-alert.php', 'login-alert');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/login-alert.php' => config_path('login-alert.php'),
        ], 'login-alert-config');

        if (! class_exists('CreateLoginAlertsTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_login_alerts_table.php.stub' =>
                    database_path('migrations/'.date('Y_m_d_His').'_create_login_alerts_table.php'),
            ], 'login-alert-migrations');
        }

        // Auto-load vendor migrations so publishing is optional
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Event::listen(Login::class, function ($event) {
            $user = $event->user;
            $request = request();

            $ip = $request?->ip() ?? '0.0.0.0';
            $ua = $request?->userAgent() ?? null;
            $device = $this->summarizeDevice($ua);

            // Default resolver (no config change required)
            $resolver = app(IpApiLocationResolver::class);
            $include = (bool) config('login-alert.include_location', true);
            $location = $include ? $resolver($ip) : null;

            $onlyNew = (bool) config('login-alert.only_new_devices', false);
            $fingerprint = $this->fingerprint($ip, $ua);

            if ($onlyNew) {
                $exists = LoginAlert::where('user_id', $user->getKey())
                    ->where('fingerprint', $fingerprint)
                    ->exists();
                if ($exists) {
                    LoginAlert::where('user_id', $user->getKey())
                        ->where('fingerprint', $fingerprint)
                        ->latest('id')
                        ->first()?->touch();
                    return;
                }
            }

            LoginAlert::create([
                'user_id' => $user->getKey(),
                'ip' => $ip,
                'location' => $location,
                'device' => $device,
                'user_agent' => $ua,
                'fingerprint' => $fingerprint,
            ]);

            if (method_exists($user, 'notify')) {
                $user->notify(new LoginAlertNotification($ip, $device, $ua, $location));
            }
        });
    }

    protected function summarizeDevice(?string $ua): ?string
    {
        if (! $ua) { return null; }
        $platform = 'Unknown OS';
        if (stripos($ua, 'Windows') !== false) $platform = 'Windows';
        elseif (stripos($ua, 'Mac OS') !== false || stripos($ua, 'Macintosh') !== false) $platform = 'macOS';
        elseif (stripos($ua, 'Android') !== false) $platform = 'Android';
        elseif (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) $platform = 'iOS';
        elseif (stripos($ua, 'Linux') !== false) $platform = 'Linux';

        $browser = 'Unknown Browser';
        if (stripos($ua, 'Edg') !== false) $browser = 'Edge';
        elseif (stripos($ua, 'Chrome') !== false && stripos($ua, 'Chromium') === false) $browser = 'Chrome';
        elseif (stripos($ua, 'Firefox') !== false) $browser = 'Firefox';
        elseif (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) $browser = 'Safari';
        elseif (stripos($ua, 'OPR') !== false || stripos($ua, 'Opera') !== false) $browser = 'Opera';

        return $platform . ' - ' . $browser;
    }

    protected function fingerprint(?string $ip, ?string $ua): string
    {
        return hash('sha256', ($ip ?? '') . '|' . ($ua ?? ''));
    }
}
