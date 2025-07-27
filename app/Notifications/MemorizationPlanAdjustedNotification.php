<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class MemorizationPlanAdjustedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Notification data
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
//        $url = $this->data['status'] === 'overdue'
//            ? url('/memorization-plans/' . $this->data['plan_id'] . '/active/')
//            : url('/memorization-plans/' . $this->data['plan_id']);

        return (new MailMessage)
            ->subject('تم تحديث خطة الحفظ الخاصة بك - مشكاة')
            ->view('emails.memorizationPlanAdjusted', [
                'user' => $notifiable,
                'messageText' => $this->data['message'],
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'plan_id' => $this->data['plan_id'],
            'plan_name' => $this->data['plan_name'],
            'status' => $this->data['status'],
            'message' => $this->data['message'],
//            'action_url' => $this->data['status'] === 'overdue'
//                ? url('/memorization/resume/' . $this->data['plan_id'])
//                : url('/memorization/plan/' . $this->data['plan_id']),
        ];
    }
}
