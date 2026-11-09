<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        RateLimiter::for('public-form', fn (Request $request) => Limit::perMinute(6)->by($request->ip()));
        Gate::define('manage-business', fn ($user) => $user->canManageBusiness());
        Gate::define('view-platform-status', fn ($user) => (bool) $user->is_platform_admin);
    }
}
