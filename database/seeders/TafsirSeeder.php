<?php

namespace Database\Seeders;

use App\Models\Tafsir;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class TafsirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('⏳ Start the process of fetching Tafsirs of the Holy Quran...');

        $api_url = "api.quran-tafseer.com/tafseer";

        try {
            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->baseUrl('http://' . $api_url)
                ->get('/');

            if (!$response->successful()) {
                throw new \Exception("Failed to connect to server: HTTP " . $response->status());
            }

            $tafsirs = $response->json();
            $totalTafsirs = count($tafsirs);
            $insertedCount = 0;
            $updatedCount = 0;

            if ($totalTafsirs === 0) {
                throw new \Exception("No tafsirs found in the API response");
            }

            $progressBar = $this->command->getOutput()->createProgressBar($totalTafsirs);
            $progressBar->start();

            foreach ($tafsirs as $tafsir) {
                try {
                    $result = Tafsir::updateOrCreate(
                        ['id' => $tafsir['id']],
                        [
                            'name' => $tafsir['name'],
                        ]
                    );

                    $result->wasRecentlyCreated ? $insertedCount++ : $updatedCount++;
                    $progressBar->advance();
                } catch (\Exception $e) {
                    $this->command->error("Error in tafsir {$tafsir['id']}: " . $e->getMessage());
                    continue;
                }
            }

            $progressBar->finish();
            $this->showStatistics($totalTafsirs, $insertedCount, $updatedCount);
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

        $successRate = ($inserted + $updated) / $total * 100;
        $this->command->line("📈 Success Rate: " . round($successRate, 2) . '%');

        if ($inserted + $updated < $total) {
            $failed = $total - ($inserted + $updated);
            $this->command->error("⚠️ There are {$failed} Tafsirs that were not processed");
        }

        $this->command->info('🎉 The process of storing the tafsirs of the Holy Quran has been completed successfully!');
    }
}