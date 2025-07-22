<?php

namespace App\Providers;

use App\Repositories\Eloquent\MemorizationPlanRepository;
use App\Repositories\Eloquent\PlanItemRepository;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use App\Repositories\Interfaces\PlanItemInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(MemorizationPlanInterface::class, MemorizationPlanRepository::class);
        $this->app->bind(PlanItemInterface::class, PlanItemRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
