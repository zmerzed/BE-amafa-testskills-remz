<?php

namespace Boilerplate\Auth\Providers;

use App\Support\Models;
use Boilerplate\Auth\Console\Commands\AclSync;
use Boilerplate\Auth\Http\Resources\PasswordResetResource;
use Boilerplate\Auth\Http\Resources\UserResource;
use Boilerplate\Auth\Middleware\VerificationTokenMiddleware;
use Boilerplate\Auth\Models\PasswordReset;
use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Policies\UserPolicy;
use Boilerplate\Media\Http\Resources\MediaResource;
use Boilerplate\Media\Models\Media;
use Illuminate\Support\Facades\Gate;
use Konekt\Concord\BaseBoxServiceProvider;
use Spatie\Permission\Middlewares\RoleMiddleware;

class ModuleServiceProvider extends BaseBoxServiceProvider
{
    protected $models = [
        User::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->register(SanctumServiceProvider::class);

        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../resources/config/permission.php', 'permission');
            $this->mergeConfigFrom(__DIR__ . '/../resources/config/media-library.php', 'media-library');
            $this->mergeConfigFrom(__DIR__ . '/../resources/config/oauth.php', 'oauth');
        }

        $this->registerResources();
        $this->registerCommands();
        $this->registerPolicies();
    }

    public function boot(): void
    {
        parent::boot();

        config()->set('auth.guards.api.driver', 'sanctum');
        config()->set('auth.providers.users.model', User::class);
        $this->app->alias(VerificationTokenMiddleware::class, 'verification-token');
        $this->app->alias(RoleMiddleware::class, 'user.role');
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AclSync::class,
            ]);
        }
    }

    protected function registerResources(): void
    {
        Models::registerModel(User::class, UserResource::class, 'users');
        Models::registerModel(Media::class, MediaResource::class, 'media');
        Models::registerModel(PasswordReset::class, PasswordResetResource::class, 'password_resets');
    }

    protected function registerPolicies(): void
    {
        Gate::policy(User::class, UserPolicy::class);

        Gate::define('update-onboarding-details', function (User $user) {
            return !$user->isOnboarded();
        });
    }
}
