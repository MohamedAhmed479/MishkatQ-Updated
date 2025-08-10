<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tafsir;
use App\Models\UserProfile;
use App\Models\UserPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $users = User::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        $tafsirs = Tafsir::all();
        return view('admin.users.create', compact('tafsirs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            // UserProfile fields
            'username' => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'string'],
            'total_points' => ['nullable', 'integer', 'min:0'],
            'verses_memorized_count' => ['nullable', 'integer', 'min:0'],
            // UserPreference fields
            'tafsir_id' => ['nullable', 'exists:tafsirs,id'],
            'daily_minutes' => ['required', 'integer', 'min:0'],
            'sessions_per_day' => ['required', 'integer', 'min:1'],
            'current_level' => ['required', 'in:beginner,intermediate,advanced'],
        ]);

        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        // Create user profile
        UserProfile::create([
            'user_id' => $user->id,
            'username' => $data['username'],
            'profile_image' => $data['profile_image'] ?? null,
            'total_points' => $data['total_points'] ?? 0,
            'verses_memorized_count' => $data['verses_memorized_count'] ?? 0,
        ]);

        // Create user preferences
        UserPreference::create([
            'user_id' => $user->id,
            'tafsir_id' => $data['tafsir_id'] ?? null,
            'daily_minutes' => $data['daily_minutes'],
            'sessions_per_day' => $data['sessions_per_day'],
            'preferred_times' => json_encode($data['preferred_times'] ?? []),
            'current_level' => $data['current_level'],
        ]);

        return redirect()->route('admin.users.index', $user)
            ->with('success', 'تم إنشاء المستخدم بنجاح مع جميع البيانات الإضافية');
    }

    public function edit(User $user): View
    {
        $tafsirs = Tafsir::all();
        return view('admin.users.edit', compact('user', 'tafsirs'));
    }

    public function show(User $user): View
    {
        $user->load(['profile', 'preference.tafsir', 'memorizationPlans', 'memorizationProgress']);
        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8'],
            // UserProfile fields
            'username' => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'string'],
            'total_points' => ['nullable', 'integer', 'min:0'],
            'verses_memorized_count' => ['nullable', 'integer', 'min:0'],
            // UserPreference fields
            'tafsir_id' => ['nullable', 'exists:tafsirs,id'],
            'daily_minutes' => ['required', 'integer', 'min:0'],
            'sessions_per_day' => ['required', 'integer', 'min:1'],
            'current_level' => ['required', 'in:beginner,intermediate,advanced'],
        ]);

        // Update user basic data
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $userData['password'] = $data['password'];
        }

        $user->update($userData);

        // Update or create user profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'username' => $data['username'],
                'profile_image' => $data['profile_image'] ?? null,
                'total_points' => $data['total_points'] ?? 0,
                'verses_memorized_count' => $data['verses_memorized_count'] ?? 0,
            ]
        );

        // Update or create user preferences
        $user->preference()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'tafsir_id' => $data['tafsir_id'] ?? null,
                'daily_minutes' => $data['daily_minutes'],
                'sessions_per_day' => $data['sessions_per_day'],
                'preferred_times' => json_encode([]),
                'current_level' => $data['current_level'],
            ]
        );

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات المستخدم وجميع المعلومات الإضافية');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم');
    }
}


