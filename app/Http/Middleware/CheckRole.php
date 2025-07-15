<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        if (!in_array($request->user()->role, $roles)) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}