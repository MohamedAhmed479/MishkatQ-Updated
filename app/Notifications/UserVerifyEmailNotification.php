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
        // Generate relative signed route to avoid domain/proxy issues
        // The signature will be based on the path only, not the domain
        $relativeUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())],
            false // absolute = false, creates relative URL
        );
        
        // Build absolute URL using APP_URL for email
        $baseUrl = config('app.url', 'https://mishkatq.app');
        $url = rtrim($baseUrl, '/') . $relativeUrl;
        
        // Log URL generation for debugging
        \Illuminate\Support\Facades\Log::info('Email verification URL generated', [
            'user_id' => $notifiable->getKey(),
            'email' => $notifiable->getEmailForVerification(),
            'app_url' => config('app.url'),
            'base_url' => $baseUrl,
            'relative_url' => $relativeUrl,
            'final_url' => $url
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
