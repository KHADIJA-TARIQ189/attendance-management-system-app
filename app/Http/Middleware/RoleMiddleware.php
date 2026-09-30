<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    // Usage in routes: ->middleware('role:admin,teacher')
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            abort(403, 'You are not authorized to view this page.');
        }
        return $next($request);
    }
}
