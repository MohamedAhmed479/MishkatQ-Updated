<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class UserVerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // public $code;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        // $this->code = $code;
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
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl(object $notifiable)
    {
        // Ensure we're using the correct root URL for signed routes
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
        
        // Log URL generation for debugging (remove in production if not needed)
        \Illuminate\Support\Facades\Log::info('Email verification URL generated', [
            'user_id' => $notifiable->getKey(),
            'email' => $notifiable->getEmailForVerification(),
            'app_url' => config('app.url'),
            'url' => $url
        ]);
        
        return $url;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);
        return (new MailMessage)
            ->subject('تأكيد البريد الإلكتروني')
            ->view('emails.verify-email', [
                // 'code' => $this->code,
                'url' => $url,
                'user' => $notifiable,
            ]);


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
