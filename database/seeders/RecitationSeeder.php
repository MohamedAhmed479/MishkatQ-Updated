<?php

namespace Database\Seeders;

use App\Models\Recitation;
use App\Models\Reciter;
use App\Models\Verse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class RecitationSeeder extends Seeder
{
    public function run()
    {
        // Inform the user that the recitation table seeding process has started
        $this->command->info('⏳ Start filling the recitation table...');

        // Retrieve all reciters from the database
        $reciters = Reciter::all();

        // Count total number of verses in the Quran
        $totalVerses = Verse::count();

        // Calculate the expected number of recitations (each verse * each reciter)
        $expectedRecitations = $totalVerses * $reciters->count();

        // Initialize counters for success and failure
        $successCount = 0;
        $failedCount = 0;

        // Create a progress bar to visually show seeding progress
        $progressBar = $this->command->getOutput()->createProgressBar($expectedRecitations);

        // Process verses in chunks to avoid memory overload
        Verse::chunk(500, function ($verses) use (&$successCount, &$failedCount, $progressBar, $reciters) {
            // Loop through each verse in the current chunk
            foreach ($verses as $verse) {
                // For each verse, loop through all reciters
                foreach ($reciters as $reciter) {
                    // Attempt to fetch and store recitation for the current verse and reciter
                    $result = $this->fetchAndStoreRecitation($verse, $reciter);

                    // Update success or failure counter based on result
                    $result ? $successCount++ : $failedCount++;

                    // ✅ Advance the progress bar after processing each recitation
                    $progressBar->advance();
                }
            }
        });

        // Finish and close the progress bar
        $progressBar->finish();

        // Print spacing and summary of results
        $this->command->newLine(2);
        $this->showSummary($successCount, $failedCount, $expectedRecitations);
    }


    protected function fetchAndStoreRecitation($verse, $reciter): bool
    {
        try {
            // Base URL for accessing the audio files of the Quran recitations
            // $static_url = "https://verses.quran.foundation/";

            // Create a unique identifier for the verse by padding chapter and verse numbers
            $verse_serial = str_pad($verse->chapter_id, 3, '0', STR_PAD_LEFT) .
                str_pad($verse->verse_number, 3, '0', STR_PAD_LEFT);

            // Map reciter names to their respective file paths
            $recitersMap = [
                "AbdulBaset AbdulSamad" => "AbdulBaset/Mujawwad/mp3/",
                "Mishary Al-Afasy" => "Alafasy/mp3/",
                "Muhammad Siddiq al-Minshawi" => "Minshawi/Murattal/mp3/",
            ];

            // Get the specific file path for the reciter, or return null if not found
            $path = $recitersMap[$reciter->reciter_name] ?? null;

            // If the reciter is unknown, log an error and return false
            if (!$path) {
                $this->command->error("Unknown reciter: {$reciter->reciter_name}");
                return false;
            }

            // Build the full audio URL for the verse
            $verse_audio_url = $path . $verse_serial . ".mp3";

            // Store or update the recitation record in the database with the audio URL
            Recitation::updateOrCreate(
                ['verse_id' => $verse->id, 'reciter_id' => $reciter->id],
                ['audio_url' => $verse_audio_url]
            );

            // Return true if the recitation was successfully stored
            return true;
        } catch (\Exception $e) {
            // If any error occurs, log it and return false
            $this->command->error("Error in verse {$verse->verse_key}: " . $e->getMessage());
            return false;
        }
    }

    protected function showSummary($successCount, $failedCount, $expectedRecitations): void
    {
        // Print an empty line to separate the output for readability
        $this->command->newLine(2);

        // Output the summary header for the recitation process
        $this->command->info('📊 Summary of Recitation Table Fill Process');
        $this->command->line('------------------------------------------');

        // Calculate the total number of processed recitations (success + failures)
        $totalProcessed = $successCount + $failedCount;

        // Display the expected, successful, and failed recitations counts
        $this->command->line("🔢 Total expected recitations:     {$expectedRecitations}");
        $this->command->line("✅ Successfully stored recitations: {$successCount}");
        $this->command->line("❌ Failed recitations:              {$failedCount}");

        // Calculate and display the coverage percentage if there are expected recitations
        if ($expectedRecitations > 0) {
            $coveragePercentage = ($successCount / $expectedRecitations) * 100;
            $this->command->line("📈 Coverage percentage:            " . round($coveragePercentage, 2) . '%');
        } else {
            // If there are no expected recitations, show a warning
            $this->command->warn('⚠️ Expected recitations count is 0. Check your data.');
        }

        // If there are any failed recitations, show a warning
        if ($failedCount > 0) {
            $this->command->warn("⚠️ {$failedCount} recitations failed to store. Review the logs for more details.");
        }

        // If all recitations were successfully stored, print a success message
        if ($successCount === $expectedRecitations) {
            $this->command->info('✅ All recitations have been stored successfully!');
        }

        // Final message indicating the process is completed
        $this->command->info('🎉 Recitation table filling process completed.');
    }
}
