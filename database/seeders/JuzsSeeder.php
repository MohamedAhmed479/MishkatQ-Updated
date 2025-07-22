<?php

namespace Database\Seeders;

use App\Models\Juz;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class JuzsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('⏳ Start the process of fetching juzs of the Holy Quran...');

        $api_url = "https://api.quran.com/api/v4/juzs";

        try {
            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->get($api_url);

            if (!$response->successful()) {
                throw new \Exception("Failed to connect to server: HTTP " . $response->status());
            }

            if (!isset($response['juzs'])) {
                throw new \Exception("Unexpected data structure from API");
            } 

            $juzs = $response['juzs'];
            $totalJuzs = count($juzs);
            $insertedCount = 0;
            $updatedCount = 0;

            $progressBar = $this->command->getOutput()->createProgressBar($totalJuzs);
            $progressBar->start();

            foreach ($juzs as $juz) {
                try {
                    $result = Juz::updateOrCreate(
                        ['juz_number' => $juz['juz_number']],
                        [
                            'start_verse_id' => $juz['first_verse_id'],
                            'end_verse_id' => $juz['last_verse_id'],
                            'verses_count' => $juz['verses_count'],
                        ]
                    );

                    $result->wasRecentlyCreated ? $insertedCount++ : $updatedCount++;
                    $progressBar->advance();
                } catch (\Exception $e) {
                    $this->command->error("Error in juz {$juz['id']}: " . $e->getMessage());
                    continue;
                }
            }

            $progressBar->finish();
            $this->showStatistics($totalJuzs, $insertedCount, $updatedCount);
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

        $successRate = ($inserted + $updated) / $total * 100;
        $this->command->line("📈 Success Rate: " . round($successRate, 2) . '%');

        if ($inserted + $updated < $total) {
            $failed = $total - ($inserted + $updated);
            $this->command->error("⚠️ There are {$failed} Juzs that were not processed");
        }

        $this->command->info('🎉 The process of storing the Juzs of the Holy Quran has been completed successfully!');
    }
}
