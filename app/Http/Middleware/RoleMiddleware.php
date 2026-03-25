<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = array_filter(
            array_map(fn (string $role) => UserRole::tryFrom($role), $roles)
        );

        if (empty($allowedRoles) || !auth()->check() || !in_array(auth()->user()->role, $allowedRoles)) {
            abort(403);
        }

        return $next($request);
    }
}
