<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // جوه دالة store في AuthenticatedSessionController

        // ... بعد ما يعمل login
        $request->session()->regenerate();

        // التوجيه الذكي حسب الدور
        // ... بعد السطر $request->session()->regenerate();

        // استخدمنا $request->user() بدل auth()->user() عشان الـ VS Code يفهمها
        $user = $request->user();

        if ($user->role === 'doctor' || $user->role === 'ta' || $user->role === 'admin') {
            return redirect()->intended(route('staff.dashboard'));
        }

        return redirect()->intended(route('dashboard')); // يروح لطريق الطلاب
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
