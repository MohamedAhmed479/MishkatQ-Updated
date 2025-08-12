<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tafsir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class TafsirController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:tafsirs.view', only: ['index']),
            new ControllerMiddleware('permission:tafsirs.create', only: ['create','store']),
            new ControllerMiddleware('permission:tafsirs.edit', only: ['edit','update']),
            new ControllerMiddleware('permission:tafsirs.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $search = (string) $request->string('q');

        $tafsirs = Tafsir::query()
            ->when($search !== '', fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('id', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.tafsirs.index', compact('tafsirs', 'search'));
    }

    public function create(): View
    {
        return view('admin.tafsirs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:tafsirs,name'],
        ]);

        Tafsir::create($data);

        return redirect()->route('admin.tafsirs.index')->with('success', 'تم إنشاء التفسير بنجاح');
    }

    public function edit(Tafsir $tafsir): View
    {
        return view('admin.tafsirs.edit', compact('tafsir'));
    }

    public function update(Request $request, Tafsir $tafsir): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:tafsirs,name,' . $tafsir->id],
        ]);

        $tafsir->update($data);

        return redirect()->route('admin.tafsirs.index')->with('success', 'تم تحديث التفسير بنجاح');
    }

    public function destroy(Tafsir $tafsir): RedirectResponse
    {
        // Optional: prevent deletion if linked to preferences
        if ($tafsir->preferences()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذا التفسير لأنه مرتبط بتفضيلات مستخدمين');
        }

        $tafsir->delete();

        return redirect()->route('admin.tafsirs.index')->with('success', 'تم حذف التفسير بنجاح');
    }
}


