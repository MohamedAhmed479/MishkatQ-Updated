<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Word;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⏳ Start the process of fetching and storing words...');

        $startTime = now();

        try {
            // قراءة الملف
            $path = base_path('tests/data/words.json');
            if (!File::exists($path)) {
                $this->command->error("❌ File not found at path: {$path}");
                return;
            }

            $json = File::get($path);
            $words = json_decode($json, true);

            if (!is_array($words)) {
                $this->command->error("❌ Invalid JSON structure.");
                return;
            }

            // إحصائيات
            $total = count($words);
            $inserted = 0;
            $updated = 0;
            $failed = 0;

            // تقسيم البيانات لمجموعات
            $chunks = array_chunk($words, 1000);
            $this->command->info("📊 Processing {$total} words in " . count($chunks) . " chunks...");

            foreach ($chunks as $chunkIndex => $chunk) {
                $this->command->info("🔄 Processing chunk " . ($chunkIndex + 1) . "/" . count($chunks));

                DB::beginTransaction();

                try {
                    $chunkStats = $this->processChunk($chunk);
                    $inserted += $chunkStats['inserted'];
                    $updated += $chunkStats['updated'];
                    $failed += $chunkStats['failed'];

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $failed += count($chunk);
                    $this->command->warn("⚠️ Failed to process chunk " . ($chunkIndex + 1) . ": {$e->getMessage()}");
                }
            }

            $duration = now()->diffForHumans($startTime, true);
            $this->showStatistics($total, $inserted + $updated, $inserted, $updated, $failed, $duration);

        } catch (\Exception $e) {
            $this->command->error('❌ Major Error: ' . $e->getMessage());
        }
    }

    protected function processChunk(array $words): array
    {
        $inserted = 0;
        $updated = 0;
        $failed = 0;

        // IDs للكلمات
        $wordIds = array_column($words, 'id');

        // جلب الكلمات الموجودة مسبقاً
        $existingWords = Word::whereIn('id', $wordIds)->get()->keyBy('id');

        $toInsert = [];
        $toUpdate = [];

        foreach ($words as $word) {
            try {
                if (isset($existingWords[$word['id']])) {
                    $toUpdate[] = $word;
                } else {
                    $toInsert[] = $word;
                }
            } catch (\Exception $e) {
                $failed++;
            }
        }

        // إدراج جديد
        if (!empty($toInsert)) {
            try {
                Word::insert($toInsert);
                $inserted = count($toInsert);
            } catch (\Exception $e) {
                $failed += count($toInsert);
                $this->command->warn("⚠️ Failed to bulk insert: {$e->getMessage()}");
            }
        }

        // تحديث موجود
        if (!empty($toUpdate)) {
            try {
                $this->bulkUpdate($toUpdate);
                $updated = count($toUpdate);
            } catch (\Exception $e) {
                $failed += count($toUpdate);
                $this->command->warn("⚠️ Failed to bulk update: {$e->getMessage()}");
            }
        }

        return compact('inserted', 'updated', 'failed');
    }

    protected function bulkUpdate(array $words): void
    {
        Word::upsert($words, ['id'], array_keys($words[0]));
    }

    protected function showStatistics($total, $processed, $inserted, $updated, $failed, $duration): void
    {
        $this->command->info("\n==========================");
        $this->command->info("📚 Total Words in file: {$total}");
        $this->command->info("✅ Words Processed: {$processed}");
        $this->command->info("🆕 Inserted: {$inserted}");
        $this->command->info("🔄 Updated: {$updated}");
        $this->command->info("❌ Failed: {$failed}");
        $this->command->info("⏱ Time taken: {$duration}");
        $this->command->info("==========================\n");
    }
}
