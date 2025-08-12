<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $periodType = (string) $request->string('period_type');
        $date = (string) $request->string('date'); // any date within the target period

        $leaderboards = Leaderboard::query()
            ->with(['user:id,name,email'])
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($periodType !== '', function ($q) use ($periodType) {
                $q->where('period_type', $periodType);
            })
            ->when($date !== '', function ($q) use ($date, $periodType) {
                $targetDate = Carbon::parse($date);
                [$start, $end] = $this->getPeriodBounds($periodType ?: 'monthly', $targetDate);
                $q->whereDate('period_start', '>=', $start->startOfDay())
                    ->whereDate('period_end', '<=', $end->endOfDay());
            })
            ->orderBy('period_type')
            ->orderBy('period_start', 'desc')
            ->orderBy('rank')
            ->paginate(15)
            ->withQueryString();

        $periodTypes = ['daily' => 'يومي', 'weekly' => 'أسبوعي', 'monthly' => 'شهري', 'yearly' => 'سنوي'];

        return view('admin.leaderboards.index', compact('leaderboards', 'search', 'periodType', 'date', 'periodTypes'));
    }

    public function create(): View
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $periodTypes = ['daily' => 'يومي', 'weekly' => 'أسبوعي', 'monthly' => 'شهري', 'yearly' => 'سنوي'];
        return view('admin.leaderboards.create', compact('users', 'periodTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'total_points' => ['required', 'integer', 'min:0'],
            'rank' => ['required', 'integer', 'min:1'],
            'period_type' => ['required', 'in:daily,weekly,monthly,yearly'],
            'date' => ['required', 'date'], // any date in the target period
        ]);

        [$start, $end] = $this->getPeriodBounds($data['period_type'], Carbon::parse($data['date']));

        Leaderboard::create([
            'user_id' => $data['user_id'],
            'total_points' => $data['total_points'],
            'rank' => $data['rank'],
            'period_type' => $data['period_type'],
            'period_start' => $start,
            'period_end' => $end,
        ]);

        return redirect()->route('admin.leaderboards.index')->with('success', 'تم إضافة سجل المتصدرين بنجاح');
    }

    public function show(Leaderboard $leaderboard): View
    {
        $leaderboard->load('user:id,name,email');
        return view('admin.leaderboards.show', compact('leaderboard'));
    }

    public function edit(Leaderboard $leaderboard): View
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $periodTypes = ['daily' => 'يومي', 'weekly' => 'أسبوعي', 'monthly' => 'شهري', 'yearly' => 'سنوي'];
        return view('admin.leaderboards.edit', compact('leaderboard', 'users', 'periodTypes'));
    }

    public function update(Request $request, Leaderboard $leaderboard): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'total_points' => ['required', 'integer', 'min:0'],
            'rank' => ['required', 'integer', 'min:1'],
            'period_type' => ['required', 'in:daily,weekly,monthly,yearly'],
            'date' => ['required', 'date'],
        ]);

        [$start, $end] = $this->getPeriodBounds($data['period_type'], Carbon::parse($data['date']));

        $leaderboard->update([
            'user_id' => $data['user_id'],
            'total_points' => $data['total_points'],
            'rank' => $data['rank'],
            'period_type' => $data['period_type'],
            'period_start' => $start,
            'period_end' => $end,
        ]);

        return redirect()->route('admin.leaderboards.index')->with('success', 'تم تحديث سجل المتصدرين بنجاح');
    }

    public function destroy(Leaderboard $leaderboard): RedirectResponse
    {
        $leaderboard->delete();
        return redirect()->route('admin.leaderboards.index')->with('success', 'تم حذف السجل بنجاح');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:leaderboards,id'],
        ])['ids'];

        $count = Leaderboard::whereIn('id', $ids)->delete();
        return back()->with('success', "تم حذف {$count} سجل من لوحة المتصدرين");
    }

    public function recalculate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'period_type' => ['required', 'in:daily,weekly,monthly,yearly'],
            'date' => ['required', 'date'],
        ]);

        [$start, $end] = $this->getPeriodBounds($data['period_type'], Carbon::parse($data['date']));

        $entries = Leaderboard::where('period_type', $data['period_type'])
            ->whereDate('period_start', '>=', $start->startOfDay())
            ->whereDate('period_end', '<=', $end->endOfDay())
            ->orderByDesc('total_points')
            ->orderBy('user_id')
            ->get();

        $rank = 1;
        foreach ($entries as $entry) {
            $entry->update(['rank' => $rank++]);
        }

        return back()->with('success', 'تم إعادة احتساب الرتب بنجاح');
    }

    private function getPeriodBounds(string $periodType, Carbon $reference): array
    {
        return match ($periodType) {
            'daily' => [$reference->copy()->startOfDay(), $reference->copy()->endOfDay()],
            'weekly' => [$reference->copy()->startOfWeek(), $reference->copy()->endOfWeek()],
            'yearly' => [$reference->copy()->startOfYear(), $reference->copy()->endOfYear()],
            default => [$reference->copy()->startOfMonth(), $reference->copy()->endOfMonth()],
        };
    }
}


