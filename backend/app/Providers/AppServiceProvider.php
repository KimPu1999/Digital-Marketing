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
        Gate::policy(\App\Models\Service::class, \App\Policies\ServicePolicy::class);
        Gate::policy(\App\Models\Package::class, \App\Policies\PackagePolicy::class);
        Gate::policy(\App\Models\PortfolioProject::class, \App\Policies\PortfolioProjectPolicy::class);
        Gate::policy(\App\Models\BlogPost::class, \App\Policies\BlogPostPolicy::class);
        Gate::policy(\App\Models\Faq::class, \App\Policies\FaqPolicy::class);
        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip());
        });
        RateLimiter::for('public-leads', function (Request $request) {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip());
        });
        RateLimiter::for('public-quotes', function (Request $request) {
            return Limit::perMinute(3)->by(strtolower((string) $request->input('email')).'|'.$request->ip());
        });
    }
}
