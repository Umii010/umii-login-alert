<?php

namespace Umii\LoginAlert\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoginAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ip;
    protected $location;
    protected $userAgent;

    public function __construct(string $ip, string $location, ?string $userAgent = null)
    {
        $this->ip = $ip;
        $this->location = $location;
        $this->userAgent = $userAgent;
    }

    public function via(object $notifiable): array
    {
        return config('login-alert.notify_via', ['mail']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Login Detected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We detected a new login to your account.')
            ->line('**IP:** ' . $this->ip)
            ->line('**Location:** ' . $this->location)
            ->line('**Device:** ' . ($this->userAgent ?: 'Unknown'))
            ->line('If this wasn’t you, we recommend updating your password immediately.');
    }
}
