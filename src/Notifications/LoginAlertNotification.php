<?php

namespace Umii\LoginAlert\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoginAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $ip,
        protected ?string $device,
        protected ?string $userAgent,
        protected ?string $location
    ) {}

    public function via(object $notifiable): array
    {
        return config('login-alert.channels', ['mail']);
    }


public function toMail(object $notifiable): MailMessage
{
    return (new MailMessage)
        ->subject('🔐 Login Alert - ' . config('app.name'))
        ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
        ->line('We noticed a new login to your account. Here are the details:')
        ->line('')
        ->line('**IP Address:** ' . ($this->ip ?: 'Unknown'))
        ->line('**Location:** ' . ($this->location ?: 'Unknown'))
        ->line('**Device:** ' . ($this->device ?: 'Unknown'))
        ->line('**User Agent:** ' . ($this->userAgent ?: 'Unknown'))
        ->line('')
        ->line('If this was you, no further action is required.')
        ->line('If this wasn’t you, please reset your password immediately and contact support.')
        ->salutation('Regards, ' . config('app.name'));
}

}
