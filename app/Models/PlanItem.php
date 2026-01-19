<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanItem extends Model
{
    use HasFactory, AuditableTrait;

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
     * علاقة مع جدول اختبارات الجلسة
     */
    public function sessionTests(): HasMany
    {
        return $this->hasMany(SessionTest::class, "plan_item_id", "id");
    }

    /**
     * التحقق من اجتياز المستخدم لجميع الاختبارات المطلوبة
     */
    public function hasPassedAllTests(int $userId, float $threshold = null): bool
    {
        return SessionTest::hasPassedAllTests($userId, $this->id, $threshold);
    }

    /**
     * الحصول على إحصائيات الاختبارات لهذا العنصر
     */
    public function getTestStats(int $userId): array
    {
        return SessionTest::getTestStats($userId, $this->id);
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

    /**
     * Get the word count for this plan item
     */
    public function getWordCount(): int
    {
        $verses = Verse::where('chapter_id', $this->quran_surah_id)
            ->where('id', '>=', $this->verse_start_id)
            ->where('id', '<=', $this->verse_end_id)
            ->get();

        return $verses->sum(function ($verse) {
            $text = $verse->text_imlaei ?? $verse->text_uthmani;
            // Count Arabic words (split by spaces)
            return count(preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY));
        });
    }

    /**
     * Get the number of verses in this plan item
     */
    public function getVersesCount(): int
    {
        return Verse::where('chapter_id', $this->quran_surah_id)
            ->where('id', '>=', $this->verse_start_id)
            ->where('id', '<=', $this->verse_end_id)
            ->count();
    }
}
