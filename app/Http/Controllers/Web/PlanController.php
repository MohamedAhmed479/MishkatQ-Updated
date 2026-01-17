<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\MemorizationPlan;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use App\Services\MemorizationPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function __construct(
        private MemorizationPlanInterface $planRepository,
        private MemorizationPlanService $memorizationPlanService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $plans = $user->memorizationPlans()->with(['planItems.quranSurah'])->get();

        return Inertia::render('Plans/Index', [
            'plans' => $plans->map(function ($plan) {
                $firstItem = $plan->planItems()->orderBy('sequence')->with('quranSurah')->first();
                $lastItem = $plan->planItems()->orderBy('sequence', 'desc')->with('quranSurah')->first();
                
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'status' => $plan->status,
                    'start_chapter' => $firstItem?->quranSurah?->name_ar,
                    'end_chapter' => $lastItem?->quranSurah?->name_ar,
                    'progress' => $plan->progress_percentage ?? 0,
                    'total_items' => $plan->planItems()->count(),
                    'completed_items' => $plan->planItems()->where('is_completed', true)->count(),
                    'created_at' => $plan->created_at->format('Y-m-d'),
                ];
            }),
        ]);
    }

    public function create(Request $request)
    {
        $chapters = Chapter::orderBy('id')->get(['id', 'name_ar', 'name_en', 'verses_count']);

        return Inertia::render('Plans/Create', [
            'chapters' => $chapters->map(fn ($chapter) => [
                'id' => $chapter->id,
                'name_arabic' => $chapter->name_ar,
                'name_english' => $chapter->name_en,
                'verses_count' => $chapter->verses_count,
            ]),
        ]);
    }

    public function show(Request $request, MemorizationPlan $plan)
    {
        // Check if the plan belongs to the authenticated user
        $user = $request->user();
        if (!$user || $plan->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخطة');
        }

        $firstItem = $plan->planItems()->orderBy('sequence')->with('quranSurah')->first();
        $lastItem = $plan->planItems()->orderBy('sequence', 'desc')->with('quranSurah')->first();

        // Get paginated plan items
        $perPage = $request->input('per_page', 10);
        $itemsPaginated = $plan->planItems()
            ->with(['quranSurah', 'verseStart', 'verseEnd'])
            ->orderBy('sequence')
            ->paginate($perPage)
            ->withQueryString();

        // Get total stats
        $totalItems = $plan->planItems()->count();
        $completedItems = $plan->planItems()->where('is_completed', true)->count();

        return Inertia::render('Plans/Show', [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'status' => $plan->status,
                'start_chapter' => $firstItem?->quranSurah?->name_ar,
                'end_chapter' => $lastItem?->quranSurah?->name_ar,
                'start_date' => $plan->start_date?->format('Y-m-d'),
                'end_date' => $plan->end_date?->format('Y-m-d'),
                'progress' => $plan->progress_percentage ?? 0,
                'created_at' => $plan->created_at->format('Y-m-d'),
                'total_items' => $totalItems,
                'completed_items' => $completedItems,
            ],
            'items' => [
                'data' => $itemsPaginated->getCollection()->map(fn ($item) => [
                    'id' => $item->id,
                    'sequence' => $item->sequence,
                    'chapter_name' => $item->quranSurah?->name_ar,
                    'start_verse' => $item->verseStart?->verse_number,
                    'end_verse' => $item->verseEnd?->verse_number,
                    'scheduled_date' => $item->target_date?->format('Y-m-d'),
                    'is_completed' => $item->is_completed,
                    'completed_at' => $item->is_completed ? $item->updated_at?->format('Y-m-d') : null,
                ]),
                'current_page' => $itemsPaginated->currentPage(),
                'last_page' => $itemsPaginated->lastPage(),
                'per_page' => $itemsPaginated->perPage(),
                'total' => $itemsPaginated->total(),
                'from' => $itemsPaginated->firstItem(),
                'to' => $itemsPaginated->lastItem(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'start_chapter_id' => 'required|integer|exists:chapters,id',
            'end_chapter_id' => 'required|integer|exists:chapters,id|gte:start_chapter_id',
            'start_verse' => 'nullable|integer|min:1',
            'end_verse' => 'nullable|integer|min:1',
        ]);

        $user = $request->user();
        
        try {
            DB::beginTransaction();

            // Prepare data for MemorizationPlanService
            $planData = [
                'name' => $validated['name'] ?: 'خطة حفظ جديدة',
                'surah_start_id' => $validated['start_chapter_id'],
                'surah_end_id' => $validated['end_chapter_id'],
                'start_date' => now()->toDateString(),
                'description' => null,
            ];

            $response = $this->memorizationPlanService->makeNewMemoPlanWithHisItems($user, $planData);

            // Check if response is success (it returns JsonResponse)
            if ($response->getStatusCode() === 201) {
                DB::commit();
                return redirect()->route('user.plans')->with('success', 'تم إنشاء الخطة بنجاح!');
            } else {
                DB::rollBack();
                $responseData = json_decode($response->getContent(), true);
                $errorMessage = $responseData['message'] ?? 'حدث خطأ أثناء إنشاء الخطة';
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء إنشاء الخطة: ' . $e->getMessage()])->withInput();
        }
    }
}
