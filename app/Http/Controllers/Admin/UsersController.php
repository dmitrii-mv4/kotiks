<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Users\UserCreateRequest;
use App\Http\Requests\Users\UserEditRequest;
use App\Models\User;
use App\Models\Role;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::get();

        return view('admin/users/index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::get();

        return view('admin/users/create', compact('roles'));
    }

    public function store(UserCreateRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users')->with('success', 'Пользователь добавлен');
    }

    public function edit(User $user)
    {
        // $this->authorize('update', $user);

        // $this->authorize('update', \App\Models\User::class);

        $roles = Role::get();

        return view('admin/users/edit', compact('user', 'roles'));
    }

    public function update(User $user, UserEditRequest $request)
    {
        $validated = $request->validated();

        // $this->authorize('update', \App\Models\User::class);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        // Обновляем пароль только если он указан и не пустой
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users')->with('success', 'Пользователь обновлён');
    }

    public function delete(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Пользователь удалён');
    }
}
