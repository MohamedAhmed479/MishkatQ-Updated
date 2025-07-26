<?php

namespace App\Repositories\Eloquent;

use App\Models\Chapter;
use App\Models\MemorizationProgress;
use App\Repositories\Interfaces\MemorizationProgressInterface;

class MemorizationProgressRepository implements MemorizationProgressInterface
{
    public function findOrCreateProgress(int $userId, Chapter $chapter): MemorizationProgress
    {
        $progress = MemorizationProgress::firstOrNew([
            'user_id' => $userId,
            'chapter_id' => $chapter->id,
        ]);

        if (! $progress->exists) {
            $progress->total_verses = $chapter->verses_count;
            $progress->verses_memorized = 0;
        }

        return $progress;
    }

    public function saveProgress(MemorizationProgress $progress): void
    {
        $progress->save();
    }
}
