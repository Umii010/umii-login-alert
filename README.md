# 🔐 Umii Login Alert

**Package:** `umii/login-alert`  
Send an email (and optionally SMS) whenever a user logs in. Includes IP address, device info, user agent, and location (auto-resolved). Supports **new device/IP-only alerts**.

---

## ✨ Features
- 📩 Sends login alerts via **Laravel Notifications** (Mail/SMS supported)  
- 🌍 Auto-detects **IP, device, user agent, location**  
- 🆕 "Only new devices" mode with fingerprint storage  
- ⚡ Plug & play: **auto-discovered service provider**  
- ⏳ Queueable notifications supported  

---

## 📦 Installation
```bash
composer require umii/login-alert
php artisan vendor:publish --tag=config
php artisan vendor:publish --tag=migrations
php artisan migrate
```

---

## ⚙️ Setup

1. **Configure mail in `.env`** (required for email alerts):  
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your-username
   MAIL_PASSWORD=your-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=no-reply@yourapp.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

2. **(Optional) Track new devices**  
   Add the provided trait to your `User` model to enable *"only new device"* alerts:
   ```php
   use Illuminate\Foundation\Auth\User as Authenticatable;
   use Illuminate\Notifications\Notifiable;
   use Umii\LoginAlert\Traits\TracksLoginAlerts;

   class User extends Authenticatable
   {
       use Notifiable, TracksLoginAlerts;
   }
   ```

3. **(Optional) SMS support**  
   - Install Vonage/Nexmo or Twilio driver  
   - Implement `routeNotificationForVonage()` or `routeNotificationForTwilio()` on your `User` model.  

---

## ⚙️ Configuration
File: `config/login-alert.php`

```php
return [
    // Send alerts only on new devices/IPs?
    'only_new_devices' => false,

    // Location resolver: default uses ip-api.com (free IP lookup)
    'location_resolver' => Umii\LoginAlert\Support\DefaultLocationResolver::class,
];
```

---

## 🧠 How It Works
- Listens to `Illuminate\Auth\Events\Login`  
- Extracts IP, User-Agent, device info  
- Resolves location automatically via configured resolver  
- Sends notification immediately (queued if your app uses queues)  
- If `only_new_devices = true`, compares with stored fingerprints in `login_alerts` table  

---

## 📨 Example Email

```
Subject: 🔐 Login Alert - YourApp

Hello John Doe,

We noticed a new login to your account. Here are the details:

IP Address: 203.0.113.10
Location: Karachi, PK
Device: Windows - Chrome
User Agent: Mozilla/5.0 (...)

If this was you, no further action is required.
If this wasn’t you, please reset your password immediately and contact support.

Regards,
YourApp
```

---

## 🧩 Notes
- Without the `TracksLoginAlerts` trait → every login triggers an alert.  
- With the trait → alerts trigger only on new device/IP (depending on config).  
- Supports Laravel’s native `ShouldQueue` for async notifications.  

---

## 📝 License
MIT © Muhammad  
