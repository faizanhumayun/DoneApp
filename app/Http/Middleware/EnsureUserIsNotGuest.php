<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->companies->isNotEmpty()) {
            $role = $user->companies->first()->pivot->role ?? null;

            if ($role === 'guest') {
                abort(403, 'Guests do not have access to this section.');
            }
        }

        return $next($request);
    }
}
