<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewRecord;
use App\Models\SpacedRepetition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class ReviewRecordController extends Controller implements HasMiddleware
{
	public static function middleware(): array
	{
		return [
			'auth:admin',
			new ControllerMiddleware('permission:review-records.view', only: ['index','show']),
			new ControllerMiddleware('permission:review-records.create', only: ['create','store']),
			new ControllerMiddleware('permission:review-records.edit', only: ['edit','update']),
			new ControllerMiddleware('permission:review-records.delete', only: ['destroy']),
		];
	}

	public function index(Request $request): View
	{
		$revisionId = (int) $request->integer('spaced_repetition_id');
		$rating = $request->string('performance_rating');
		$success = $request->string('successful');
		$date = $request->string('review_date');

		$records = ReviewRecord::query()
			->with(['spacedRepetition.planItem.memorizationPlan.user'])
			->when($revisionId > 0, fn($q) => $q->where('spaced_repetition_id', $revisionId))
			->when($rating !== '', fn($q) => $q->where('performance_rating', $rating))
			->when($success !== '', fn($q) => $q->where('successful', $success === '1' || $success === 'true'))
			->when($date !== '', fn($q) => $q->whereDate('review_date', $date))
			->latest('id')
			->paginate(20)
			->withQueryString();

		$revisions = SpacedRepetition::latest('id')->get(['id']);
		$total = ReviewRecord::count();
		$successCount = ReviewRecord::where('successful', true)->count();
		$today = ReviewRecord::whereDate('review_date', now()->toDateString())->count();

		return view('admin.review-records.index', compact('records','revisionId','rating','success','date','revisions','total','successCount','today'));
	}

	public function create(Request $request): View
	{
		$defaultRevisionId = (int) $request->integer('spaced_repetition_id');
		$revisions = SpacedRepetition::latest('id')->get(['id']);
		return view('admin.review-records.create', compact('revisions','defaultRevisionId'));
	}

	public function store(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'spaced_repetition_id' => ['required','exists:spaced_repetitions,id'],
			'review_date' => ['required','date'],
			'performance_rating' => ['required','integer','min:0','max:5'],
			'successful' => ['required','boolean'],
			'notes' => ['nullable','string','max:1000']
		]);

		ReviewRecord::create($data);

		return redirect()->route('admin.review-records.index', ['spaced_repetition_id' => $data['spaced_repetition_id']])
			->with('success', 'تم إنشاء سجل تقييم المراجعة بنجاح');
	}

	public function show(ReviewRecord $review_record): View
	{
		$review_record->load(['spacedRepetition.planItem.memorizationPlan.user']);
		return view('admin.review-records.show', compact('review_record'));
	}

	public function edit(ReviewRecord $review_record): View
	{
		$revisions = SpacedRepetition::latest('id')->get(['id']);
		return view('admin.review-records.edit', compact('review_record','revisions'));
	}

	public function update(Request $request, ReviewRecord $review_record): RedirectResponse
	{
		$data = $request->validate([
			'spaced_repetition_id' => ['required','exists:spaced_repetitions,id'],
			'review_date' => ['required','date'],
			'performance_rating' => ['required','integer','min:0','max:5'],
			'successful' => ['required','boolean'],
			'notes' => ['nullable','string','max:1000']
		]);

		$review_record->update($data);

		return redirect()->route('admin.review-records.show', $review_record)
			->with('success', 'تم تحديث سجل التقييم بنجاح');
	}

	public function destroy(ReviewRecord $review_record): RedirectResponse
	{
		$revisionId = $review_record->spaced_repetition_id;
		$review_record->delete();
		return redirect()->route('admin.review-records.index', ['spaced_repetition_id' => $revisionId])
			->with('success', 'تم حذف سجل التقييم بنجاح');
	}
}


