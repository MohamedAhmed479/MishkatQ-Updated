<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanItem extends Model
{

    /**
     * القواعد التي يجب تحويلها
     *
     * @var array
     */
    protected $casts = [
        'target_date' => 'date',
    ];


    protected $guarded = ['id'];

    public function memorizationPlan()
    {
        return $this->belongsTo(MemorizationPlan::class, "plan_id", "id");
    }

    public function quranSurah()
    {
        return $this->belongsTo(Chapter::class, "quran_surah_id", "id");
    }

    public function verseStart()
    {
        return $this->belongsTo(Verse::class, "verse_start_id", "id");
    }

    public function verseEnd()
    {
        return $this->belongsTo(Verse::class, "verse_end_id", "id");
    }

    /**
     * علاقة مع جدول المراجعات المتباعدة
     */
    public function spacedRepetitions(): HasMany
    {
        return $this->hasMany(SpacedRepetition::class, "plan_item_id", "id");
    }

    /**
     * الحصول على نص الآيات المرتبطة بهذا المقطع
     */
    public function getVersesText()
    {
        return Verse::where('chapter_id', $this->quran_surah_id)
            ->where('id', '>=', $this->verse_start_id)
            ->where('id', '<=', $this->verse_end_id)
            ->pluck('text_uthmani');
    }

    /**
     * الحصول على وصف المقطع
     */
    public function getDescription(): string
    {
        $surahName = $this->quranSurah->name_ar;
        $startVerseNumber = $this->verseStart->verse_number;
        $endVerseNumber = $this->verseEnd->verse_number;

        return "{$surahName} ({$startVerseNumber}-{$endVerseNumber})";
    }
}
