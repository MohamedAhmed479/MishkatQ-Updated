<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Verse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerseController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $chapterId = (string) $request->string('chapter_id');
        $juz = (string) $request->string('juz_number');
        $page = (string) $request->string('page_number');
        $sortBy = $request->string('sort_by', 'id');
        $sortOrder = $request->string('sort_order', 'asc');

        $verses = Verse::query()
            ->with('chapter')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('text_uthmani', 'like', "%{$search}%")
                      ->orWhere('text_imlaei', 'like', "%{$search}%")
                      ->orWhere('verse_key', 'like', "%{$search}%");
                });
            })
            ->when($chapterId !== '', fn($q) => $q->where('chapter_id', $chapterId))
            ->when($juz !== '', fn($q) => $q->where('juz_number', (int) $juz))
            ->when($page !== '', fn($q) => $q->where('page_number', (int) $page))
            ->orderBy(in_array($sortBy, ['id','chapter_id','verse_number','page_number','juz_number']) ? $sortBy : 'id', $sortOrder === 'desc' ? 'desc' : 'asc')
            ->paginate(20)
            ->withQueryString();

        $chapters = Chapter::select('id','name_ar')->orderBy('id')->get();

        return view('admin.verses.index', compact('verses', 'chapters', 'search', 'chapterId', 'juz', 'page', 'sortBy', 'sortOrder'));
    }

    public function create(): View
    {
        $chapters = Chapter::select('id','name_ar')->orderBy('id')->get();
        return view('admin.verses.create', compact('chapters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Verse::create($data);
        return redirect()->route('admin.verses.index')->with('success', 'تم إنشاء الآية بنجاح');
    }

    public function show(Verse $verse): View
    {
        $verse->load('chapter');
        return view('admin.verses.show', compact('verse'));
    }

    public function edit(Verse $verse): View
    {
        $chapters = Chapter::select('id','name_ar')->orderBy('id')->get();
        return view('admin.verses.edit', compact('verse', 'chapters'));
    }

    public function update(Request $request, Verse $verse): RedirectResponse
    {
        $data = $this->validateData($request, updating: true);
        $verse->update($data);
        return redirect()->route('admin.verses.index')->with('success', 'تم تحديث الآية بنجاح');
    }

    public function destroy(Verse $verse): RedirectResponse
    {
        $verse->delete();
        return redirect()->route('admin.verses.index')->with('success', 'تم حذف الآية بنجاح');
    }

    protected function validateData(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'chapter_id' => ['required', 'exists:chapters,id'],
            'verse_number' => ['required', 'integer', 'min:1'],
            'verse_key' => ['required', 'string', 'max:255'],
            'text_uthmani' => ['required', 'string'],
            'text_imlaei' => ['nullable', 'string'],
            'page_number' => ['required', 'integer', 'min:1'],
            'juz_number' => ['required', 'integer', 'min:1', 'max:30'],
            'hizb_number' => ['nullable', 'integer', 'min:0'],
            'rub_number' => ['nullable', 'integer', 'min:0'],
            'sajda' => ['nullable', 'boolean'],
        ]);
    }
}


