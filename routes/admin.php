<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\Interface\MenuController;


Route::prefix('admin')->middleware(['admin'])->group(function () {

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

    Route::prefix('/interface')->group(function () 
    {
        Route::prefix('/menu')->controller(MenuController::class)->group(function () 
        {
            Route::get('/', 'index')->name('admin.interface.menu.index');
            Route::get('/create', 'create')->name('admin.interface.menu.create');
            Route::post('/store', 'store')->name('admin.interface.menu.store');
        });
    });
});