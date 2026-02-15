<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        // 1. لو مش مسجل دخول
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. لو أدمن -> عدي (مفتاح ماستر)
        if (Auth::user()->role === 'admin') {
            return $next($request);
        }

        // 3. لو معوش الصلاحية -> اطرد
        if (!Auth::user()->hasPermission($permission)) {
            abort(403, '⛔ ACCESS DENIED');
        }

        return $next($request);
    }
}
