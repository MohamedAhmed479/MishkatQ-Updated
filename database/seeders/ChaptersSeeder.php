<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ChaptersSeeder extends Seeder
{

    public function run(): void
    {
        $this->command->info('⏳ Start the process of fetching and storing the chapters of the Holy Quran...');

        try {
            $json = File::get(base_path('tests/data/chapters.json'));
            $chapters = json_decode($json, true);

            $total = count($chapters);
            $inserted = 0;
            $updated = 0;

            foreach ($chapters as $chapter) {
                $existing = Chapter::find($chapter['id']);

                if ($existing) {
                    $existing->update($chapter);
                    $updated++;
                } else {
                    Chapter::create($chapter);
                    $inserted++;
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
        $this->command->line("📖 Number of Surahs in the source: {$total}");
        $this->command->line("🆕 New Surahs added: {$inserted}");
        $this->command->line("🔄 Updated Surahs: {$updated}");

        $successRate = ($inserted + $updated) / $total * 100;
        $this->command->line("📈 Success Rate: " . round($successRate, 2) . '%');

        if ($inserted + $updated < $total) {
            $failed = $total - ($inserted + $updated);
            $this->command->error("⚠️ There are {$failed} surahs that were not processed");
        }

        $this->command->info('🎉 The process of storing the surahs of the Holy Quran has been completed successfully!');
    }
}
