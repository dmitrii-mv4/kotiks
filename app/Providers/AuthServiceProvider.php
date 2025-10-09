<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Role;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;

class AuthServiceProvider extends ServiceProvider
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
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);

        // Для супер админа все способности
        // Gate::before(function (User $user) {
        //     if ($user->role_id === 99) {
        //         return true;
        //     }
        // });

        // Gate::define('add-user', function(User $user) {
        //     return $user->role_id === 1;
        // });

        // Gate::define('update-user', function(User $user) {
        //     return $user->role_id === 1;
        // });

        // Gate::define('delete-user', function(User $user) {
        //     return $user->role_id === 1;
        // });
    }
}
