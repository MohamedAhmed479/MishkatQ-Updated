<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Juz;
use App\Models\Verse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class JuzController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:juzs.view', only: ['index','show']),
            new ControllerMiddleware('permission:juzs.create', only: ['create','store']),
            new ControllerMiddleware('permission:juzs.edit', only: ['edit','update']),
            new ControllerMiddleware('permission:juzs.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $minVerses = (string) $request->string('min_verses');
        $maxVerses = (string) $request->string('max_verses');
        $sortBy = $request->string('sort_by', 'juz_number');
        $sortOrder = $request->string('sort_order', 'asc');

        $juzs = Juz::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('juz_number', (int) $search);
            })
            ->when($minVerses !== '', fn($q) => $q->where('verses_count', '>=', (int) $minVerses))
            ->when($maxVerses !== '', fn($q) => $q->where('verses_count', '<=', (int) $maxVerses))
            ->orderBy(in_array($sortBy, ['juz_number','verses_count']) ? $sortBy : 'juz_number', $sortOrder === 'desc' ? 'desc' : 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.juzs.index', compact('juzs', 'search', 'minVerses', 'maxVerses', 'sortBy', 'sortOrder'));
    }

    public function create(): View
    {
        return view('admin.juzs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'juz_number' => ['required', 'integer', 'min:1', 'max:30', 'unique:juzs,juz_number'],
            'start_verse_id' => ['required', 'integer', 'exists:verses,id'],
            'end_verse_id' => ['required', 'integer', 'exists:verses,id', 'gte:start_verse_id'],
            'verses_count' => ['nullable', 'integer', 'min:1'],
        ]);

        // If verses_count is not provided, try to estimate
        if (empty($data['verses_count'])) {
            $data['verses_count'] = max(1, ($data['end_verse_id'] - $data['start_verse_id'] + 1));
        }

        Juz::create($data);

        return redirect()->route('admin.juzs.index')->with('success', 'تم إنشاء الجزء بنجاح');
    }

    public function show(Juz $juz): View
    {
        $juz->load(['startVerse.chapter', 'endVerse.chapter']);
        return view('admin.juzs.show', compact('juz'));
    }

    public function edit(Juz $juz): View
    {
        return view('admin.juzs.edit', compact('juz'));
    }

    public function update(Request $request, Juz $juz): RedirectResponse
    {
        $data = $request->validate([
            'juz_number' => ['required', 'integer', 'min:1', 'max:30', 'unique:juzs,juz_number,' . $juz->id],
            'start_verse_id' => ['required', 'integer', 'exists:verses,id'],
            'end_verse_id' => ['required', 'integer', 'exists:verses,id', 'gte:start_verse_id'],
            'verses_count' => ['nullable', 'integer', 'min:1'],
        ]);

        if (empty($data['verses_count'])) {
            $data['verses_count'] = max(1, ($data['end_verse_id'] - $data['start_verse_id'] + 1));
        }

        $juz->update($data);

        return redirect()->route('admin.juzs.index')->with('success', 'تم تحديث الجزء بنجاح');
    }

    public function destroy(Juz $juz): RedirectResponse
    {
        $juzNumber = $juz->juz_number;
        $juz->delete();
        return redirect()->route('admin.juzs.index')->with('success', "تم حذف الجزء رقم {$juzNumber} بنجاح");
    }
}


