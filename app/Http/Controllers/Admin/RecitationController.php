<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recitation;
use App\Models\Reciter;
use App\Models\Chapter;
use App\Models\Verse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecitationController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $reciterId = (string) $request->string('reciter_id');
        $chapterId = (string) $request->string('chapter_id');
        $verseId = (string) $request->string('verse_id');
        $sortBy = $request->string('sort_by', 'id');
        $sortOrder = $request->string('sort_order', 'asc');

        $recitations = Recitation::query()
            ->with(['reciter', 'verse.chapter'])
            ->when($search !== '', fn($q) => $q->where('audio_url', 'like', "%{$search}%"))
            ->when($reciterId !== '', fn($q) => $q->where('reciter_id', $reciterId))
            ->when($verseId !== '', fn($q) => $q->where('verse_id', $verseId))
            ->when($chapterId !== '', function ($q) use ($chapterId) {
                $q->whereHas('verse', fn($q2) => $q2->where('chapter_id', $chapterId));
            })
            ->orderBy(in_array($sortBy, ['id','reciter_id','verse_id']) ? $sortBy : 'id', $sortOrder === 'desc' ? 'desc' : 'asc')
            ->paginate(20)
            ->withQueryString();

        $reciters = Reciter::select('id','reciter_name')->orderBy('reciter_name')->get();
        $chapters = Chapter::select('id','name_ar')->orderBy('id')->get();

        return view('admin.recitations.index', compact('recitations', 'reciters', 'chapters', 'search', 'reciterId', 'chapterId', 'verseId', 'sortBy', 'sortOrder'));
    }

    public function create(): View
    {
        $reciters = Reciter::select('id','reciter_name')->orderBy('reciter_name')->get();
        return view('admin.recitations.create', compact('reciters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Recitation::create($data);
        return redirect()->route('admin.recitations.index')->with('success', 'تم إضافة التسجيل بنجاح');
    }

    public function show(Recitation $recitation): View
    {
        $recitation->load(['reciter', 'verse.chapter']);
        return view('admin.recitations.show', compact('recitation'));
    }

    public function edit(Recitation $recitation): View
    {
        $reciters = Reciter::select('id','reciter_name')->orderBy('reciter_name')->get();
        return view('admin.recitations.edit', compact('recitation', 'reciters'));
    }

    public function update(Request $request, Recitation $recitation): RedirectResponse
    {
        $data = $this->validateData($request, updating: true);
        $recitation->update($data);
        return redirect()->route('admin.recitations.index')->with('success', 'تم تحديث التسجيل بنجاح');
    }

    public function destroy(Recitation $recitation): RedirectResponse
    {
        $recitation->delete();
        return redirect()->route('admin.recitations.index')->with('success', 'تم حذف التسجيل بنجاح');
    }

    protected function validateData(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'reciter_id' => ['required', 'exists:reciters,id'],
            'verse_id' => ['required', 'exists:verses,id'],
            'audio_url' => ['required', 'url'],
        ]);
    }
}


