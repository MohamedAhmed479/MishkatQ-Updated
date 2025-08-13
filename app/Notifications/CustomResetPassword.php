<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\MailMessage as LaravelMailMessage;

class CustomResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('رمز إعادة تعيين كلمة المرور')
            ->view('emails.reset-password', [
                'code' => $this->code,
                'expire' => config('auth.passwords.users.expire')
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
