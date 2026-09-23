<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
    }
}
