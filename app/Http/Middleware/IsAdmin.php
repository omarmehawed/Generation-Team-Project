<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. تأكد إن اليوزر مسجل دخول أصلاً
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. هنا بنعرف المتغير $user (ده السطر اللي كان ناقص أو عامل المشكلة)
        $user = Auth::user();

        // 3. الشرط: لو الرتبة "أدمن" OR "دكتور/معيد" معاه صلاحية manage_users
        if ($user->role === 'admin' || $user->hasPermission('manage_users')) {
            return $next($request);
        }

        // 4. لو الشروط محصلتش -> اطرد
        abort(403, 'ACCESS DENIED: You do not have permission to view this page.');
    }
}
