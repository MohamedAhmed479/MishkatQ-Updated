<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function __construct(
        private UserPreferenceService $preferenceService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $preferences = $user->preference;

        // Get all available tafsirs
        $tafsirs = \App\Models\Tafsir::orderBy('id')->get(['id', 'name']);

        return Inertia::render('Settings/Index', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name ?? '',
                'email' => $user->email ?? '',
                'created_at' => $user->created_at?->format('Y-m-d') ?? '',
            ],
            'preferences' => $preferences ? [
                'hifz_level' => $preferences->current_level ?? 'beginner',
                'daily_time_minutes' => $preferences->daily_minutes ?? 30,
                'notification_enabled' => true, // Not in database yet
                'tafsir_id' => $preferences->tafsir_id ?? 1,
            ] : [
                'hifz_level' => 'beginner',
                'daily_time_minutes' => 30,
                'notification_enabled' => true,
                'tafsir_id' => 1,
            ],
            'tafsirs' => $tafsirs->map(fn ($tafsir) => [
                'id' => $tafsir->id,
                'name' => $tafsir->name,
            ]),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'hifz_level' => 'nullable|in:beginner,intermediate,advanced',
            'daily_time_minutes' => 'nullable|integer|min:5|max:300',
            'notification_enabled' => 'nullable|boolean',
            'tafsir_id' => 'nullable|integer|exists:tafsirs,id',
        ]);

        try {
            // Update user name if provided
            if (isset($validated['name'])) {
                $user->name = $validated['name'];
                $user->save();
            }

            // Get or create preferences
            $preferences = $user->preference;
            if (!$preferences) {
                $preferences = $user->preference()->create([
                    'current_level' => $validated['hifz_level'] ?? 'beginner',
                    'daily_minutes' => $validated['daily_time_minutes'] ?? 30,
                    'sessions_per_day' => 1,
                    'preferred_times' => json_encode([]),
                ]);
            } else {
                if (isset($validated['hifz_level'])) {
                    $preferences->current_level = $validated['hifz_level'];
                }
                if (isset($validated['daily_time_minutes'])) {
                    $preferences->daily_minutes = $validated['daily_time_minutes'];
                }
                if (isset($validated['tafsir_id'])) {
                    $preferences->tafsir_id = $validated['tafsir_id'];
                }
                $preferences->save();
            }

            return redirect()->back()->with('success', 'تم حفظ التغييرات بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ التغييرات');
        }
    }
}
