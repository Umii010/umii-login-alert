# 🔐 Umii Login Alert

**Package:** `umii/login-alert`  
Send an email or SMS when a user logs in. Includes IP address, device info, and optional location. Can alert **only on new devices/IPs**.

## ✨ Features
- Email notification out of the box (SMS via Vonage optional)
- Includes IP, device summary, user agent
- Optional location string (you supply the resolver)
- "Only new devices" mode using a simple device fingerprint
- Minimal setup; auto-discovered service provider
- Queueable notifications supported

## 📦 Installation
```bash
composer require umii/login-alert
php artisan vendor:publish --tag=config    # publish config/login-alert.php
php artisan vendor:publish --tag=migrations
php artisan migrate
```

## ⚙️ Quick Setup
1) **Add trait** to your `User` model for new-device memory:
```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Umii\LoginAlert\Traits\TracksLoginAlerts;

class User extends Authenticatable
{
    use Notifiable, TracksLoginAlerts;
}
```

2) **Ensure notifications work via mail** (Laravel mail configured).  
   For SMS (optional), install Vonage/Nexmo and set your `routeNotificationForVonage` on the User.

3) (Optional) **Location enrichment** (no network calls included by default):
```php
// In a service provider or a boot file
config(['login-alert.include_location' => true]);
config(['login-alert.location_resolver' => function (string $ip) {
    // Return a human string like "Karachi, PK" based on your own logic/service
    return null;
}]);
```

## 🧠 How it Works
- Listens to `Illuminate\Auth\Events\Login`
- Builds a fingerprint from `IP + User-Agent`
- If `only_new_devices = true`, sends only when first seen
- Stores login fingerprints in `login_alerts` table (when trait used)

## 📨 Example Email
```
Subject: New login on YourApp
IP: 203.0.113.10
Device: Windows - Chrome
User Agent: Mozilla/5.0 (...)
Location: Karachi, PK (if enabled)
```

## 🧪 Config (`config/login-alert.php`)
```php
return [
    'channels' => ['mail'],       // or ['mail', 'vonage']
    'only_new_devices' => true,   // send only when new device is seen
    'include_location' => false,  // supply a resolver if you enable this
    'location_resolver' => null,  // callable(string $ip): ?string
];
```

## 📁 Structure
```
umii-login-alert/
├── src/
│   ├── Listeners/SendLoginAlertNotification.php
│   ├── Notifications/LoginAlert.php
│   ├── Services/DeviceFingerprintService.php
│   ├── Traits/TracksLoginAlerts.php
│   ├── Models/LoginAlert.php
│   └── LoginAlertServiceProvider.php
├── config/login-alert.php
├── database/migrations/create_login_alerts_table.php
├── tests/Feature/
├── tests/Unit/
├── composer.json
└── README.md
```

## 🧩 Notes
- If you don't add the `TracksLoginAlerts` trait, alerts will still send, but the **"only new devices"** mode is disabled.
- To queue notifications, make your notification implement `ShouldQueue` or configure queue globally.

## 📝 License
MIT © Muhammad
