<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Users\Roles\RoleCreateRequest;
use App\Http\Requests\Users\Roles\RoleEditRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Models\RoleHasPermissions;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::get();

        //$this->authorize('viewAny', \App\Models\User::class);

        return view('admin/users/roles/index', compact('roles', 'users'));
    }

    public function create()
    {
        $roles = Role::get();

        return view('admin/users/roles/create', compact('roles'));
    }

    public function store(RoleCreateRequest $request)
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
        ]);

        // Проверяем, отмечен ли чекбокс show_admin
        if ($request->has('show_admin')) {

            // Ищем разрешение по имени
            $permission = Permission::where('name', 'show_admin')->first();

            // Если разрешение найдено, добавляем связь
            if ($permission) {
                RoleHasPermissions::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        }

        // Проверяем, отмечен ли чекбокс users_viewAny
        if ($request->has('users_viewAny')) {
            
            // Ищем разрешение по имени
            $permission = Permission::where('name', 'users_viewAny')->first();

            // Если разрешение найдено, добавляем связь
            if ($permission) {
                RoleHasPermissions::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        } 

        // Проверяем, отмечен ли чекбокс users_view
        if ($request->has('users_view')) {
            
            // Ищем разрешение по имени
            $permission = Permission::where('name', 'users_view')->first();

            // Если разрешение найдено, добавляем связь
            if ($permission) {
                RoleHasPermissions::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        } 

        // Проверяем, отмечен ли чекбокс users_create
        if ($request->has('users_create')) {
            
            // Ищем разрешение по имени
            $permission = Permission::where('name', 'users_create')->first();

            // Если разрешение найдено, добавляем связь
            if ($permission) {
                RoleHasPermissions::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        } 

        // Проверяем, отмечен ли чекбокс users_update
        if ($request->has('users_update')) {
            
            // Ищем разрешение по имени
            $permission = Permission::where('name', 'users_update')->first();

            // Если разрешение найдено, добавляем связь
            if ($permission) {
                RoleHasPermissions::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        } 

        // Проверяем, отмечен ли чекбокс users_delete
        if ($request->has('users_delete')) {
            
            // Ищем разрешение по имени
            $permission = Permission::where('name', 'users_delete')->first();

            // Если разрешение найдено, добавляем связь
            if ($permission) {
                RoleHasPermissions::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        } 

        // Проверяем, отмечен ли чекбокс roles_viewAny
        if ($request->has('roles_viewAny')) {
            
            // Ищем разрешение по имени
            $permission = Permission::where('name', 'roles_viewAny')->first();

            // Если разрешение найдено, добавляем связь
            if ($permission) {
                RoleHasPermissions::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        } 

        // Проверяем, отмечен ли чекбокс roles_create
        if ($request->has('roles_create')) {
            
            // Ищем разрешение по имени
            $permission = Permission::where('name', 'roles_create')->first();

            // Если разрешение найдено, добавляем связь
            if ($permission) {
                RoleHasPermissions::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        } 

        // Проверяем, отмечен ли чекбокс roles_update
        if ($request->has('roles_update')) {
            
            // Ищем разрешение по имени
            $permission = Permission::where('name', 'roles_update')->first();

            // Если разрешение найдено, добавляем связь
            if ($permission) {
                RoleHasPermissions::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        } 

        // Проверяем, отмечен ли чекбокс roles_delete
        if ($request->has('roles_delete')) {
            
            // Ищем разрешение по имени
            $permission = Permission::where('name', 'roles_delete')->first();

            // Если разрешение найдено, добавляем связь
            if ($permission) {
                RoleHasPermissions::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        } 

        return redirect()->route('admin.roles')->with('success', 'Новая роль создана');
    }

    public function edit(Role $role)
    {
        return view('admin/users/roles/edit', compact('role'));
    }

    public function update(Role $role, RoleEditRequest $request)
    {
        $validated = $request->validated();

        $role->update([
            'name' => $validated['name'],
        ]);

        // Получаем разрешения
        $permission_show_admin = Permission::firstOrCreate(['name' => 'show_admin']);
        $permission_users_viewAny = Permission::firstOrCreate(['name' => 'users_viewAny']);
        $permission_users_view = Permission::firstOrCreate(['name' => 'users_view']);
        $permission_users_create = Permission::firstOrCreate(['name' => 'users_create']);
        $permission_users_update = Permission::firstOrCreate(['name' => 'users_update']);
        $permission_users_delete = Permission::firstOrCreate(['name' => 'users_delete']);

        $permission_roles_viewAny = Permission::firstOrCreate(['name' => 'roles_viewAny']);
        $permission_roles_create = Permission::firstOrCreate(['name' => 'roles_create']);
        $permission_roles_update = Permission::firstOrCreate(['name' => 'roles_update']);
        $permission_roles_delete = Permission::firstOrCreate(['name' => 'roles_delete']);

        // Проверяем состояние чекбокса show_admin
        if ($request->has('show_admin')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission_show_admin->id)->exists()) {
                $role->permissions()->attach($permission_show_admin->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission_show_admin->id);
        }

        // Проверяем состояние чекбокса users_viewAny
        if ($request->has('users_viewAny')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission_users_viewAny->id)->exists()) {
                $role->permissions()->attach($permission_users_viewAny->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission_users_viewAny->id);
        }

        // Проверяем состояние чекбокса users_view
        if ($request->has('users_view')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission_users_view->id)->exists()) {
                $role->permissions()->attach($permission_users_view->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission_users_view->id);
        }

        // Проверяем состояние чекбокса users_create
        if ($request->has('users_create')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission_users_create->id)->exists()) {
                $role->permissions()->attach($permission_users_create->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission_users_create->id);
        }

        // Проверяем состояние чекбокса users_update
        if ($request->has('users_update')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission_users_update->id)->exists()) {
                $role->permissions()->attach($permission_users_update->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission_users_update->id);
        }

        // Проверяем состояние чекбокса users_delete
        if ($request->has('users_delete')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission_users_delete->id)->exists()) {
                $role->permissions()->attach($permission_users_delete->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission_users_delete->id);
        }

        // Проверяем состояние чекбокса roles_viewAny
        if ($request->has('roles_viewAny')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission_roles_viewAny->id)->exists()) {
                $role->permissions()->attach($permission_roles_viewAny->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission_roles_viewAny->id);
        }

        // Проверяем состояние чекбокса roles_create
        if ($request->has('roles_create')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission_roles_create->id)->exists()) {
                $role->permissions()->attach($permission_roles_create->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission_roles_create->id);
        }

        // Проверяем состояние чекбокса roles_update
        if ($request->has('roles_update')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission_roles_update->id)->exists()) {
                $role->permissions()->attach($permission_roles_update->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission_roles_update->id);
        }

        // Проверяем состояние чекбокса roles_delete
        if ($request->has('roles_delete')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission_roles_delete->id)->exists()) {
                $role->permissions()->attach($permission_roles_delete->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission_roles_delete->id);
        }

        return redirect()->route('admin.roles')->with('success', 'Роль изменена');
    }

    public function delete(Role $role)
    {
        // Удаление всех связанных разрешений
        $role->permissions()->detach();

        // Удаление роли
        $role->delete();

        return redirect()->route('admin.roles')->with('success', 'Роль удалена');
    }
}
