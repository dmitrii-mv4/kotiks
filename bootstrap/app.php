<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminPanelMiddleware;
use App\Http\Middleware\Users\UsersIndexMiddleware;
use App\Http\Middleware\Users\UsersCreateMiddleware;
use App\Http\Middleware\Users\UsersUpdateMiddleware;
use App\Http\Middleware\Users\UsersDeleteMiddleware;
use App\Http\Middleware\Roles\RolesIndexMiddleware;
use App\Http\Middleware\Roles\RolesCreateMiddleware;
use App\Http\Middleware\Roles\RolesUpdateMiddleware;
use App\Http\Middleware\Roles\RolesDeleteMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminPanelMiddleware::class,
            'users_index' => UsersIndexMiddleware::class,
            'users_create' => UsersCreateMiddleware::class,
            'users_update' => UsersUpdateMiddleware::class,
            'users_delete' => UsersDeleteMiddleware::class,

            'roles_index' => RolesIndexMiddleware::class,
            'roles_create' => RolesCreateMiddleware::class,
            'roles_update' => RolesUpdateMiddleware::class,
            'roles_delete' => RolesDeleteMiddleware::class,
        ]);
    })
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            $apiRoutesPath = base_path('routes/api');
            
            if (File::isDirectory($apiRoutesPath)) {
                foreach (File::files($apiRoutesPath) as $file) {
                    // Use the correct method to get the file extension
                    if ($file->getExtension() === 'php') { // Or use: if ($file->extension() === 'php')
                        Route::middleware('api') // Применяет middleware группу 'api'
                            ->prefix('api') // This adds /api prefix to all routes in the file
                            ->group($file->getPathname());
                    }
                }
            }
        },
    )
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
