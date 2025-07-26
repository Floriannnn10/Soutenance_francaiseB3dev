<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userRole = Auth::user()->roles->first();
        if (!$userRole || $userRole->code !== $role) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
