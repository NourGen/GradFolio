<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Portfolio;
use App\Models\Project;
use App\Policies\PortfolioPolicy;
use App\Policies\ProjectPolicy;

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
        // Register Policies
        Gate::policy(Portfolio::class, PortfolioPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);

        // Register Rate Limiters
        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->input('email') . $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('analytics', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->ip());
        });
    }
}
