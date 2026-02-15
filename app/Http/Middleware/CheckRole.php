<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // <--- ده السطر السحري اللي كان ناقص

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // دلوقتي هو عارف مين هي Auth
        if (!Auth::check()) {
            return redirect('login');
        }

        if (!in_array(Auth::user()->role, $roles)) {
            // لو الدور مش مسموح، نرجعه للداشبورد العادية
            return redirect('/dashboard');
        }

        return $next($request);
    }
}