<?php

namespace Umii\LoginAlert;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Umii\LoginAlert\Listeners\SendLoginAlertNotification;

class LoginAlertServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/login-alert.php', 'login-alert');
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/login-alert.php' => config_path('login-alert.php'),
        ], 'config');

        // Publish migration
        if (! class_exists('CreateLoginAlertsTable')) {
            $timestamp = date('Y_m_d_His');
            $this->publishes([
                __DIR__ . '/../database/migrations/create_login_alerts_table.php' => database_path("migrations/{$timestamp}_create_login_alerts_table.php"),
            ], 'migrations');
        }

        // Listen to login events
        Event::listen(Login::class, SendLoginAlertNotification::class);
    }
}
