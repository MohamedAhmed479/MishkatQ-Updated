<?php

namespace Database\Seeders;

use App\Models\Tafsir;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TafsirSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⏳ Start the process of fetching Tafsirs of the Holy Quran...');

        try {
            $path = base_path('tests/data/tafsirs.json');

            if (!File::exists($path)) {
                $this->command->error("❌ File not found at path: {$path}");
                return;
            }

            $json = File::get($path);
            $tafsirs = json_decode($json, true);

            if (!is_array($tafsirs)) {
                $this->command->error("❌ Invalid JSON structure.");
                return;
            }

            $total = count($tafsirs);
            $inserted = 0;
            $updated = 0;

            foreach ($tafsirs as $tafsir) {
                try {
                    $existing = Tafsir::find($tafsir['id']);

                    if ($existing) {
                        $existing->update($tafsir);
                        $updated++;
                    } else {
                        Tafsir::create($tafsir);
                        $inserted++;
                    }
                } catch (\Exception $e) {
                    $this->command->warn("⚠️ Failed to process tafsir ID {$tafsir['id']}: {$e->getMessage()}");
                }
            }

            $this->showStatistics($total, $inserted, $updated);

        } catch (\Exception $e) {
            $this->command->error('❌ Major Error: ' . $e->getMessage());
        }
    }

    protected function showStatistics($total, $inserted, $updated): void
    {
        $this->command->newLine(2);
        $this->command->info('📊 Execution Statistics:');
        $this->command->line("📖 Number of Tafsirs in the source: {$total}");
        $this->command->line("🆕 New Tafsirs added: {$inserted}");
        $this->command->line("🔄 Updated Tafsirs: {$updated}");

        $successRate = ($inserted + $updated) / max($total, 1) * 100;
        $this->command->line("📈 Success Rate: " . round($successRate, 2) . '%');

        if ($inserted + $updated < $total) {
            $failed = $total - ($inserted + $updated);
            $this->command->error("⚠️ There are {$failed} Tafsirs that were not processed");
        }

        $this->command->info('🎉 The process of storing the tafsirs of the Holy Quran has been completed successfully!');
    }
}
