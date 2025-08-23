<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomPasswordResetNotification extends Notification
{
    use Queueable;

    protected $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset()
        ], false));
 
 
        return (new MailMessage)
            ->subject('Terlupa katalaluan? Reset kembali di sini')
            ->greeting("Salam sejahtera {$notifiable->name},")
            ->line('Kami menerima cubaan untuk mereset kembali katalaluan anda ke sistem. Sekiranya ini anda, teruskan ke halaman Reset Kataluan berikut.')
            ->action('Reset Katalaluan', $url)
            ->line('Jika anda tidak meminta untuk mereset katalaluan ini, abaikan email ini.')
            ->salutation('Terima kasih.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
