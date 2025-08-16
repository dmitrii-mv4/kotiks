<?php

namespace App\Http\Middleware\Users;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Role;

class UsersIndexMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем разрешение через Gate
        if (Gate::allows('viewAny', User::class)) {
            return $next($request);
        }
        
        // Если доступ запрещен
        abort(403, 'Доступ запрещен');
    }
}
