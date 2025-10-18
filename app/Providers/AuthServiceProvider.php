<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\ModuleGenerator;
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

        // Подключаем модули
        $this->registerModulePolicies();

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

    /**
     * Регистрируем модули для политики
     */
    private function registerModulePolicies(): void
    {
        try {
            $allModuleData = ModuleGenerator::getAllModuleData();

            foreach ($allModuleData as $tableName => $items) {
                foreach ($items as $item) {
                    $moduleCode = $item->code;
                    $studlyName = Str::studly($moduleCode);
                    
                    // Формируем имена классов
                    $modelClass = "App\\Models\\Modules\\{$studlyName}Module";
                    $policyClass = "App\\Policies\\Modules\\{$studlyName}Policy";

                    // Проверяем существование классов перед регистрацией
                    if (class_exists($modelClass) && class_exists($policyClass)) {
                        Gate::policy($modelClass, $policyClass);
                        \Log::info("Policy registered: {$modelClass} => {$policyClass}");
                    } else {
                        \Log::warning("Policy registration failed - classes not found:", [
                            'module' => $moduleCode,
                            'model_class' => $modelClass,
                            'policy_class' => $policyClass,
                            'model_exists' => class_exists($modelClass),
                            'policy_exists' => class_exists($policyClass),
                        ]);
                    }
                }
            }

        } catch (\Exception $e) {
            \Log::error('Error registering module policies: ' . $e->getMessage());
        }
    }
}
