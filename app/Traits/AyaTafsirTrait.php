<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait AyaTafsirTrait
{
    /**
     * Get tafsir (interpretation) for a specific verse
     *
     * @param int|null $tafsirId The ID of the tafsir source
     * @param int $chapter The chapter (surah) ID
     * @param int $ayaNumber The verse number
     * @return string|null The tafsir text or null if not available
     */
    protected function getAyaTafsir(?int $tafsirId, int $chapter, int $ayaNumber): ?string
    {
        if (!$tafsirId) {
            return null;
        }

        try {
            $api_url = "api.quran-tafseer.com/tafseer";
            $endpoint = "$tafsirId/$chapter/$ayaNumber";

            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->baseUrl('http://' . $api_url)
                ->get($endpoint);

            if (!$response->successful()) {
                Log::error("Tafsir API error: HTTP " . $response->status() . " for tafsir_id: $tafsirId, chapter: $chapter, verse: $ayaNumber");
                return null;
            }

            $data = $response->json();

            if (isset($data['text']) && !empty($data['text'])) {
                return $data['text'];
            } else {
                Log::warning("Tafsir API returned unexpected data structure for tafsir_id: $tafsirId, chapter: $chapter, verse: $ayaNumber");
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Error fetching tafsir: " . $e->getMessage());
            return null;
        }
    }
}
