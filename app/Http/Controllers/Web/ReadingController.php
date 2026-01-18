<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ReadingPlan;
use App\Models\Reciter;
use App\Models\Tafsir;
use App\Models\Verse;
use App\Services\ReadingPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ReadingController extends Controller
{
    protected ReadingPlanService $readingPlanService;

    public function __construct(ReadingPlanService $readingPlanService)
    {
        $this->readingPlanService = $readingPlanService;
    }

    /**
     * Reading Hub - Main dashboard for reading plans
     */
    public function index(): Response
    {
        $user = Auth::user();
        
        // Get active plan with daily wird
        $activePlan = null;
        $statistics = null;
        
        if ($user) {
            $plan = $user->activeReadingPlan();
            if ($plan) {
                $activePlan = $this->readingPlanService->getPlanWithDailyWird($plan->id);
            }
            $statistics = $this->readingPlanService->getStatistics($user);
        }
        
        $suggestions = $this->readingPlanService->getSuggestedPlans();

        return Inertia::render('Reading/Index', [
            'activePlan' => $activePlan?->getData(true),
            'statistics' => $statistics?->getData(true),
            'suggestions' => $suggestions->getData(true),
        ]);
    }

    /**
     * Create a new reading plan form
     */
    public function create(): Response
    {
        $suggestions = $this->readingPlanService->getSuggestedPlans();

        return Inertia::render('Reading/Create', [
            'suggestions' => $suggestions->getData(true),
        ]);
    }

    /**
     * Store a new reading plan
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'type' => 'nullable|in:sequential,custom',
                'pages_per_day' => 'nullable|integer|min:1|max:100',
                'start_page' => 'nullable|integer|min:1|max:604',
                'end_page' => 'nullable|integer|min:1|max:604',
                'start_date' => 'nullable|date',
                'reading_mode' => 'nullable|in:hadr,tadabbur',
                'settings' => 'nullable|array',
            ]);

            // Check for existing active plan
            $existingPlan = $user->readingPlans()->active()->first();
            if ($existingPlan) {
                return back()->withErrors([
                    'error' => 'لديك خطة قراءة نشطة. يُرجى إيقافها أو إكمالها قبل البدء بخطة جديدة.'
                ]);
            }

            // Prepare plan data
            $type = $validated['type'] ?? 'sequential';
            $startPage = $type === 'sequential' ? 1 : ($validated['start_page'] ?? 1);
            $endPage = $type === 'sequential' ? ReadingPlan::TOTAL_QURAN_PAGES : ($validated['end_page'] ?? ReadingPlan::TOTAL_QURAN_PAGES);
            
            $pagesPerDay = $validated['pages_per_day'] ?? 1;
            $startDate = \Carbon\Carbon::parse($validated['start_date'] ?? now());

            // Calculate end date
            $totalPages = $endPage - $startPage + 1;
            $daysRequired = (int) ceil($totalPages / $pagesPerDay);
            $endDate = isset($validated['end_date']) 
                ? \Carbon\Carbon::parse($validated['end_date']) 
                : (clone $startDate)->addDays($daysRequired - 1);

            // Default settings
            $settings = array_merge(ReadingPlan::DEFAULT_SETTINGS, $validated['settings'] ?? []);

            // Create plan
            $plan = ReadingPlan::create([
                'user_id' => $user->id,
                'name' => $validated['name'] ?? 'خطة ختم القرآن',
                'description' => $validated['description'] ?? null,
                'type' => $type,
                'target_type' => $validated['target_type'] ?? 'pages',
                'pages_per_day' => $pagesPerDay,
                'current_page' => $startPage,
                'start_page' => $startPage,
                'end_page' => $endPage,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reading_mode' => $validated['reading_mode'] ?? 'hadr',
                'status' => ReadingPlan::STATUS_ACTIVE,
                'settings' => $settings,
                'current_streak' => 0,
                'longest_streak' => 0,
                'hatmah_count' => 0,
            ]);

            return redirect()->route('user.reading.index')
                ->with('success', 'تم إنشاء خطة القراءة بنجاح');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Reading Experience - Main reading interface
     */
    public function experience(Request $request, int $planId): Response
    {
        $user = Auth::user();
        $plan = ReadingPlan::where('id', $planId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Get daily wird (today's reading target)
        $dailyWird = $plan->getDailyWird();
        $startPage = $dailyWird['start_page'];
        $endPage = $dailyWird['end_page'];
        
        // Get requested page (default to start page of today's wird)
        $currentPage = $request->query('page', $startPage);
        
        // Ensure current page is within today's wird range
        $currentPage = max($startPage, min($endPage, $currentPage));
        
        // Get verses for the current page
        $verses = Verse::where('page_number', $currentPage)
            ->with(['chapter', 'recitations.reciter', 'words'])
            ->orderBy('chapter_id')
            ->orderBy('verse_number')
            ->get()
            ->map(function ($verse) {
                // Ensure recitations have full_audio_url
                $verse->recitations->each(function ($recitation) {
                    if ($recitation->audio_url && !str_starts_with($recitation->audio_url, 'http')) {
                        $recitation->full_audio_url = 'https://verses.quran.foundation/' . ltrim($recitation->audio_url, '/');
                    } else {
                        $recitation->full_audio_url = $recitation->audio_url;
                    }
                });
                return $verse;
            });

        // Get current chapter (first verse's chapter)
        $chapter = $verses->first()?->chapter;

        // Get reciters
        $reciters = Reciter::orderBy('reciter_name')->get();

        // Get tafsirs
        $tafsirs = Tafsir::orderBy('name')->get();

        // Get today's progress if exists
        $todayProgress = $plan->todayProgress();

        return Inertia::render('Reading/Experience', [
            'plan' => [
                'data' => [
                    'plan' => $this->formatPlan($plan),
                    'daily_wird' => $dailyWird,
                ],
            ],
            'verses' => $verses,
            'chapter' => $chapter,
            'reciters' => $reciters,
            'tafsirs' => $tafsirs,
            'currentPage' => (int) $currentPage,
            'startPage' => $startPage,
            'endPage' => $endPage,
            'totalPages' => 604,
            'todayProgress' => $todayProgress,
        ]);
    }

    /**
     * Plan settings page
     */
    public function settings(int $planId): Response
    {
        $user = Auth::user();
        $plan = ReadingPlan::where('id', $planId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $reciters = Reciter::orderBy('reciter_name')->get();

        return Inertia::render('Reading/Settings', [
            'plan' => $this->formatPlan($plan),
            'reciters' => $reciters,
        ]);
    }

    /**
     * Statistics page
     */
    public function statistics(): Response
    {
        $user = Auth::user();
        $statistics = $this->readingPlanService->getStatistics($user);

        return Inertia::render('Reading/Statistics', [
            'statistics' => $statistics->getData(true),
        ]);
    }

    /**
     * Update plan settings (AJAX endpoint)
     */
    public function updateSettings(Request $request, int $planId)
    {
        $user = Auth::user();
        $plan = ReadingPlan::where('id', $planId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $validated = $request->validate([
            'theme' => 'nullable|in:classic,night,soft_blue,mint',
            'font_size' => 'nullable|integer|min:14|max:48',
            'reciter_id' => 'nullable|integer|exists:reciters,id',
            'auto_scroll' => 'nullable|boolean',
            'show_translation' => 'nullable|boolean',
            'haptic_feedback' => 'nullable|boolean',
            'view_mode' => 'nullable|in:page,verse',
            'script_type' => 'nullable|in:uthmani,imlaei',
            'reading_mode' => 'nullable|in:hadr,tadabbur',
        ]);

        // Separate reading_mode from settings
        $readingMode = $validated['reading_mode'] ?? null;
        unset($validated['reading_mode']);

        // Update settings JSON field
        $currentSettings = $plan->settings ?? [];
        $newSettings = array_merge($currentSettings, $validated);
        
        $updateData = ['settings' => $newSettings];
        
        // Update reading_mode if provided
        if ($readingMode !== null) {
            $updateData['reading_mode'] = $readingMode;
        }
        
        $plan->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'تم حفظ الإعدادات',
            'settings' => $plan->refresh()->settings,
            'reading_mode' => $plan->reading_mode,
        ]);
    }

    /**
     * Mark reading progress (AJAX endpoint)
     */
    public function markProgress(Request $request, int $planId)
    {
        $user = Auth::user();
        $plan = ReadingPlan::where('id', $planId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $validated = $request->validate([
            'start_page' => 'nullable|integer|min:1|max:604',
            'end_page' => 'nullable|integer|min:1|max:604|gte:start_page',
            'reading_mode' => 'nullable|in:hadr,tadabbur',
            'duration_minutes' => 'nullable|integer|min:1|max:1440',
            'notes' => 'nullable|string|max:2000',
        ]);

        return $this->readingPlanService->markProgress($planId, $validated);
    }

    /**
     * Pause plan (AJAX endpoint)
     */
    public function pausePlan(int $planId)
    {
        return $this->readingPlanService->pausePlan($planId);
    }

    /**
     * Resume plan (AJAX endpoint)
     */
    public function resumePlan(int $planId)
    {
        return $this->readingPlanService->resumePlan($planId);
    }

    /**
     * Auto-adjust plan (AJAX endpoint)
     */
    public function autoAdjust(int $planId)
    {
        return $this->readingPlanService->autoAdjustPlan($planId);
    }

    /**
     * Delete plan (AJAX endpoint)
     */
    public function deletePlan(int $planId)
    {
        $user = Auth::user();
        $plan = ReadingPlan::where('id', $planId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $plan->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الخطة بنجاح',
        ]);
    }

    /**
     * Format plan for response
     */
    protected function formatPlan(ReadingPlan $plan): array
    {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'description' => $plan->description,
            'type' => $plan->type,
            'target_type' => $plan->target_type,
            'pages_per_day' => $plan->pages_per_day,
            'current_page' => $plan->current_page,
            'start_page' => $plan->start_page,
            'end_page' => $plan->end_page,
            'start_date' => $plan->start_date?->format('Y-m-d'),
            'end_date' => $plan->end_date?->format('Y-m-d'),
            'reading_mode' => $plan->reading_mode,
            'status' => $plan->status,
            'settings' => $plan->settings ?? ReadingPlan::DEFAULT_SETTINGS,
            'current_streak' => $plan->current_streak,
            'longest_streak' => $plan->longest_streak,
            'hatmah_count' => $plan->hatmah_count,
            'progress_percentage' => $plan->getProgressPercentage(),
            'total_pages' => $plan->getTotalPages(),
            'pages_read' => $plan->getPagesRead(),
            'remaining_pages' => $plan->getRemainingPages(),
            'days_remaining' => $plan->getDaysRemaining(),
            'has_read_today' => $plan->hasReadToday(),
        ];
    }
}
