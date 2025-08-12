<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Notifications\DatabaseNotification as Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->select('id')->get();
        $admins = Admin::query()->select('id')->get();

        if ($users->isEmpty() && $admins->isEmpty()) {
            $this->command?->warn('No users or admins found. Skipping NotificationSeeder.');
            return;
        }

        $notificationTypes = [
            \App\Notifications\UserVerifyEmailNotification::class,
            \App\Notifications\CustomResetPassword::class,
            \App\Notifications\ReviewReminder::class,
            \App\Notifications\MemorizationPlanAdjustedNotification::class,
        ];

        $createCount = 80;
        $rows = [];

        for ($i = 0; $i < $createCount; $i++) {
            // Choose notifiable (prefer users if available)
            $chooseAdmin = !$admins->isEmpty() && (random_int(1, 100) <= 20); // ~20% admins
            if ($chooseAdmin && !$admins->isEmpty()) {
                $notifiableType = Admin::class;
                $notifiableId = $admins->random()->id;
            } else {
                $notifiableType = User::class;
                $notifiableId = $users->isNotEmpty() ? $users->random()->id : ($admins->random()->id ?? null);
            }

            if ($notifiableId === null) {
                // Safety check
                continue;
            }

            $type = Arr::random($notificationTypes);
            $data = $this->fakePayloadForType($type);

            $createdAt = now()
                ->subDays(random_int(0, 60))
                ->subMinutes(random_int(0, 24 * 60));

            $isRead = random_int(1, 100) <= 55; // ~55% read
            $readAt = $isRead ? (clone $createdAt)->addMinutes(random_int(5, 72 * 60)) : null;

            $rows[] = [
                'id' => (string) Str::uuid(),
                'type' => $type,
                'notifiable_type' => $notifiableType,
                'notifiable_id' => $notifiableId,
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'read_at' => $readAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        if (!empty($rows)) {
            // Use insert for performance; we JSON-encoded data above
            Notification::query()->insert($rows);
            $this->command?->info('Inserted '.count($rows).' notifications.');
        }
    }

    private function fakePayloadForType(string $type): array
    {
        return match ($type) {
            \App\Notifications\UserVerifyEmailNotification::class => [
                'subject' => 'تأكيد البريد الإلكتروني',
                'message' => 'يرجى تأكيد بريدك الإلكتروني لإكمال التسجيل.',
                'url' => config('app.frontend_url').'/auth/verify-email?token=' . Str::random(40),
            ],
            \App\Notifications\CustomResetPassword::class => [
                'subject' => 'إعادة تعيين كلمة المرور',
                'message' => 'طلبت إعادة تعيين كلمة المرور لحسابك.',
                'url' => config('app.frontend_url').'/auth/reset-password?token=' . Str::random(64),
            ],
            \App\Notifications\ReviewReminder::class => [
                'title' => 'تذكير بالمراجعة',
                'message' => 'لديك مراجعات مقررة اليوم ضمن خطة الحفظ.',
                'due_reviews_count' => random_int(1, 7),
            ],
            \App\Notifications\MemorizationPlanAdjustedNotification::class => [
                'title' => 'تم ضبط خطة الحفظ تلقائيًا',
                'reason' => Arr::random(['low_performance', 'schedule_change', 'consistency_improvement']),
                'adjustment_summary' => 'تم تعديل عدد الآيات اليومية بما يتناسب مع أدائك الأخير.',
            ],
            default => [
                'message' => 'تنبيه عام في النظام.',
            ],
        };
    }
}


