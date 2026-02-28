<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }

        $user = auth()->user();

        if ($user->status !== 'active') {
            return redirect('/login')->with('error', 'Your account is inactive.');
        }

        $allowedRoles = $roles === [] ? ['admin'] : $roles;

        if (! in_array($user->role, $allowedRoles, true)) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}
