<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading();

        // Blade permission helpers for admin guard
        Blade::if('canAdmin', function (string $permission): bool {
            return auth('admin')->check() && auth('admin')->user()->can($permission);
        });

        Blade::directive('permClass', function ($permission) {
            return "<?php echo (auth('admin')->check() && auth('admin')->user()->can($permission)) ? '' : 'opacity-40 pointer-events-none cursor-not-allowed'; ?>";
        });

        Blade::directive('permDisabled', function ($permission) {
            return "<?php echo (auth('admin')->check() && auth('admin')->user()->can($permission)) ? '' : 'disabled aria-disabled=\"true\" tabindex=\"-1\"'; ?>";
        });
    }
}
