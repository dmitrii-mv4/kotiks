<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RolesController;


Route::prefix('admin')->middleware(['admin'])->group(function () {

    Route::get('/', [App\Http\Controllers\Admin\Dashboard::class, 'dashboard'])->name('admin.dashboard');

    Route::prefix('/users')->controller(UsersController::class)->group(function () 
    {
        Route::get('/', 'index')->name('admin.users');
        Route::get('/create', 'create')->name('admin.users.create');
        Route::post('/store', 'store')->name('admin.users.store');
        Route::get('/edit/{user}', 'edit')->name('admin.users.edit');
        Route::patch('/edit/{user}', 'update')->name('admin.users.update');
        Route::delete('/delete/{user}', 'delete')->name('admin.users.delete');
    });

    Route::prefix('/roles')->controller(RolesController::class)->group(function () 
    {
        Route::get('/', 'index')->name('admin.roles');
        Route::get('/create', 'create')->name('admin.roles.create');
        Route::post('/store', 'store')->name('admin.roles.store');
        Route::get('/edit/{role}', 'edit')->name('admin.roles.edit');
        Route::patch('/edit/{role}', 'update')->name('admin.roles.update');
        Route::delete('/delete/{role}', 'delete')->name('admin.roles.delete');
    });
});