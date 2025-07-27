<?php

namespace App\Providers;

use App\Repositories\Eloquent\BadgeRepository;
use App\Repositories\Eloquent\LeaderboardRepository;
use App\Repositories\Eloquent\MemorizationPlanRepository;
use App\Repositories\Eloquent\PlanItemRepository;
use App\Repositories\Eloquent\PointsTransactionRepository;
use App\Repositories\Eloquent\RevisionReviewsRepository;
use App\Repositories\Eloquent\SpacedRepetitionRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Interfaces\BadgeInterface;
use App\Repositories\Interfaces\LeaderboardInterface;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use App\Repositories\Interfaces\PlanItemInterface;
use App\Repositories\Interfaces\PointsTransactionInterface;
use App\Repositories\Interfaces\RevisionReviewsInterface;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use App\Repositories\Interfaces\UserInterface;
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
        $this->app->bind(SpacedRepetitionInterface::class, SpacedRepetitionRepository::class);
        $this->app->bind(RevisionReviewsInterface::class, RevisionReviewsRepository::class);

        $this->app->bind(UserInterface::class, UserRepository::class);

        // Incentive System
        $this->app->bind(BadgeInterface::class, BadgeRepository::class);
        $this->app->bind(PointsTransactionInterface::class, PointsTransactionRepository::class);
        $this->app->bind(LeaderboardInterface::class, LeaderboardRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
