<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\ModuleGenerator\ModuleGeneratorController;
use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use App\Http\Controllers\Admin\ModuleGenerator\DeleteModuleController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ModuleGenerator;


Route::prefix('admin')->middleware(['admin'])->group(function () {

    // Работа с модулями
    $allModuleData = ModuleGenerator::getAllModuleData();

    foreach ($allModuleData as $tableName => $items) {
        foreach ($items as $item) {
            // Формируем имя контроллера
            $controllerName = 'App\\Http\\Controllers\\Admin\\Modules\\' . 
                Str::studly($item->code) . 'Controller';

            // Проверяем существование класса контроллера
            if (!class_exists($controllerName)) {
                continue; // Пропускаем если контроллер не существует
            }

            // Проверяем наличие свойства code у $item
            if (!isset($item->code)) {
                continue; // Пропускаем если свойство не существует
            }

            // Создаем префикс для группы роутов
            $prefix = '/modules/' . $item->code;

            // Регистрируем маршруты ДЛЯ КАЖДОГО элемента
            Route::prefix($prefix)->group(function () use ($controllerName, $item) {
                Route::get('/', [$controllerName, 'index'])
                    ->middleware([$item->code . '_index'])
                    ->name('admin.modules.' . $item->code . '.index');
                        
                Route::get('/create', [$controllerName, 'create'])
                    ->middleware([$item->code . '_create'])
                    ->name('admin.modules.' . $item->code . '.create');

                Route::patch('/store', [$controllerName, 'store'])
                    ->middleware([$item->code . '_create'])
                    ->name('admin.modules.' . $item->code . '.store');

                Route::get('/edit/{item}', [$controllerName, 'edit'])
                    ->middleware([$item->code . '_update'])
                    ->name('admin.modules.' . $item->code . '.edit');

                Route::patch('/update/{item}', [$controllerName, 'update'])
                    ->middleware([$item->code . '_update'])
                    ->name('admin.modules.' . $item->code . '.update');

                Route::delete('/delete/{item}', [$controllerName, 'delete'])
                    ->middleware([$item->code . '_delete'])
                    ->name('admin.modules.' . $item->code . '.delete');
            });
        }
    }

    Route::get('/', [App\Http\Controllers\Admin\Dashboard::class, 'dashboard'])->name('admin.dashboard');

    Route::prefix('/users')->controller(UsersController::class)->group(function () 
    {
        Route::get('/', 'index')->middleware(['users_index'])->name('admin.users');
        Route::get('/create', 'create')->middleware(['users_create'])->name('admin.users.create');
        Route::post('/store', 'store')->middleware(['users_create'])->name('admin.users.store');
        Route::get('/edit/{user}', 'edit')->middleware(['users_update'])->name('admin.users.edit');
        Route::patch('/edit/{user}', 'update')->middleware(['users_update'])->name('admin.users.update');
        Route::delete('/delete/{user}', 'delete')->middleware(['users_delete'])->name('admin.users.delete');
    });

    Route::prefix('/roles')->controller(RolesController::class)->group(function () 
    {
        Route::get('/', 'index')->middleware(['roles_index'])->name('admin.roles');
        Route::get('/create', 'create')->middleware(['roles_create'])->name('admin.roles.create');
        Route::post('/store', 'store')->middleware(['roles_create'])->name('admin.roles.store');
        Route::get('/edit/{role}', 'edit')->middleware(['roles_update'])->name('admin.roles.edit');
        Route::patch('/edit/{role}', 'update')->middleware(['roles_update'])->name('admin.roles.update');
        Route::delete('/delete/{role}', 'delete')->middleware(['roles_delete'])->name('admin.roles.delete');
    });

    Route::prefix('/modules')->group(function ()
    {
        Route::get('/', [ModuleGeneratorController::class, 'index'])->name('admin.modules');
        Route::get('/create', [CreateModuleController::class, 'create'])->name('admin.modules.create');
        Route::post('/store', [CreateModuleController::class, 'store'])->name('admin.modules.store');
        Route::delete('/delete/{moduleCode}', [DeleteModuleController::class, 'delete'])->name('admin.modules.delete');
    });
});