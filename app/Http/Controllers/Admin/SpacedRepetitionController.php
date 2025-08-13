<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpacedRepetition;
use App\Models\PlanItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class SpacedRepetitionController extends Controller implements HasMiddleware
{
	public static function middleware(): array
	{
		return [
			'auth:admin',
			new ControllerMiddleware('permission:spaced-repetitions.view', only: ['index','show']),
			new ControllerMiddleware('permission:spaced-repetitions.create', only: ['create','store']),
			new ControllerMiddleware('permission:spaced-repetitions.edit', only: ['edit','update']),
			new ControllerMiddleware('permission:spaced-repetitions.delete', only: ['destroy']),
		];
	}

	public function index(Request $request): View
	{
		$planItemId = (int) $request->integer('plan_item_id');
		$userId = (int) $request->integer('user_id');
		$status = (string) $request->string('status'); // scheduled|completed|overdue
		$scheduledDate = (string) $request->string('scheduled_date');

		$revisions = SpacedRepetition::query()
			->with(['planItem.memorizationPlan.user','planItem.quranSurah'])
			->when($planItemId > 0, fn($q) => $q->where('plan_item_id', $planItemId))
			->when($userId > 0, function ($q) use ($userId) {
				$q->whereHas('planItem.memorizationPlan', fn($qq) => $qq->where('user_id', $userId));
			})
			->when($scheduledDate !== '', fn($q) => $q->whereDate('scheduled_date', $scheduledDate))
			->when($status !== '', function ($q) use ($status) {
				if ($status === 'completed') {
					$q->whereNotNull('last_reviewed_at');
				} elseif ($status === 'overdue') {
					$q->whereDate('scheduled_date', '<', now()->toDateString())->whereNull('last_reviewed_at');
				} elseif ($status === 'scheduled') {
					$q->whereNull('last_reviewed_at');
				}
			})
			->orderBy('scheduled_date')
			->paginate(20)
			->withQueryString();

		$planItems = PlanItem::with(['memorizationPlan.user'])->latest('id')->get(['id','plan_id']);
		$users = User::orderBy('name')->get(['id','name']);

		$total = SpacedRepetition::count();
		$completed = SpacedRepetition::whereNotNull('last_reviewed_at')->count();
		$today = SpacedRepetition::whereDate('scheduled_date', now()->toDateString())->count();
		$overdue = SpacedRepetition::whereDate('scheduled_date', '<', now()->toDateString())->whereNull('last_reviewed_at')->count();

		return view('admin.spaced-repetitions.index', compact(
			'revisions','planItemId','planItems','userId','users','status','scheduledDate',
			'total','completed','today','overdue'
		));
	}

	public function create(Request $request): View
	{
		$defaultPlanItemId = (int) $request->integer('plan_item_id');
		$planItems = PlanItem::with(['memorizationPlan.user'])->latest('id')->get(['id','plan_id']);
		return view('admin.spaced-repetitions.create', compact('planItems','defaultPlanItemId'));
	}

	public function store(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'plan_item_id' => ['required','exists:plan_items,id'],
			'interval_index' => ['required','integer','min:1'],
			'scheduled_date' => ['required','date'],
			'ease_factor' => ['nullable','numeric','min:1'],
			'repetition_count' => ['nullable','integer','min:0'],
		]);

		SpacedRepetition::create([
			'plan_item_id' => $data['plan_item_id'],
			'interval_index' => $data['interval_index'],
			'scheduled_date' => $data['scheduled_date'],
			'ease_factor' => $data['ease_factor'] ?? 2.5,
			'repetition_count' => $data['repetition_count'] ?? 0,
			'last_reviewed_at' => null,
		]);

		return redirect()->route('admin.spaced-repetitions.index', ['plan_item_id' => $data['plan_item_id']])
			->with('success', 'تم إنشاء مراجعة مجدولة بنجاح');
	}

	public function show(SpacedRepetition $spaced_repetition): View
	{
		$spaced_repetition->load(['planItem.memorizationPlan.user','planItem.quranSurah','reviewRecord']);
		return view('admin.spaced-repetitions.show', compact('spaced_repetition'));
	}

	public function edit(SpacedRepetition $spaced_repetition): View
	{
		$planItems = PlanItem::with(['memorizationPlan.user'])->latest('id')->get(['id','plan_id']);
		return view('admin.spaced-repetitions.edit', compact('spaced_repetition','planItems'));
	}

	public function update(Request $request, SpacedRepetition $spaced_repetition): RedirectResponse
	{
		$data = $request->validate([
			'plan_item_id' => ['required','exists:plan_items,id'],
			'interval_index' => ['required','integer','min:1'],
			'scheduled_date' => ['required','date'],
			'ease_factor' => ['nullable','numeric','min:1'],
			'repetition_count' => ['nullable','integer','min:0'],
			'last_reviewed_at' => ['nullable','date'],
		]);

		$spaced_repetition->update($data);

		return redirect()->route('admin.spaced-repetitions.show', $spaced_repetition)
			->with('success', 'تم تحديث بيانات المراجعة بنجاح');
	}

	public function destroy(SpacedRepetition $spaced_repetition): RedirectResponse
	{
		$planItemId = $spaced_repetition->plan_item_id;
		$spaced_repetition->delete();
		return redirect()->route('admin.spaced-repetitions.index', ['plan_item_id' => $planItemId])
			->with('success', 'تم حذف المراجعة بنجاح');
	}
}


