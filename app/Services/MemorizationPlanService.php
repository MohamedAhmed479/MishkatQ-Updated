<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\MemorizationPlan;
use App\Models\PlanItem;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\Verse;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use App\Repositories\Interfaces\PlanItemInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemorizationPlanService
{
    public $memorizationPlanRepository;
    protected $planItemRepository;

    /**
     * Time estimation constants based on user level (minutes per word)
     */
    const MEMORIZATION_RATES = [
        'beginner' => 0.6667,   // ~1.5 words per minute
        'intermediate' => 0.4,  // ~2.5 words per minute
        'advanced' => 0.1667,   // ~6 words per minute
    ];


    /**
     * Percentage of time allocated for new memorization vs revision
     */
    const NEW_MEMORIZATION_PERCENTAGE = 0.6; // 60% for new memorization

    /**
     * Constructor to initialize the service with required repositories.
     *
     * @param MemorizationPlanInterface $memorizationPlanRepository  Repository for managing memorization plans.
     * @param PlanItemInterface $planItemRepository  Repository for managing daily plan items.
     */
    public function __construct(MemorizationPlanInterface $memorizationPlanRepository, PlanItemInterface $planItemRepository)
    {
        $this->memorizationPlanRepository = $memorizationPlanRepository;
        $this->planItemRepository = $planItemRepository;
    }


    /**
     * Retrieve a memorization plan and its paginated items for the authenticated user.
     *
     * @param int $planId The ID of the memorization plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function planDetailsAndHisItems(int $planId): JsonResponse
    {
        $plan = $this->memorizationPlanRepository->find($planId);

        if (!$plan || $plan->user_id !== Auth::id()) {
            return ApiResponse::error("الخطة غير موجودة", 404);
        }

        $planItems = $this->planItemRepository->getPlanItems($plan->id)->paginate(10);

        return ApiResponse::success([
            'plan' => $plan,
            'plan_items' => $planItems
        ]);
    }

    /**
     * Pause an active memorization plan for the authenticated user.
     *
     * @param int $planId The ID of the memorization plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function handlePauseMemorizationPlan(int $planId): JsonResponse
    {
        $memorizationPlan = $this->memorizationPlanRepository->findPlanForUser(Auth::id(), $planId);

        if (!$memorizationPlan) {
            return ApiResponse::notFound("خطة الحفظ غير موجودة");
        }

        if ($memorizationPlan->status === "paused") {
            return ApiResponse::error("لقد تم إيقاف الخطة مؤقتًا من قبل.");
        }

        $this->memorizationPlanRepository->pausePlan($memorizationPlan->id);

        return ApiResponse::success(null, "تم إيقاف الخطة بنجاح.");
    }

    /**
     * Reactivate a paused memorization plan and reset its item dates.
     *
     * Applies activation logic and returns the updated plan with paginated items.
     *
     * @param int $planId The ID of the memorization plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleActivateMemorizationPlan(int $planId): JsonResponse
    {
        $memorizationPlan = $this->memorizationPlanRepository->findPlanForUser(Auth::id(), $planId);

        if (!$memorizationPlan) {
            return ApiResponse::notFound("خطة الحفظ غير موجودة");
        }

        if ($memorizationPlan->status === "active") {
            return ApiResponse::error("خطة الحفظ مفعلة");
        }

        $this->applyPlanActivationLogic($memorizationPlan);

        $paginatedItems = $this->planItemRepository->getPlanItems($memorizationPlan->id)->paginate(10);

        return ApiResponse::success([
            'plan' => $memorizationPlan,
            'plan_items' => $paginatedItems,
        ], "تم تفعيل الخطة مرة أخرى وتم إعادة ترتيب التواريخ بنجاح.");
    }


    /**
     * Creates a new memorization plan for the user and distributes the verses
     * into daily memorization items based on user's preferences.
     *
     * Validates if the user already has an active plan.
     * Ensures the user preferences are set before proceeding.
     * Generates the plan, distributes verses, calculates end date, and returns a success response.
     *
     * @param User $user  The user who wants to create a new memorization plan.
     * @param array $validatedData  The validated input data used to create the plan.
     *
     * @return JsonResponse  A response containing the plan details or an error message.
     */
    public function makeNewMemoPlanWithHisItems(User $user, Array $validatedData): JsonResponse
    {
        if($this->memorizationPlanRepository->isUserHasActivePlan($user)){
            return ApiResponse::error("لديك خطة حفظ نشطة. يُرجى إيقافها مؤقتًا أو إكمالها قبل البدء بخطة جديدة.", 400);
        }

        $userPreference = $user->preference;
        if (!$userPreference) return ApiResponse::error('لم يتم العثور على تفضيلات المستخدم. يُرجى ضبط تفضيلاتك أولاً.', 404);

        if($userPreference->daily_minutes == 0) return ApiResponse::error(' يُرجى ضبط تفضيلاتك أولاً.', 404);

        $result = $this->generateMemorizationPlan($user, $userPreference, $validatedData);
        if($result['status'] === "success"){
            $memoPlan = $result['memoPlan'];
            $versesToMemorize = $result['versesToMemorize'];
            $dailyCapacity = $result['dailyCapacity'];
        } else {
            return ApiResponse::error($result['message']);
        }

        // Distributes verses into daily memorization items based on user's daily capacity.
        $this->generateDailyMemorizationItems($memoPlan, $versesToMemorize, $dailyCapacity);

        $target_date = $this->memorizationPlanRepository->getLastTargetDate($memoPlan->id);
        $total_days = $memoPlan->start_date->diffInDays($target_date) + 1;

        $this->memorizationPlanRepository->update($memoPlan->id, ['end_date' => $target_date]);

        $data = [
            'plan_id' => $memoPlan->id,
            'plan_name' => $memoPlan->name,
            'plan_description' => $memoPlan->description,
            'status' => $memoPlan->status,
            'total_days' => $total_days,
            'start_date' => $memoPlan->start_date,
            'target_date' => $target_date,
            'total_verses' => $versesToMemorize->count(),
        ];

        return ApiResponse::created($data, "تم إنشاء خطة الحفظ بنجاح.");
    }

    /**
     * Generates a memorization plan based on user preferences and selected surah range.
     *
     * @param User $user
     * @param UserPreference $userPreference
     * @param array $validatedData
     * @return array
     */
    protected function generateMemorizationPlan(User $user, UserPreference $userPreference, Array $validatedData)
    {
        // Get all verses to memorize
        $versesToMemorize = $this->getVersesToMemorize($validatedData["surah_start_id"], $validatedData["surah_end_id"]);
        if ($versesToMemorize->isEmpty()) {
            return [
                "status" => "error",
                "message" => "لم يتم العثور على آيات في النطاق المحدد."
            ];
        }

        // Calculate daily capacity based on user preferences
        $dailyCapacity = $this->calculateDailyCapacity($userPreference);

        // Calculate end date based on daily capacity
        $totalDaysNeeded = $this->calculateRequiredDays($versesToMemorize, $dailyCapacity);
        $endDate = (clone Carbon::parse($validatedData['start_date']))->addDays($totalDaysNeeded - 1); // -1 because start date is included

        $memoPlan = $this->makeMemoPlan($user, $validatedData, $endDate);

        return [
            "status" => "success",
            "memoPlan" => $memoPlan,
            "versesToMemorize" => $versesToMemorize,
            "dailyCapacity" => $dailyCapacity,
        ];
    }


    /**
     * Creates and stores a new active memorization plan.
     *
     * @param User $user
     * @param array $validatedData
     * @param Carbon $end_date
     * @return MemorizationPlan
     */
    protected function makeMemoPlan(User $user, Array $validatedData, Carbon $end_date): MemorizationPlan
    {
        $planData = [
            "user_id" => $user->id,
            "name" => $validatedData["name"],
            "description" => $validatedData["description"] ?? null,
            "start_date" => Carbon::parse($validatedData["start_date"]),
            "end_date" => $end_date,
            "status" => "active",
        ];

        return $this->memorizationPlanRepository->makeActivePlan($planData);
    }

    /**
     * Get all verses between the start and end surahs
     *
     * @param int $surahStartId
     * @param int $surahEndId
     * @return Collection
     */
    protected function getVersesToMemorize(int $surahStartId, int $surahEndId): Collection
    {
        // Get all verses from the specified range of surahs
        return Verse::whereHas('chapter', function ($query) use ($surahStartId, $surahEndId) {
            $query->where('id', '>=', $surahStartId)
                ->where('id', '<=', $surahEndId);
        })
            ->with(['chapter', 'words']) // Eager load related data
            ->orderBy('chapter_id')
            ->orderBy('verse_number')
            ->get();
    }

    /**
     * Calculate daily memorization capacity based on user preferences
     *
     * @param UserPreference $userPreference
     * @return int (number of words per day)
     */
    protected function calculateDailyCapacity(UserPreference $userPreference): int
    {
        // Total daily available time in minutes
        $dailyMinutes = $userPreference->daily_minutes;

        // Time allocated for new memorization (60% of total time)
        $newMemorizationMinutes = $dailyMinutes * self::NEW_MEMORIZATION_PERCENTAGE;

        // Get memorization rate based on user level (minutes per word)
        $memorizationRate = self::MEMORIZATION_RATES[$userPreference->current_level];

        // Calculate how many words can be memorized per day
        $wordsPerDay = floor($newMemorizationMinutes / $memorizationRate);

        return max(1, $wordsPerDay); // Return at least 1 word per day
    }

    /**
     * Calculate the total days required to memorize all verses
     *
     * @param Collection $verses
     * @param int $dailyCapacity
     * @return int
     */
    protected function calculateRequiredDays(Collection $verses, int $dailyCapacity): int
    {
        // Calculate total words in all verses
        $totalWords = $verses->sum(function ($verse) {
            return $verse->words->count();
        });

        // Calculate required days (round up)
        return ceil($totalWords / $dailyCapacity);
    }


    /**
     * Distributes verses into daily memorization items based on user's daily capacity.
     *
     * @param MemorizationPlan $plan
     * @param Collection $verses
     * @param int $dailyCapacity
     * @return void
     */
    protected function generateDailyMemorizationItems(
        MemorizationPlan $plan,
        Collection $verses,
        int $dailyCapacity,
    ): void {
        if ($verses->isEmpty()) {
            return;
        }

        $currentDate = Carbon::parse($plan->start_date);
        $sequence = 1;
        $currentDayWords = 0;
        $verseStart = null;
        $currentSurah = null;

        foreach ($verses as $index => $verse) {
            $verseWords = $verse->words->count();

            // Start a new group for the first verse or a new surah
            if ($verseStart === null || $currentSurah !== $verse->chapter_id) {
                $verseStart = $verse;
                $currentSurah = $verse->chapter_id;
            }

            $currentDayWords += $verseWords;

            // Determine if we should create a plan item
            $isLastVerse = ($index === $verses->count() - 1);
            $isNewSurahNext = !$isLastVerse && $verses[$index + 1]->chapter_id !== $currentSurah;

            if ($currentDayWords >= $dailyCapacity || $isLastVerse || $isNewSurahNext) {
                $this->planItemRepository->storePlanItem([
                    'plan_id' => $plan->id,
                    'quran_surah_id' => $currentSurah,
                    'verse_start_id' => $verseStart->id,
                    'verse_end_id' => $verse->id, // Include current verse
                    'target_date' => $currentDate,
                    'sequence' => $sequence,
                ]);


                // Prepare for next day
                $currentDate->addDay();
                $currentDayWords = 0;
                $verseStart = null;
                $sequence++;
            }
        }
    }


    /**
     * Activates the given memorization plan and updates its scheduling.
     *
     * This includes:
     * - Setting the plan status to active
     * - Reassigning target dates for uncompleted items
     * - Updating the plan's end date based on the latest item
     *
     * @param \App\Models\MemorizationPlan $memorizationPlan
     * @return void
     */
    private function applyPlanActivationLogic(MemorizationPlan $memorizationPlan): void
    {
        DB::transaction(function () use ($memorizationPlan) {
            $this->memorizationPlanRepository->avtivePlan($memorizationPlan->id);
            $this->resetTargetDatesForUncompletedItems($memorizationPlan);
            $this->updatePlanEndDateBasedOnItems($memorizationPlan);
        });
    }

    /**
     * Resets the target dates of uncompleted items in the plan,
     * starting from tomorrow and incrementing by one day for each item.
     *
     * @param \App\Models\MemorizationPlan $plan
     * @return void
     */
    private function resetTargetDatesForUncompletedItems(MemorizationPlan $plan): void
    {
        $uncompletedItems = $plan->planItems()
            ->where('is_completed', 0)
            ->orderBy('target_date', 'ASC')
            ->get();

        foreach ($uncompletedItems as $index => $item) {
            $newDate = Carbon::today()->addDays($index + 1);
            $item->update(['target_date' => $newDate]);
        }
    }

    /**
     * Updates the plan's end date to match the latest target date
     * among all plan items.
     *
     * @param \App\Models\MemorizationPlan $plan
     * @return void
     */
    private function updatePlanEndDateBasedOnItems(MemorizationPlan $plan): void
    {
        $end_date = $this->memorizationPlanRepository->getLastTargetDate($plan->id);
        $plan->update(['end_date' => $end_date]);
    }
}
