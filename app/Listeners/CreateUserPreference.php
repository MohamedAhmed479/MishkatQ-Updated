<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Models\UserPreference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateUserPreference
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegisteredEvent $event): void
    {
        UserPreference::create([
            "user_id" => $event->user->id,
            // "tafsir_id" => 1,
            "daily_minutes" => 0,
            "sessions_per_day" => 0,
            "current_level" => "beginner",
        ]);
    }
}
