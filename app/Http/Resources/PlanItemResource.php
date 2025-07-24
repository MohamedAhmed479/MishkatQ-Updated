<?php

namespace App\Http\Resources;

use App\Traits\AyaTafsirTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class PlanItemResource extends JsonResource
{
    use AyaTafsirTrait;

    protected ?int $tafsirId;
    protected $verses;

    public function __construct($resource, Collection $verses, ?int $tafsirId = null)
    {
        parent::__construct($resource);
        $this->tafsirId = $tafsirId;
        $this->verses = $verses;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'surah' => [
                'id' => $this->quranSurah->id,
                'name_ar' => $this->quranSurah->name_ar,
                'name_en' => $this->quranSurah->name_en,
            ],
            'verses' => $this->verses->map(function ($verse) {
                return [
                    'number' => $verse->verse_number,
                    'text_uthmani' => $verse->text_uthmani,
                    'text_imlaei' => $verse->text_imlaei,
                    'tafsir' => $this->getAyaTafsir($this->tafsirId, $this->quranSurah->id, $verse->verse_number),
                    'words' => $verse->words->map(fn($word) => [
                        'position' => $word->position,
                        'text' => $word->text,
                    ]),
                    'recitations' => $verse->recitations->map(fn($recitation) => [
                        'reciter_name' => $recitation->reciter->reciter_name,
                        'audio_url' => "https://verses.quran.foundation/" . $recitation->audio_url,
                    ]),
                ];
            }),
            'target_date' => $this->target_date,
            'sequence' => $this->sequence,
        ];
    }
}
