<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $admin = $request->user();

        if (!$admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        foreach ($permissions as $permission) {
            if ($admin->hasPermission($permission)) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Forbidden: Anda tidak memiliki izin untuk mengakses ini'], 403);
    }
}
