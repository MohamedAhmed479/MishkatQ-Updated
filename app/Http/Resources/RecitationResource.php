<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->load('reciter');

        return [
            'id' => $this->id,
            'verse_id' => $this->verse_id,
            'reciter_id' => $this->reciter_id,
            'audio_url' => "https://verses.quran.foundation/" . $this->audio_url,
            'reciter' => new ReciterResource($this->whenLoaded('reciter')),
        ];
    }
}
