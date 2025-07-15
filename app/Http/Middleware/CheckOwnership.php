<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOwnership
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if ($user->role === 'admin') {
            return $next($request);
        }

        $userId = $request->route('user');
        
        if ($userId && $user->id != $userId) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}