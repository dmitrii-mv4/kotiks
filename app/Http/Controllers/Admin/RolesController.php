<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Users\Roles\RoleCreateRequest;
use App\Http\Requests\Users\Roles\RoleEditRequest;
use App\Models\Role;
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
        $roles = Role::get();

        return view('admin/users/roles/index', compact('roles'));
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

        // Получаем разрешение "show_admin"
        $permission = Permission::firstOrCreate(['name' => 'show_admin']);

        // Проверяем состояние чекбокса
        if ($request->has('show_admin')) {
            // Добавляем разрешение если связи еще нет
            if (!$role->permissions()->where('permission_id', $permission->id)->exists()) {
                $role->permissions()->attach($permission->id);
            }
        } else {
            // Удаляем разрешение если оно было
            $role->permissions()->detach($permission->id);
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
