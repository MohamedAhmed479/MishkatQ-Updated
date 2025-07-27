<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SpacedRepetitionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReviewReminder;
use Carbon\Carbon;

class ProcessDailyReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:process-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process daily review schedule and send notifications';

    /**
     * The spaced repetition service.
     *
     * @var SpacedRepetitionService
     */
    protected $spacedRepetitionService;

    /**
     * Create a new command instance.
     *
     * @param SpacedRepetitionService $spacedRepetitionService
     * @return void
     */
    public function __construct(SpacedRepetitionService $spacedRepetitionService)
    {
        parent::__construct();
        $this->spacedRepetitionService = $spacedRepetitionService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting daily review process...');

        // الحصول على جميع المستخدمين النشطين
        // $users = User::where('last_active_at', '>=', Carbon::now()->subMinutes(2))->get();
        $users = User::all();
        $today = Carbon::today()->format('Y-m-d');

        $totalNotificationsSent = 0;

        foreach ($users as $user) {
            try {
                // الحصول على مراجعات اليوم للمستخدم
                $todayReviews = $this->spacedRepetitionService->getTodayReviewsForUser($user->id);

                $reviewCount = $todayReviews->count();

                if ($reviewCount > 0) {
                    // إرسال إشعار للمستخدم
                    Notification::send($user, new ReviewReminder($reviewCount, $today));

                    $totalNotificationsSent++;

                    $this->info("Sent notification to user #{$user->id} for {$reviewCount} reviews");
                }
            } catch (\Exception $e) {
                Log::error("Error processing reviews for user #{$user->id}: {$e->getMessage()}");
                $this->error("Error processing reviews for user #{$user->id}: {$e->getMessage()}");
            }
        }

        $this->info("Process completed. Sent notifications to {$totalNotificationsSent} users.");

        return 0;
    }
}
