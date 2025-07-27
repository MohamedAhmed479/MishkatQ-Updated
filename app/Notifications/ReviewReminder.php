<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class ReviewReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reviewCount;
    protected $reviewDate;

    public function __construct(int $reviewCount, string $reviewDate)
    {
        $this->reviewCount = $reviewCount;
        $this->reviewDate = $reviewDate;
    }

    public function via($notifiable)
    {
        $channels = ['database']; // استخدام البريد الإلكتروني وقاعدة البيانات

        return $channels;
    }

    public function toDatabase($notifiable)
    {
        $reviewWord = $this->reviewCount == 1 ? 'مراجعة' : 'مراجعات';

        return [
            'title' => '📖 تذكير بمراجعة القرآن الكريم',
            'body' => "لديك {$this->reviewCount} {$reviewWord} مستحقة اليوم {$this->reviewDate}. لا تنسَ المراجعة لتثبيت الحفظ بإذن الله.",
            'icon' => '📖',
            'review_count' => $this->reviewCount,
            'review_date' => $this->reviewDate,
            'action_url' => '/reviews/today',
        ];
    }
}
