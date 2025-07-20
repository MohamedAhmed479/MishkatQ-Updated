<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Models\UserProfile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateUserProfile
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
        UserProfile::create([
            'user_id' => $event->user->id,
            'username' => $event->user->name . rand(100, 999),
        ]);
    }
}
