<?php

namespace Database\Seeders;

use App\Models\Juz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class JuzsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⏳ Start the process of fetching juzs of the Holy Quran...');

        try {
            $path = base_path('tests/data/juzs.json');

            if (!File::exists($path)) {
                $this->command->error("❌ File not found at path: {$path}");
                return;
            }

            $json = File::get($path);
            $juzs = json_decode($json, true);

            if (!is_array($juzs)) {
                $this->command->error("❌ Invalid JSON structure.");
                return;
            }

            $total = count($juzs);
            $inserted = 0;
            $updated = 0;

            foreach ($juzs as $juz) {
                try {
                    $existing = Juz::find($juz['id']);

                    if ($existing) {
                        $existing->update($juz);
                        $updated++;
                    } else {
                        Juz::create($juz);
                        $inserted++;
                    }

                } catch (\Exception $e) {
                    $this->command->warn("⚠️ Failed to process juz ID {$juz['id']}: {$e->getMessage()}");
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
        $this->command->line("📖 Number of Juzs in the source: {$total}");
        $this->command->line("🆕 New Juzs added: {$inserted}");
        $this->command->line("🔄 Updated Juzs: {$updated}");

        $successRate = ($inserted + $updated) / max($total, 1) * 100;
        $this->command->line("📈 Success Rate: " . round($successRate, 2) . '%');

        if ($inserted + $updated < $total) {
            $failed = $total - ($inserted + $updated);
            $this->command->error("⚠️ There are {$failed} Juzs that were not processed");
        }

        $this->command->info('🎉 The process of storing the Juzs of the Holy Quran has been completed successfully!');
    }
}
