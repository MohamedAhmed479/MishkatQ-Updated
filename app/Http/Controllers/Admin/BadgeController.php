<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class BadgeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:badges.view', only: ['index','show','awardedUsers']),
            new ControllerMiddleware('permission:badges.create', only: ['create','store']),
            new ControllerMiddleware('permission:badges.edit', only: ['edit','update','toggleStatus']),
            new ControllerMiddleware('permission:badges.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $status = $request->string('status');

        $badges = Badge::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($status !== '', function ($q) use ($status) {
                if ($status === 'active') {
                    $q->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $q->where('is_active', false);
                }
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $badges->load('users');

        return view('admin.badges.index', compact('badges', 'search', 'status'));
    }

    public function create(): View
    {
        $criteriaTypes = [
            'verses_memorized' => 'عدد الآيات المحفوظة',
            'consecutive_days' => 'الأيام المتتالية',
            'total_points' => 'إجمالي النقاط',
            'perfect_reviews' => 'المراجعات المثالية',
        ];

        return view('admin.badges.create', compact('criteriaTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:255'],
            'points_value' => ['required', 'integer', 'min:0'],
            'criteria_type' => ['required', 'string', 'in:verses_memorized,consecutive_days,total_points,perfect_reviews'],
            'criteria_threshold' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        Badge::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'icon' => $data['icon'],
            'points_value' => $data['points_value'],
            'winning_criteria' => [
                'type' => $data['criteria_type'],
                'threshold' => $data['criteria_threshold'],
            ],
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('admin.badges.index')
            ->with('success', 'تم إنشاء الشارة بنجاح');
    }

    public function show(Badge $badge): View
    {
        $badge->load(['users' => function($query) {
            $query->withPivot('awarded_at')->latest('pivot_awarded_at')->take(10);
        }]);

        $totalAwarded = $badge->users()->count();
        $recentAwards = $badge->users()->withPivot('awarded_at')->latest('pivot_awarded_at')->take(5)->get();

        return view('admin.badges.show', compact('badge', 'totalAwarded', 'recentAwards'));
    }

    public function edit(Badge $badge): View
    {
        $criteriaTypes = [
            'verses_memorized' => 'عدد الآيات المحفوظة',
            'consecutive_days' => 'الأيام المتتالية',
            'total_points' => 'إجمالي النقاط',
            'perfect_reviews' => 'المراجعات المثالية',
        ];

        return view('admin.badges.edit', compact('badge', 'criteriaTypes'));
    }

    public function update(Request $request, Badge $badge): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:255'],
            'points_value' => ['required', 'integer', 'min:0'],
            'criteria_type' => ['required', 'string', 'in:verses_memorized,consecutive_days,total_points,perfect_reviews'],
            'criteria_threshold' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        $badge->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'icon' => $data['icon'],
            'points_value' => $data['points_value'],
            'winning_criteria' => [
                'type' => $data['criteria_type'],
                'threshold' => $data['criteria_threshold'],
            ],
            'is_active' => $data['is_active'] ?? false,
        ]);

        return redirect()->route('admin.badges.index')
            ->with('success', 'تم تحديث الشارة بنجاح');
    }

    public function destroy(Badge $badge): RedirectResponse
    {
        // Check if badge has been awarded to users
        $awardedCount = $badge->users()->count();

        if ($awardedCount > 0) {
            return back()->with('error', "لا يمكن حذف هذه الشارة لأنها تم منحها لـ {$awardedCount} مستخدم. يمكنك إلغاء تفعيلها بدلاً من ذلك.");
        }

        $badge->delete();

        return redirect()->route('admin.badges.index')
            ->with('success', 'تم حذف الشارة بنجاح');
    }

    public function toggleStatus(Badge $badge): RedirectResponse
    {
        $badge->update(['is_active' => !$badge->is_active]);

        $status = $badge->is_active ? 'تفعيل' : 'إلغاء تفعيل';

        return back()->with('success', "تم {$status} الشارة بنجاح");
    }

    public function awardedUsers(Badge $badge): View
    {
        $users = $badge->users()
            ->withPivot('awarded_at')
            ->latest('pivot_awarded_at')
            ->paginate(20);

        return view('admin.badges.awarded-users', compact('badge', 'users'));
    }
}
