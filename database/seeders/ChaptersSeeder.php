<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ChaptersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⏳ Start the process of fetching and storing the chapters of the Holy Quran...');

        $api_url = "https://api.quran.com/api/v4/chapters";

        try {
            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->get($api_url);

            if (!$response->successful()) {
                throw new \Exception("Failed to connect to server: HTTP " . $response->status());
            }

            if (!isset($response['chapters'])) {
                throw new \Exception("Unexpected data structure from API");
            }

            $chapters = $response['chapters'];
            $totalChapters = count($chapters);
            $insertedCount = 0;
            $updatedCount = 0;

            $progressBar = $this->command->getOutput()->createProgressBar($totalChapters);
            $progressBar->start();

            foreach ($chapters as $chapter) {
                try {
                    $result = Chapter::updateOrCreate(
                        ['id' => $chapter['id']],
                        [
                            'name_ar' => $chapter['name_arabic'],
                            'name_en' => $chapter['name_simple'],
                            'revelation_place' => $chapter['revelation_place'],
                            'revelation_order' => $chapter['revelation_order'],
                            'verses_count' => $chapter['verses_count'],
                        ]
                    );

                    $result->wasRecentlyCreated ? $insertedCount++ : $updatedCount++;
                    $progressBar->advance();
                } catch (\Exception $e) {
                    $this->command->error("Error in Surah {$chapter['id']}: " . $e->getMessage());
                    continue;
                }
            }

            $progressBar->finish();
            $this->showStatistics($totalChapters, $insertedCount, $updatedCount);
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
