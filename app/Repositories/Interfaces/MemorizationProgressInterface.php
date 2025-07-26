<?php

namespace App\Repositories\Interfaces;

use App\Models\Chapter;
use App\Models\MemorizationProgress;

interface MemorizationProgressInterface
{
    public function findOrCreateProgress(int $userId, Chapter $chapter): MemorizationProgress;
    public function saveProgress(MemorizationProgress $progress): void;
}
