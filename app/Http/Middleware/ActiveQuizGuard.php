<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizAttempt;
use App\Models\QuizViolation;
use App\Models\User;

class ActiveQuizGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        // Retrieve active attempt
        $activeAttempt = QuizAttempt::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        // Allow Team Leaders / System Owners to bypass being trapped
        $user = Auth::user();
        if ($activeAttempt && $user instanceof User && $user->hasPermission('manage_quizzes')) {
            return $next($request);
        }

        if ($activeAttempt) {
            // Check if current route is part of the exam workflow
            // Exclude logout.
            $allowedRoutes = [
                'quizzes/*/attempt',
                'quizzes/*/api/*',
                'logout'
            ];

            $isAllowed = false;
            foreach ($allowedRoutes as $routePattern) {
                if ($request->is($routePattern)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'Action blocked during an active exam.'], 403);
                }

                // Record violation
                QuizViolation::create([
                    'attempt_id' => $activeAttempt->id,
                    'violation_type' => 'illegal_navigation',
                    'details' => 'Tried to access: ' . $request->path(),
                ]);
                $activeAttempt->increment('violation_count');

                return redirect()->route('quizzes.attempt', $activeAttempt->quiz_id)
                    ->with('warning', 'Leaving the exam page is not allowed. A violation has been recorded.');
            }
        }

        return $next($request);
    }
}
