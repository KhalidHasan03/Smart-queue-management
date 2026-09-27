<?php

namespace App\Providers;

use App\Support\ReviewSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
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
        // The whole app authorises through the custom permission middleware and
        // User::hasPermission(), not Laravel's Gate. Register a matching Blade
        // directive so views can guard UI with the same permission names.
        Blade::if('permission', function (string $permission) {
            return auth()->user()?->hasPermission($permission) ?? false;
        });

        // The kiosk is a public, code-guessing surface, so both steps are rate
        // limited per IP. The ceiling is admin-tunable through Review Kiosk
        // Setup without touching the route definitions.
        RateLimiter::for('review-kiosk', function (Request $request) {
            return Limit::perMinute(ReviewSettings::throttlePerMinute())->by($request->ip());
        });
    }
}
