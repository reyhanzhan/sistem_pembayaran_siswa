<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check() || !in_array(auth()->user()->peran, $roles)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $next($request);
    }
}