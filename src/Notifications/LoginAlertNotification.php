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
            ->subject('Login Alert')
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line('A login to your account was detected.')
            ->line('IP: ' . $this->ip)
            ->line('Location: ' . ($this->location ?: 'Unknown'))
            ->line('Device: ' . ($this->device ?: 'Unknown'))
            ->line('User Agent: ' . ($this->userAgent ?: 'Unknown'))
            ->line('If this was you, no action is needed. If not, please reset your password and contact support.')
            ->line('Regards,')
            ->line(config('app.name'));
    }
}
