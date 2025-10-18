<?php

namespace App\Policies\Modules;

use Illuminate\Auth\Access\Response;
use App\Models\User;

class NewsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Получаем все разрешения пользователя
        $permissions = $user->permissions;

        // Поиск конкретного разрешения по имени
        $showPermission = $permissions->firstWhere('name', 'module_news_viewAny');

        if ($showPermission)
        {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Получаем все разрешения пользователя
        $permissions = $user->permissions;

        // Поиск конкретного разрешения по имени
        $showPermission = $permissions->firstWhere('name', 'module_news_create');

        if ($showPermission)
        {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        // Получаем все разрешения пользователя
        $permissions = $user->permissions;

        // Поиск конкретного разрешения по имени
        $showPermission = $permissions->firstWhere('name', 'module_news_update');

        if ($showPermission)
        {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        // Получаем все разрешения пользователя
        $permissions = $user->permissions;

        // Поиск конкретного разрешения по имени
        $showPermission = $permissions->firstWhere('name', 'module_news_delete');

        if ($showPermission)
        {
            return true;
        }

        return false;
    }
}