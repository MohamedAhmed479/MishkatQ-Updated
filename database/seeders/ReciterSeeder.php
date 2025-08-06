<?php

namespace Database\Seeders;

use App\Models\Reciter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ReciterSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⏳ Start the process of fetching Reciters of the Holy Quran...');

        try {
            $path = base_path('tests/data/reciters.json');

            if (!File::exists($path)) {
                $this->command->error("❌ File not found at path: {$path}");
                return;
            }

            $json = File::get($path);
            $reciters = json_decode($json, true);

            if (!is_array($reciters)) {
                $this->command->error("❌ Invalid JSON structure.");
                return;
            }

            $total = count($reciters);
            $inserted = 0;
            $updated = 0;

            foreach ($reciters as $reciter) {
                try {
                    $existing = Reciter::find($reciter['id']);

                    if ($existing) {
                        $existing->update($reciter);
                        $updated++;
                    } else {
                        Reciter::create($reciter);
                        $inserted++;
                    }

                } catch (\Exception $e) {
                    $this->command->warn("⚠️ Failed to process reciter ID {$reciter['id']}: {$e->getMessage()}");
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
        $this->command->line("📖 Number of Reciters in the source: {$total}");
        $this->command->line("🆕 New Reciters added: {$inserted}");
        $this->command->line("🔄 Updated Reciters: {$updated}");

        $successRate = ($inserted + $updated) / max($total, 1) * 100;
        $this->command->line("📈 Success Rate: " . round($successRate, 2) . '%');

        if ($inserted + $updated < $total) {
            $failed = $total - ($inserted + $updated);
            $this->command->error("⚠️ There are {$failed} Reciters that were not processed");
        }

        $this->command->info('🎉 The process of storing the Reciters of the Holy Quran has been completed successfully!');
    }
}
