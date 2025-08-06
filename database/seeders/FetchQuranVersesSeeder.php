<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Verse;

class FetchQuranVersesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⏳ Start the process of fetching and storing verses of the Holy Quran...');

        $startTime = now();

        try {
            // قراءة الملف
            $path = base_path('tests/data/verses.json');
            if (!File::exists($path)) {
                $this->command->error("❌ File not found at path: {$path}");
                return;
            }

            $json = File::get($path);
            $verses = json_decode($json, true);

            if (!is_array($verses)) {
                $this->command->error("❌ Invalid JSON structure.");
                return;
            }

            // إحصائيات
            $total = count($verses);
            $inserted = 0;
            $updated = 0;
            $failed = 0;

            // تحسين الأداء: تقسيم البيانات إلى مجموعات
            $chunks = array_chunk($verses, 1000); // معالجة 1000 آية في كل مرة
            
            $this->command->info("📊 Processing {$total} verses in " . count($chunks) . " chunks...");

            foreach ($chunks as $chunkIndex => $chunk) {
                $this->command->info("🔄 Processing chunk " . ($chunkIndex + 1) . "/" . count($chunks));
                
                // استخدام المعاملات لتحسين الأداء
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

    protected function processChunk(array $verses): array
    {
        $inserted = 0;
        $updated = 0;
        $failed = 0;

        // استخراج جميع معرفات الآيات في هذه المجموعة
        $verseIds = array_column($verses, 'id');
        
        // جلب الآيات الموجودة مسبقاً
        $existingVerses = Verse::whereIn('id', $verseIds)->get()->keyBy('id');
        
        $toInsert = [];
        $toUpdate = [];

        foreach ($verses as $verse) {
            try {
                if (isset($existingVerses[$verse['id']])) {
                    // تحديث الآية الموجودة
                    $toUpdate[] = $verse;
                } else {
                    // إدراج آية جديدة
                    $toInsert[] = $verse;
                }
            } catch (\Exception $e) {
                $failed++;
            }
        }

        // إدراج الآيات الجديدة بشكل جماعي
        if (!empty($toInsert)) {
            try {
                Verse::insert($toInsert);
                $inserted = count($toInsert);
            } catch (\Exception $e) {
                $failed += count($toInsert);
                $this->command->warn("⚠️ Failed to bulk insert: {$e->getMessage()}");
            }
        }

        // تحديث الآيات الموجودة بشكل جماعي
        if (!empty($toUpdate)) {
            try {
                $this->bulkUpdate($toUpdate);
                $updated = count($toUpdate);
            } catch (\Exception $e) {
                $failed += count($toUpdate);
                $this->command->warn("⚠️ Failed to bulk update: {$e->getMessage()}");
            }
        }

        return [
            'inserted' => $inserted,
            'updated' => $updated,
            'failed' => $failed
        ];
    }

    protected function bulkUpdate(array $verses): void
    {
        // استخدام upsert بدلاً من update منفصل لكل آية
        Verse::upsert($verses, ['id'], array_keys($verses[0]));
    }

    protected function showStatistics($total, $processed, $inserted, $updated, $failed, $duration): void
    {
        $this->command->info("\n==========================");
        $this->command->info("📖 Total Verses in file: {$total}");
        $this->command->info("✅ Verses Processed: {$processed}");
        $this->command->info("🆕 Inserted: {$inserted}");
        $this->command->info("🔄 Updated: {$updated}");
        $this->command->info("❌ Failed: {$failed}");
        $this->command->info("⏱ Time taken: {$duration}");
        $this->command->info("==========================\n");
    }
}
