<?php

namespace Boilerplate\Auth\Providers;

use Laravel\Sanctum\Console\Commands\PruneExpired;
use Laravel\Sanctum\Sanctum;

class SanctumServiceProvider extends \Laravel\Sanctum\SanctumServiceProvider
{
    public function register(): void
    {
        Sanctum::ignoreMigrations();

        config([
            'auth.guards.sanctum' => array_merge([
                'driver' => 'sanctum',
                'provider' => null,
            ], config('auth.guards.sanctum', [])),
        ]);

        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../resources/config/sanctum.php', 'sanctum');
        }
    }

    public function boot(): void
    {
        if (app()->runningInConsole()) {
            $this->commands([
                PruneExpired::class,
            ]);
        }

        $this->defineRoutes();
        $this->configureGuard();
        $this->configureMiddleware();
    }
}
