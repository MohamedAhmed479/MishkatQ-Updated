<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Verse;
use App\Models\MemorizationProgress;
use App\Models\PlanItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class ChapterController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:chapters.view', only: ['index','show','verses','memorizationProgress','memorizationPlans']),
            new ControllerMiddleware('permission:chapters.create', only: ['create','store']),
            new ControllerMiddleware('permission:chapters.edit', only: ['edit','update']),
            new ControllerMiddleware('permission:chapters.delete', only: ['destroy']),
        ];
    }
    /**
     * عرض قائمة السور
     */
    public function index(Request $request): View
    {
        // Debug: Log the request parameters
        \Log::info('Chapter index request parameters:', $request->all());

        $search = (string) $request->string('q');
        $revelationPlace = (string) $request->string('revelation_place');
        $sortBy = (string) $request->string('sort_by', 'id');
        $sortOrder = (string) $request->string('sort_order', 'asc');

        // Debug: Log the extracted values
        \Log::info('Extracted filter values:', [
            'search' => $search,
            'revelationPlace' => $revelationPlace,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
        ]);

        $chapters = Chapter::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name_ar', 'like', "%{$search}%")
                      ->orWhere('name_en', 'like', "%{$search}%");
                });
            })
            ->when($revelationPlace !== '', function ($q) use ($revelationPlace) {
                $q->where('revelation_place', $revelationPlace);
            })
            ->when($sortBy === 'verses_count', function ($q) use ($sortOrder) {
                $q->orderBy('verses_count', $sortOrder);
            })
            ->when($sortBy === 'revelation_order', function ($q) use ($sortOrder) {
                $q->orderBy('revelation_order', $sortOrder);
            })
            ->when($sortBy === 'name_ar', function ($q) use ($sortOrder) {
                $q->orderBy('name_ar', $sortOrder);
            })
            ->when($sortBy === 'id', function ($q) use ($sortOrder) {
                $q->orderBy('id', $sortOrder);
            })
            ->paginate(20)
            ->withQueryString();

        // إحصائيات
        $totalChapters = Chapter::count();
        $makkahChapters = Chapter::where('revelation_place', 'makkah')->count();
        $madinahChapters = Chapter::where('revelation_place', 'madinah')->count();
        $totalVerses = Chapter::sum('verses_count');

        return view('admin.chapters.index', compact(
            'chapters', 'search', 'revelationPlace', 'sortBy', 'sortOrder',
            'totalChapters', 'makkahChapters', 'madinahChapters', 'totalVerses'
        ));
    }

    /**
     * عرض نموذج إنشاء سورة جديدة
     */
    public function create(): View
    {
        $revelationPlaces = [
            'makkah' => 'مكية',
            'madinah' => 'مدنية'
        ];

        return view('admin.chapters.create', compact('revelationPlaces'));
    }

    /**
     * حفظ سورة جديدة
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:100|unique:chapters,name_ar',
            'name_en' => 'nullable|string|max:100',
            'revelation_place' => 'required|in:makkah,madinah',
            'revelation_order' => 'nullable|integer|min:1|unique:chapters,revelation_order',
            'verses_count' => 'nullable|integer|min:1',
        ], [
            'name_ar.required' => 'اسم السورة العربية مطلوب',
            'name_ar.unique' => 'اسم السورة العربية موجود مسبقاً',
            'name_ar.max' => 'اسم السورة العربية يجب أن لا يتجاوز 100 حرف',
            'revelation_place.required' => 'مكان النزول مطلوب',
            'revelation_place.in' => 'مكان النزول يجب أن يكون مكة أو مدينة',
            'revelation_order.integer' => 'رقم النزول يجب أن يكون رقماً صحيحاً',
            'revelation_order.min' => 'رقم النزول يجب أن يكون أكبر من صفر',
            'revelation_order.unique' => 'رقم النزول موجود مسبقاً',
            'verses_count.integer' => 'عدد الآيات يجب أن يكون رقماً صحيحاً',
            'verses_count.min' => 'عدد الآيات يجب أن يكون أكبر من صفر',
        ]);

        Chapter::create($validated);

        return redirect()
            ->route('admin.chapters.index')
            ->with('success', 'تم إنشاء السورة بنجاح');
    }

    /**
     * عرض تفاصيل السورة
     */
    public function show(Chapter $chapter): View
    {
        // تحميل العلاقات
        $chapter->load(['verses' => function ($query) {
            $query->orderBy('verse_number');
        }]);

        // إحصائيات
        $actualVersesCount = $chapter->verses->count();
        $memorizationProgressCount = MemorizationProgress::where('chapter_id', $chapter->id)->count();
        $planItemsCount = PlanItem::where('quran_surah_id', $chapter->id)->count();

        // آخر 10 آيات
        $recentVerses = $chapter->verses()
            ->orderBy('verse_number', 'desc')
            ->limit(10)
            ->get();

        return view('admin.chapters.show', compact(
            'chapter', 'actualVersesCount', 'memorizationProgressCount',
            'planItemsCount', 'recentVerses'
        ));
    }

    /**
     * عرض نموذج تعديل السورة
     */
    public function edit(Chapter $chapter): View
    {
        $revelationPlaces = [
            'makkah' => 'مكية',
            'madinah' => 'مدنية'
        ];

        return view('admin.chapters.edit', compact('chapter', 'revelationPlaces'));
    }

    /**
     * تحديث السورة
     */
    public function update(Request $request, Chapter $chapter): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:100|unique:chapters,name_ar,' . $chapter->id,
            'name_en' => 'nullable|string|max:100',
            'revelation_place' => 'required|in:makkah,madinah',
            'revelation_order' => 'nullable|integer|min:1|unique:chapters,revelation_order,' . $chapter->id,
            'verses_count' => 'nullable|integer|min:1',
        ], [
            'name_ar.required' => 'اسم السورة العربية مطلوب',
            'name_ar.unique' => 'اسم السورة العربية موجود مسبقاً',
            'name_ar.max' => 'اسم السورة العربية يجب أن لا يتجاوز 100 حرف',
            'revelation_place.required' => 'مكان النزول مطلوب',
            'revelation_place.in' => 'مكان النزول يجب أن يكون مكة أو مدينة',
            'revelation_order.integer' => 'رقم النزول يجب أن يكون رقماً صحيحاً',
            'revelation_order.min' => 'رقم النزول يجب أن يكون أكبر من صفر',
            'revelation_order.unique' => 'رقم النزول موجود مسبقاً',
            'verses_count.integer' => 'عدد الآيات يجب أن يكون رقماً صحيحاً',
            'verses_count.min' => 'عدد الآيات يجب أن يكون أكبر من صفر',
        ]);

        $chapter->update($validated);

        return redirect()
            ->route('admin.chapters.show', $chapter)
            ->with('success', 'تم تحديث السورة بنجاح');
    }

    /**
     * حذف السورة
     */
    public function destroy(Chapter $chapter): RedirectResponse
    {
        // التحقق من عدم استخدام السورة
        $versesCount = $chapter->verses()->count();
        $memorizationProgressCount = MemorizationProgress::where('chapter_id', $chapter->id)->count();
        $planItemsCount = PlanItem::where('quran_surah_id', $chapter->id)->count();

        if ($versesCount > 0 || $memorizationProgressCount > 0 || $planItemsCount > 0) {
            return redirect()
                ->route('admin.chapters.show', $chapter)
                ->with('error', 'لا يمكن حذف السورة لأنها مرتبطة ببيانات أخرى');
        }

        $chapter->delete();

        return redirect()
            ->route('admin.chapters.index')
            ->with('success', 'تم حذف السورة بنجاح');
    }

    /**
     * عرض آيات السورة
     */
    public function verses(Chapter $chapter): View
    {
        $verses = $chapter->verses()
            ->orderBy('verse_number')
            ->paginate(50);

        return view('admin.chapters.verses', compact('chapter', 'verses'));
    }

    /**
     * عرض تقدم الحفظ للسورة
     */
    public function memorizationProgress(Chapter $chapter): View
    {
        $progress = MemorizationProgress::where('chapter_id', $chapter->id)
            ->with('user')
            ->paginate(20);

        return view('admin.chapters.memorization-progress', compact('chapter', 'progress'));
    }

    /**
     * عرض خطط الحفظ التي تستخدم السورة
     */
    public function memorizationPlans(Chapter $chapter): View
    {
        $planItems = PlanItem::where('quran_surah_id', $chapter->id)
            ->with(['memorizationPlan.user'])
            ->paginate(20);

        return view('admin.chapters.memorization-plans', compact('chapter', 'planItems'));
    }
}
