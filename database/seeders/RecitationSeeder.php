<?php

namespace Database\Seeders;

use App\Models\Recitation;
use App\Models\Reciter;
use App\Models\Verse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RecitationSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('⏳ Start filling the recitation table...');

        // 🔁 Start time
        $startTime = microtime(true);

        try {
            $path = base_path('tests/data/recitations.json');

            if (!File::exists($path)) {
                $this->command->error("❌ recitations.json file not found at: {$path}");
                return;
            }

            $data = json_decode(File::get($path), true);
            $total = count($data);

            if ($total === 0) {
                $this->command->warn("⚠️ No recitations found in the file.");
                return;
            }

            $this->command->info("🔍 Found {$total} recitations in the file.");
            $bar = $this->command->getOutput()->createProgressBar($total);
            $bar->start();

            // ✅ Fetch valid verse and reciter IDs once
            $validVerseIds = Verse::pluck('id')->toArray();
            $validReciterIds = Reciter::pluck('id')->toArray();

            // ⚡ Batch insert
            $batch = [];
            $successCount = 0;
            $failedCount = 0;
            $chunkSize = 1000;

            foreach ($data as $recitation) {
                if (
                    in_array($recitation['verse_id'], $validVerseIds) &&
                    in_array($recitation['reciter_id'], $validReciterIds)
                ) {
                    $batch[] = [
                        'verse_id' => $recitation['verse_id'],
                        'reciter_id' => $recitation['reciter_id'],
                        'audio_url' => $recitation['audio_url'] ?? null,
                    ];
                    $successCount++;
                } else {
                    $failedCount++;
                }

                if (count($batch) >= $chunkSize) {
                    Recitation::insert($batch);
                    $batch = [];
                }

                $bar->advance();
            }

            if (!empty($batch)) {
                Recitation::insert($batch);
            }

            $bar->finish();

            // 🔁 End time
            $endTime = microtime(true);
            $duration = $endTime - $startTime;

            $this->showSummary($successCount, $failedCount, $total, $duration);
        } catch (\Exception $e) {
            $this->command->error('❌ Major Error: ' . $e->getMessage());
        }
    }

    protected function showSummary($successCount, $failedCount, $expectedRecitations, $duration): void
    {
        $this->command->newLine(2);
        $this->command->info('📊 Summary of Recitation Table Fill Process');
        $this->command->line('------------------------------------------');

        $this->command->line("🔢 Total expected recitations:     {$expectedRecitations}");
        $this->command->line("✅ Successfully stored recitations: {$successCount}");
        $this->command->line("❌ Failed recitations:              {$failedCount}");

        if ($expectedRecitations > 0) {
            $coveragePercentage = ($successCount / $expectedRecitations) * 100;
            $this->command->line("📈 Coverage percentage:            " . round($coveragePercentage, 2) . '%');
        } else {
            $this->command->warn('⚠️ Expected recitations count is 0. Check your data.');
        }

        if ($failedCount > 0) {
            $this->command->warn("⚠️ {$failedCount} recitations failed to store. Review the data for issues.");
        }

        if ($successCount === $expectedRecitations) {
            $this->command->info('✅ All recitations have been stored successfully!');
        }

        // 🕒 Show duration
        $this->command->newLine();
        $this->command->info('⏱️ Time taken: ' . $this->formatDuration($duration));

        $this->command->info('🎉 Recitation table filling process completed.');
    }

    protected function formatDuration($seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = round($seconds % 60);

        if ($minutes > 0) {
            return "{$minutes} min {$remainingSeconds} sec";
        }

        return "{$remainingSeconds} sec";
    }
}
