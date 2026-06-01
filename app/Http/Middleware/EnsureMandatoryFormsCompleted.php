<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Form;

class EnsureMandatoryFormsCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Staff users are exempt (admin, ta, doctor, etc.)
            if (in_array($user->role, ['admin', 'ta'])) {
                return $next($request);
            }

            // If profile is not completed yet, let EnsureProfileCompleted handle it first
            if (!$user->profile_completed) {
                return $next($request);
            }

            // Check for pending mandatory forms that match the user's gender
            $pendingForm = Form::mandatoryForUser($user)->first();

            if ($pendingForm) {
                // Prevent redirect loops — allow mandatory form routes, logout, and profile completion
                if (
                    $request->routeIs('forms.mandatory.*') ||
                    $request->routeIs('profile.complete.*') ||
                    $request->routeIs('logout')
                ) {
                    return $next($request);
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Mandatory form required',
                        'redirect' => route('forms.mandatory.show', $pendingForm->id)
                    ], 403);
                }

                return redirect()->route('forms.mandatory.show', $pendingForm->id);
            }
        }

        return $next($request);
    }
}
