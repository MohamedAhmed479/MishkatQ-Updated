<?php

namespace App\Http\Resources;

use App\Traits\AyaTafsirTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VerseResource extends JsonResource
{
    use AyaTafsirTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->load("chapter");
        return [
            'id' => $this->id,
            'chapter_id' => $this->chapter_id,
            'verse_number' => $this->verse_number,
            'verse_key' => $this->verse_key,
            'text_uthmani' => $this->text_uthmani,
            'text_imlaei' => $this->text_imlaei,
            'page_number' => $this->page_number,
            'juz_number' => $this->juz_number,
            'hizb_number' => $this->hizb_number,
            'rub_number' => $this->rub_number,
            'chapter' => new ChapterResource($this->whenLoaded('chapter')),
            'sajda' => $this->sajda,
            'words' => WordResource::collection($this->whenLoaded('words')),
            'recitations' => RecitationResource::collection($this->whenLoaded('recitations')),
        ];
    }
}
