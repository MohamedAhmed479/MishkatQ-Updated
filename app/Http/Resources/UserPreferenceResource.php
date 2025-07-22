<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "user_id" => $this->user_id,
            "current_level" => $this->current_level,
            "daily_minutes" => $this->daily_minutes,
            "tafsir_id" => $this->tafsir_id ?? null,
            "tafsir_name" => $this->tafsir ? $this->tafsir->name : null,
            "sessions_per_day" => $this->sessions_per_day,
            "preferred_times" => json_decode($this->preferred_times),
        ];
    }
}
