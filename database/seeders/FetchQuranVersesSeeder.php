<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Chapter;
use App\Models\Verse;
use App\Models\Word;


// Verses And Words


class FetchQuranVersesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⏳ Start the process of fetching and storing verses of the Holy Quran...');

        $start = now();
        $chapters = Chapter::all();
        $totalChapters = $chapters->count();
        $processedChapters = 0;
        $totalVersesInserted = 0;
        $totalVersesUpdated = 0;
        $failedVerses = 0;

        $chapterProgress = $this->command->getOutput()->createProgressBar($totalChapters);
        $chapterProgress->start();

        foreach ($chapters as $chapter) {
            $chapterVersesCount = $this->fetchAndStoreChapterVerses($chapter, $totalVersesInserted, $totalVersesUpdated, $failedVerses);

            $processedChapters++;
            $chapterProgress->advance();
            $this->command->info("\nProcessed {$chapterVersesCount} verse(s) from Surah {$chapter->name_ar}");
        }

        $chapterProgress->finish();
        $end = now();
        $duration = $start->diffForHumans($end, true);

        $this->showStatistics($totalChapters, $processedChapters, $totalVersesInserted, $totalVersesUpdated, $failedVerses, $duration);
    }

    protected function fetchAndStoreChapterVerses(Chapter $chapter, &$inserted, &$updated, &$failed): int
    {
        $page = 1;
        $perPage = 100;
        $totalPages = 1;
        $chapterVersesCount = 0;

        do {
            $response = Http::timeout(30)
                ->retry(3, 500)
                ->get("https://api.quran.com/api/v4/verses/by_chapter/{$chapter->id}", [
                    'language' => 'ar',
                    'words' => 'true',
                    'word_fields' => 'text_uthmani,text_imlaei',
                    'fields' => 'text_uthmani,text_imlaei',
                    'page' => $page,
                    'per_page' => $perPage
                ]);

            if ($response->successful() && isset($response['verses'])) {
                $totalPages = $response['pagination']['total_pages'] ?? $totalPages;

                foreach ($response['verses'] as $verse) {
                    try {
                        DB::transaction(function () use ($verse, $chapter, &$inserted, &$updated) {
                            $words = $verse['words'] ?? [];

                            $result = Verse::updateOrCreate(
                                ['id' => $verse['id']],
                                [
                                    'chapter_id'   => $chapter->id,
                                    'verse_number' => $verse['verse_number'],
                                    'verse_key'    => $verse['verse_key'],
                                    'text_uthmani' => $verse['text_uthmani'],
                                    'text_imlaei'  => $verse['text_imlaei'],
                                    'page_number'  => $verse['page_number'],
                                    'juz_number'   => $verse['juz_number'],
                                    'hizb_number'  => $verse['hizb_number'],
                                    'rub_number'   => $verse['rub_el_hizb_number'],
                                    'sajda'        => $verse['sajdah_number'] !== null,
                                ]
                            );

                            $wordsData = [];
                            foreach ($words as $word) {
                                $wordsData[] = [
                                    'verse_id' => $verse['id'],
                                    'position' => $word['position'],
                                    'text'     => $word['text_uthmani'] ?? '',
                                ];
                            }
                            Word::upsert($wordsData, ['verse_id', 'position'], ['text']);

                            $result->wasRecentlyCreated ? $inserted++ : $updated++;
                        });

                        $chapterVersesCount++;
                    } catch (\Exception $e) {
                        $this->command->error("Error in verse {$verse['id']}: " . $e->getMessage());
                        Log::channel('daily')->error("Verse {$verse['id']} error: " . $e->getMessage());
                        $failed++;
                    }
                }

                $page++;
                usleep(100000); // Respect API rate limit
            } else {
                $this->command->error("Failed to fetch verses for Surah {$chapter->name_ar} (page {$page}): " . $response->status());
                $failed += $perPage;
                break;
            }
        } while ($page <= $totalPages);

        return $chapterVersesCount;
    }

    protected function showStatistics($total, $processed, $inserted, $updated, $failed, $duration): void
    {
        $this->command->info("\n==========================");
        $this->command->info("Total Chapters: {$total}");
        $this->command->info("Chapters Processed: {$processed}");
        $this->command->info("Verses Inserted: {$inserted}");
        $this->command->info("Verses Updated: {$updated}");
        $this->command->info("Failed Verses: {$failed}");
        $this->command->info("⏱ Time taken: {$duration}");
        $this->command->info("==========================");
    }
}
