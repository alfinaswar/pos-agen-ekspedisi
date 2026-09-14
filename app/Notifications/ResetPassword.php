<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject(' Reset Password - MAUREKAP')
            ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna') . '! 👋')
            ->line('Kami menerima permintaan untuk mereset password akun MAUREKAP Anda.')
            ->action('Reset Password Saya', $url)
            ->line('Tautan ini hanya berlaku selama 60 menit.')
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.')
            ->salutation('Terima kasih,<br>Tim MAUREKAP');
    }
}
