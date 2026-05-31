<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!in_array($user->role, ['admin', 'ta'])) {
                if (!$user->profile_completed) {
                    // Prevent redirect loops
                    if (!$request->routeIs('profile.complete.*') && !$request->routeIs('logout')) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'error' => 'Profile update required',
                                'redirect' => route('profile.complete.index')
                            ], 403);
                        }
                        return redirect()->route('profile.complete.index');
                    }
                }
            }
        }

        return $next($request);
    }
}
