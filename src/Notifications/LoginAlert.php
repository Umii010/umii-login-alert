<?php

namespace Umii\LoginAlert\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LoginAlert extends Notification
{
    use Queueable;

    public function __construct(
        protected string $ip,
        protected string $device,
        protected string $userAgent,
        protected ?string $location = null,
        protected array $channels = ['mail']
    ) {
    }

    public function via($notifiable): array
    {
        return $this->channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $app = config('app.name');

        $mail = (new MailMessage)
            ->subject("New login on {$app}")
            ->greeting('Login Alert')
            ->line("A login to your account was detected.")
            ->line("**IP:** {$this->ip}")
            ->line("**Device:** {$this->device}")
            ->line("**User Agent:** {$this->userAgent}");

        if ($this->location) {
            $mail->line("**Location:** {$this->location}");
        }

        $mail->line('If this was you, no action is needed. If not, please reset your password and contact support.');

        return $mail;
    }

    // Optional: Vonage/SMS support if configured
    public function toVonage($notifiable)
    {
        $text = sprintf(
            'Login Alert: IP %s, Device %s%s',
            $this->ip,
            $this->device,
            $this->location ? (', Location ' . $this->location) : ''
        );

        // Return plain object to avoid hard dependency.
        return (object)['content' => $text];
    }

    public function toArray($notifiable): array
    {
        return [
            'ip' => $this->ip,
            'device' => $this->device,
            'user_agent' => $this->userAgent,
            'location' => $this->location,
        ];
    }
}
