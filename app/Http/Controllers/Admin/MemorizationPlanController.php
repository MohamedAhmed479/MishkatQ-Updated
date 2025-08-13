<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemorizationPlan;
use App\Models\PlanItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class MemorizationPlanController extends Controller implements HasMiddleware
{
	public static function middleware(): array
	{
		return [
			'auth:admin',
			new ControllerMiddleware('permission:memorization-plans.view', only: ['index','show']),
			new ControllerMiddleware('permission:memorization-plans.create', only: ['create','store']),
			new ControllerMiddleware('permission:memorization-plans.edit', only: ['edit','update']),
			new ControllerMiddleware('permission:memorization-plans.delete', only: ['destroy']),
		];
	}

	public function index(Request $request): View
	{
		$search = (string) $request->string('q');
		$status = (string) $request->string('status');
		$userId = (int) $request->integer('user_id');

		$plans = MemorizationPlan::query()
			->with('user')
			->when($search !== '', function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhereHas('user', function ($uq) use ($search) {
						$uq->where('name', 'like', "%{$search}%")
							->orWhere('email', 'like', "%{$search}%");
					});
			})
			->when($status !== '', function ($q) use ($status) {
				$q->where('status', $status);
			})
			->when($userId > 0, function ($q) use ($userId) {
				$q->where('user_id', $userId);
			})
			->latest('id')
			->paginate(15)
			->withQueryString();

		$totalPlans = MemorizationPlan::count();
		$activePlans = MemorizationPlan::where('status', 'active')->count();
		$pausedPlans = MemorizationPlan::where('status', 'paused')->count();
		$completedPlans = MemorizationPlan::where('status', 'completed')->count();

		$users = User::orderBy('name')->select('id','name')->get();

		return view('admin.memorization-plans.index', compact(
			'plans', 'search', 'status', 'userId', 'users',
			'totalPlans', 'activePlans', 'pausedPlans', 'completedPlans'
		));
	}

	public function create(): View
	{
		$users = User::orderBy('name')->select('id','name')->get();
		return view('admin.memorization-plans.create', compact('users'));
	}

	public function store(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'user_id' => ['required','exists:users,id'],
			'name' => ['required','string','max:255'],
			'description' => ['nullable','string','max:2000'],
			'start_date' => ['required','date'],
			'end_date' => ['required','date','after_or_equal:start_date'],
			'status' => ['required','in:active,paused,completed'],
		]);

		$plan = MemorizationPlan::create($data);

		return redirect()
			->route('admin.memorization-plans.show', $plan)
			->with('success', 'تم إنشاء خطة الحفظ بنجاح');
	}

	public function show(MemorizationPlan $memorization_plan): View
	{
		$memorization_plan->load([
			'user',
			'planItems' => function ($q) {
				$q->with(['quranSurah','verseStart','verseEnd'])->orderBy('sequence');
			}
		]);

		$itemsCount = $memorization_plan->planItems()->count();
		$completedItems = $memorization_plan->planItems()->where('is_completed', true)->count();
		$upcomingItems = $memorization_plan->planItems()->whereDate('target_date', '>', now()->toDateString())->count();

		return view('admin.memorization-plans.show', compact(
			'memorization_plan', 'itemsCount', 'completedItems', 'upcomingItems'
		));
	}

	public function edit(MemorizationPlan $memorization_plan): View
	{
		$users = User::orderBy('name')->select('id','name')->get();
		return view('admin.memorization-plans.edit', compact('memorization_plan','users'));
	}

	public function update(Request $request, MemorizationPlan $memorization_plan): RedirectResponse
	{
		$data = $request->validate([
			'user_id' => ['required','exists:users,id'],
			'name' => ['required','string','max:255'],
			'description' => ['nullable','string','max:2000'],
			'start_date' => ['required','date'],
			'end_date' => ['required','date','after_or_equal:start_date'],
			'status' => ['required','in:active,paused,completed'],
		]);

		$memorization_plan->update($data);

		return redirect()
			->route('admin.memorization-plans.show', $memorization_plan)
			->with('success', 'تم تحديث خطة الحفظ بنجاح');
	}

	public function destroy(MemorizationPlan $memorization_plan): RedirectResponse
	{
		// If plan has items, deleting will cascade due to FK. Just proceed.
		$memorization_plan->delete();

		return redirect()
			->route('admin.memorization-plans.index')
			->with('success', 'تم حذف خطة الحفظ بنجاح');
	}
}


