<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Word;
use App\Models\Verse;
use App\Models\Chapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class WordController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:words.view', only: ['index','show']),
            new ControllerMiddleware('permission:words.create', only: ['create','store']),
            new ControllerMiddleware('permission:words.edit', only: ['edit','update']),
            new ControllerMiddleware('permission:words.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $chapterId = (string) $request->string('chapter_id');
        $verseId = (string) $request->string('verse_id');
        $sortBy = $request->string('sort_by', 'id');
        $sortOrder = $request->string('sort_order', 'asc');

        $words = Word::query()
            ->with(['verse.chapter'])
            ->when($search !== '', fn($q) => $q->where('text', 'like', "%{$search}%"))
            ->when($verseId !== '', fn($q) => $q->where('verse_id', $verseId))
            ->when($chapterId !== '', function ($q) use ($chapterId) {
                $q->whereHas('verse', fn($q2) => $q2->where('chapter_id', $chapterId));
            })
            ->orderBy(in_array($sortBy, ['id','position','verse_id']) ? $sortBy : 'id', $sortOrder === 'desc' ? 'desc' : 'asc')
            ->paginate(25)
            ->withQueryString();

        $chapters = Chapter::select('id','name_ar')->orderBy('id')->get();

        return view('admin.words.index', compact('words', 'chapters', 'search', 'chapterId', 'verseId', 'sortBy', 'sortOrder'));
    }

    public function create(): View
    {
        return view('admin.words.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Word::create($data);
        return redirect()->route('admin.words.index')->with('success', 'تم إضافة الكلمة بنجاح');
    }

    public function show(Word $word): View
    {
        $word->load('verse.chapter');
        return view('admin.words.show', compact('word'));
    }

    public function edit(Word $word): View
    {
        return view('admin.words.edit', compact('word'));
    }

    public function update(Request $request, Word $word): RedirectResponse
    {
        $data = $this->validateData($request, updating: true);
        $word->update($data);
        return redirect()->route('admin.words.index')->with('success', 'تم تحديث الكلمة بنجاح');
    }

    public function destroy(Word $word): RedirectResponse
    {
        $word->delete();
        return redirect()->route('admin.words.index')->with('success', 'تم حذف الكلمة بنجاح');
    }

    protected function validateData(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'verse_id' => ['required', 'exists:verses,id'],
            'position' => ['required', 'integer', 'min:1'],
            'text' => ['required', 'string'],
            'audio_url' => ['nullable', 'url'],
        ]);
    }
}


