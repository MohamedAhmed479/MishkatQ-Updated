<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\MemorizationPlan;
use App\Models\PlanItem;
use App\Models\Verse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class PlanItemController extends Controller implements HasMiddleware
{
	public static function middleware(): array
	{
		return [
			'auth:admin',
			new ControllerMiddleware('permission:plan-items.view', only: ['index','show']),
			new ControllerMiddleware('permission:plan-items.create', only: ['create','store']),
			new ControllerMiddleware('permission:plan-items.edit', only: ['edit','update']),
			new ControllerMiddleware('permission:plan-items.delete', only: ['destroy']),
		];
	}

	public function index(Request $request): View
	{
		$planId = (int) $request->integer('plan_id');
		$surahId = (int) $request->integer('quran_surah_id');
		$isCompleted = $request->string('is_completed');
		$date = (string) $request->string('target_date');

		$items = PlanItem::query()
			->with(['memorizationPlan.user', 'quranSurah', 'verseStart', 'verseEnd'])
			->when($planId > 0, fn($q) => $q->where('plan_id', $planId))
			->when($surahId > 0, fn($q) => $q->where('quran_surah_id', $surahId))
			->when($isCompleted !== '', function ($q) use ($isCompleted) {
				$q->where('is_completed', $isCompleted === '1' || $isCompleted === 'true');
			})
			->when($date !== '', fn($q) => $q->whereDate('target_date', $date))
			->orderBy('target_date')
			->orderBy('sequence')
			->paginate(20)
			->withQueryString();

		$plans = MemorizationPlan::with('user')->latest('id')->get(['id','name','user_id']);
		$surahs = Chapter::orderBy('id')->get(['id','name_ar']);

		$totalItems = PlanItem::count();
		$completedItems = PlanItem::where('is_completed', true)->count();
		$todayItems = PlanItem::whereDate('target_date', now()->toDateString())->count();
		$upcomingItems = PlanItem::whereDate('target_date', '>', now()->toDateString())->count();

		return view('admin.plan-items.index', compact(
			'items','planId','plans','surahId','surahs','isCompleted','date',
			'totalItems','completedItems','todayItems','upcomingItems'
		));
	}

	public function create(Request $request): View
	{
		$defaultPlanId = (int) $request->integer('plan_id');
		$plans = MemorizationPlan::with('user')->latest('id')->get(['id','name','user_id']);
		$surahs = Chapter::orderBy('id')->get(['id','name_ar']);

		return view('admin.plan-items.create', compact('plans','surahs','defaultPlanId'));
	}

	public function store(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'plan_id' => ['required','exists:memorization_plans,id'],
			'quran_surah_id' => ['required','exists:chapters,id'],
			'verse_start_id' => ['required','exists:verses,id'],
			'verse_end_id' => ['required','exists:verses,id','gte:verse_start_id'],
			'target_date' => ['required','date'],
			'sequence' => ['required','integer','min:1'],
			'is_completed' => ['sometimes','boolean'],
		]);

		// Optional: validate start/end verses belong to selected surah
		$start = Verse::find($data['verse_start_id']);
		$end = Verse::find($data['verse_end_id']);
		if ($start && $end && ($start->chapter_id !== (int) $data['quran_surah_id'] || $end->chapter_id !== (int) $data['quran_surah_id'])) {
			return back()->withInput()->with('error', 'الآيات المختارة لا تنتمي إلى السورة المحددة');
		}

		PlanItem::create([
			'plan_id' => $data['plan_id'],
			'quran_surah_id' => $data['quran_surah_id'],
			'verse_start_id' => $data['verse_start_id'],
			'verse_end_id' => $data['verse_end_id'],
			'target_date' => $data['target_date'],
			'sequence' => $data['sequence'],
			'is_completed' => (bool) ($data['is_completed'] ?? false),
		]);

		return redirect()
			->route('admin.plan-items.index', ['plan_id' => $data['plan_id']])
			->with('success', 'تم إنشاء عنصر الخطة بنجاح');
	}

	public function show(PlanItem $plan_item): View
	{
		$plan_item->load(['memorizationPlan.user','quranSurah','verseStart','verseEnd']);
		return view('admin.plan-items.show', compact('plan_item'));
	}

	public function edit(PlanItem $plan_item): View
	{
		$plans = MemorizationPlan::with('user')->latest('id')->get(['id','name','user_id']);
		$surahs = Chapter::orderBy('id')->get(['id','name_ar']);
		return view('admin.plan-items.edit', compact('plan_item','plans','surahs'));
	}

	public function update(Request $request, PlanItem $plan_item): RedirectResponse
	{
		$data = $request->validate([
			'plan_id' => ['required','exists:memorization_plans,id'],
			'quran_surah_id' => ['required','exists:chapters,id'],
			'verse_start_id' => ['required','exists:verses,id'],
			'verse_end_id' => ['required','exists:verses,id','gte:verse_start_id'],
			'target_date' => ['required','date'],
			'sequence' => ['required','integer','min:1'],
			'is_completed' => ['sometimes','boolean'],
		]);

		$start = Verse::find($data['verse_start_id']);
		$end = Verse::find($data['verse_end_id']);
		if ($start && $end && ($start->chapter_id !== (int) $data['quran_surah_id'] || $end->chapter_id !== (int) $data['quran_surah_id'])) {
			return back()->withInput()->with('error', 'الآيات المختارة لا تنتمي إلى السورة المحددة');
		}

		$plan_item->update([
			'plan_id' => $data['plan_id'],
			'quran_surah_id' => $data['quran_surah_id'],
			'verse_start_id' => $data['verse_start_id'],
			'verse_end_id' => $data['verse_end_id'],
			'target_date' => $data['target_date'],
			'sequence' => $data['sequence'],
			'is_completed' => (bool) ($data['is_completed'] ?? false),
		]);

		return redirect()
			->route('admin.plan-items.show', $plan_item)
			->with('success', 'تم تحديث عنصر الخطة بنجاح');
	}

	public function destroy(PlanItem $plan_item): RedirectResponse
	{
		$planId = $plan_item->plan_id;
		$plan_item->delete();
		return redirect()->route('admin.plan-items.index', ['plan_id' => $planId])
			->with('success', 'تم حذف عنصر الخطة بنجاح');
	}
}


