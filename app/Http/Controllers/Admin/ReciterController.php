<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reciter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class ReciterController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:reciters.view', only: ['index']),
            new ControllerMiddleware('permission:reciters.create', only: ['create','store']),
            new ControllerMiddleware('permission:reciters.edit', only: ['edit','update']),
            new ControllerMiddleware('permission:reciters.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $search = (string) $request->string('q');

        $reciters = Reciter::query()
            ->when($search !== '', fn($q) => $q->where('reciter_name', 'like', "%{$search}%"))
            ->orderBy('id', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reciters.index', compact('reciters', 'search'));
    }

    public function create(): View
    {
        return view('admin.reciters.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reciter_name' => ['required', 'string', 'max:255'],
        ]);

        Reciter::create($data);

        return redirect()->route('admin.reciters.index')->with('success', 'تم إضافة القارئ بنجاح');
    }

    public function edit(Reciter $reciter): View
    {
        return view('admin.reciters.edit', compact('reciter'));
    }

    public function update(Request $request, Reciter $reciter): RedirectResponse
    {
        $data = $request->validate([
            'reciter_name' => ['required', 'string', 'max:255'],
        ]);

        $reciter->update($data);

        return redirect()->route('admin.reciters.index')->with('success', 'تم تحديث القارئ بنجاح');
    }

    public function destroy(Reciter $reciter): RedirectResponse
    {
        if ($reciter->recitations()->exists()) {
            return back()->with('error', 'لا يمكن حذف القارئ المرتبط بتسجيلات');
        }

        $reciter->delete();

        return redirect()->route('admin.reciters.index')->with('success', 'تم حذف القارئ بنجاح');
    }
}


