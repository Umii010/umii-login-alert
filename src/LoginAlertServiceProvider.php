<?php

namespace Umii\LoginAlert;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Umii\LoginAlert\Notifications\LoginAlertNotification;

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
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');

        Event::listen(Login::class, function ($event) {
            $user = $event->user;
            $ip = request()->ip();
            $userAgent = request()->userAgent();
            $resolver = config('login-alert.location_resolver');
            $location = is_callable($resolver) ? $resolver($ip) : 'Unknown';

            if (method_exists($user, 'notify')) {
                $user->notify(new LoginAlertNotification($ip, $location, $userAgent));
            }
        });
    }
}
