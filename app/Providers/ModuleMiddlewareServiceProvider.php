<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use App\Models\ModuleGenerator;
use Illuminate\Support\Facades\Log;

class ModuleMiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Регистрируем middleware модулей после полной инициализации приложения
        $this->registerModuleMiddlewares();
    }

    /**
     * Register module middlewares
     */
    private function registerModuleMiddlewares(): void
    {
        try {
            $moduleAliases = $this->loadModuleMiddlewares();
            
            // Регистрируем каждый middleware через router
            foreach ($moduleAliases as $alias => $className) {
                app('router')->aliasMiddleware($alias, $className);
            }

            Log::info('Module middlewares registered: ' . count($moduleAliases));
            
        } catch (\Exception $e) {
            Log::error('Error registering module middlewares: ' . $e->getMessage());
        }
    }

    /**
     * Load module middlewares dynamically
     */
    private function loadModuleMiddlewares(): array
    {
        $moduleMiddlewareAliases = [];

        try {
            // Теперь БД доступна, так как мы в методе boot()
            $allModuleData = ModuleGenerator::getAllModuleData();

            if (empty($allModuleData)) {
                Log::info('No module data found in database');
                return $moduleMiddlewareAliases;
            }

            foreach ($allModuleData as $tableName => $items) {
                foreach ($items as $item) {
                    $moduleCode = $item->code;
                    $studlyName = Str::studly($moduleCode);
                    
                    $middlewareClasses = [
                        'index' => "App\\Http\\Middleware\\Modules\\{$studlyName}\\{$studlyName}IndexMiddleware",
                        'create' => "App\\Http\\Middleware\\Modules\\{$studlyName}\\{$studlyName}CreateMiddleware",
                        'update' => "App\\Http\\Middleware\\Modules\\{$studlyName}\\{$studlyName}UpdateMiddleware",
                        'delete' => "App\\Http\\Middleware\\Modules\\{$studlyName}\\{$studlyName}DeleteMiddleware",
                    ];

                    foreach ($middlewareClasses as $action => $className) {
                        if (class_exists($className)) {
                            $moduleMiddlewareAliases[$moduleCode . '_' . $action] = $className;
                            Log::info("Middleware registered: {$moduleCode}_{$action}");
                        } else {
                            Log::warning("Middleware class not found: {$className}");
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Error loading module middlewares: ' . $e->getMessage());
        }

        return $moduleMiddlewareAliases;
    }
}