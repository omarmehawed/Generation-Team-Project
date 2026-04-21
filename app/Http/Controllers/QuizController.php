<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\QuizViolation;
use App\Models\QuizRetryRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $quizzes = Quiz::where('is_published', true)->get()->filter(function (Quiz $quiz) use ($user) {
            return $quiz->isAvailableFor($user);
        });
        return view('quizzes.index', compact('quizzes'));
    }

    public function show(Quiz $quiz)
    {
        if (!$quiz->is_published) {
            abort(404);
        }

        /** @var User $user */
        $user = Auth::user();

        if (!$quiz->isAvailableFor($user)) {
            abort(403);
        }

        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->latest('attempt_number')
            ->first();

        $hasApprovedRetry = false;
        if ($attempt && in_array($attempt->status, ['disqualified', 'cancelled', 'submitted'])) {
            $hasApprovedRetry = QuizRetryRequest::where('attempt_id', $attempt->id)
                ->where('status', 'approved')
                ->exists();
        }

        return view('quizzes.show', compact('quiz', 'attempt', 'hasApprovedRetry'));
    }

    public function startAttempt(Quiz $quiz)
    {
        if (!$quiz->is_published) {
            abort(404);
        }

        /** @var User $user */
        $user = Auth::user();

        if (!$quiz->isAvailableFor($user)) {
            abort(403);
        }

        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->latest('attempt_number')
            ->first();

        $canStartNew = false;
        $attemptNumber = 1;

        if (!$attempt) {
            $canStartNew = true;
        } elseif ($attempt->status === 'in_progress') {
            return redirect()->route('quizzes.attempt', $quiz->id);
        } else {
            $approvedRetry = QuizRetryRequest::where('attempt_id', $attempt->id)
                ->where('status', 'approved')
                ->exists();

            if ($approvedRetry) {
                $canStartNew = true;
                $attemptNumber = $attempt->attempt_number + 1;
            }
        }

        if ($canStartNew) {
            // Generate shuffle order once at attempt start (consistent across refreshes)
            $allQuestionIds = $quiz->questions()->orderBy('sort_order')->pluck('id')->toArray();
            if ($quiz->shuffle_questions) {
                shuffle($allQuestionIds);
            }

            QuizAttempt::create([
                'quiz_id'        => $quiz->id,
                'user_id'        => Auth::id(),
                'status'         => 'in_progress',
                'started_at'     => now(),
                'ends_at'        => now()->addMinutes($quiz->duration_minutes),
                'current_step'   => 1,
                'attempt_number' => $attemptNumber,
                'question_order' => $allQuestionIds, // always saved, shuffled or not
            ]);
        } else {
            return redirect()->route('quizzes.show', $quiz->id)
                ->with('error', 'You cannot start this quiz again.');
        }

        return redirect()->route('quizzes.attempt', $quiz->id);
    }

    public function attempt(Quiz $quiz)
    {
        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            return redirect()->route('quizzes.show', $quiz->id)
                ->with('error', 'No active attempt found. It may have been completed, cancelled, or disqualified.');
        }

        $deadline = ($attempt->extra_time_ends_at && $attempt->extra_time_ends_at->gt($attempt->ends_at))
            ? $attempt->extra_time_ends_at
            : $attempt->ends_at;

        if (now()->greaterThanOrEqualTo($deadline)) {
            $this->finalizeAttempt($attempt, 'auto_submitted');
            return redirect()->route('quizzes.show', $quiz->id)->with('info', 'Time is up. Your exam was auto-submitted.');
        }

        return view('quizzes.attempt', compact('quiz', 'attempt'));
    }

    // --- API Endpoints ---

    public function getQuestionsStep(Request $request, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $attempt->update(['last_activity_at' => now()]);

        $step = (int) $request->input('step', 1);
        $perPage = 10;

        // Use the saved question order (generated once at attempt start)
        $questionIds = $attempt->question_order
            ?? $quiz->questions()->orderBy('sort_order')->pluck('id')->toArray();

        $totalQuestions = count($questionIds);
        $lastPage       = max(1, (int) ceil($totalQuestions / $perPage));
        $pageIds        = array_slice($questionIds, ($step - 1) * $perPage, $perPage);

        // Fetch questions for this page, keyed by id so we can reorder
        $questionsRaw = $quiz->questions()->with('options')
            ->whereIn('id', $pageIds)
            ->get()
            ->keyBy('id');

        // Restore the per-attempt order and optionally shuffle options
        $questions = collect($pageIds)
            ->filter(fn($id) => $questionsRaw->has($id))
            ->map(function ($id) use ($questionsRaw, $quiz) {
                $q = clone $questionsRaw->get($id);
                if ($quiz->shuffle_options && $q->options->isNotEmpty()) {
                    $q->setRelation('options', $q->options->shuffle());
                }
                return $q;
            })
            ->values();

        $answered = QuizAnswer::where('attempt_id', $attempt->id)
            ->whereIn('question_id', $pageIds)
            ->get()->keyBy('question_id');

        $attempt->update(['current_step' => $step]);

        $deadline = ($attempt->extra_time_ends_at && $attempt->extra_time_ends_at->gt($attempt->ends_at))
            ? $attempt->extra_time_ends_at
            : $attempt->ends_at;

        $timeRemaining = max(0, now()->diffInSeconds($deadline, false));

        return response()->json([
            'questions'     => $questions->toArray(),
            'current_page'  => $step,
            'last_page'     => $lastPage,
            'total'         => $totalQuestions,
            'time_remaining'=> (int) $timeRemaining,
            'answers'       => $answered,
        ]);
    }


    public function saveAnswer(Request $request, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $attempt->update(['last_activity_at' => now()]);

        $question_id        = $request->input('question_id');
        $selected_option_id = $request->input('selected_option_id');
        $text_answer        = $request->input('text_answer');

        $question      = $quiz->questions()->findOrFail($question_id);
        $is_correct    = null;
        $marks_awarded = 0;

        if ($question->question_type === 'mcq' && $selected_option_id) {
            $option = $question->options()->find($selected_option_id);
            if ($option) {
                $is_correct    = $option->is_correct;
                $marks_awarded = $is_correct ? $question->marks : 0;
            }
        }

        QuizAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $question_id],
            [
                'selected_option_id' => $selected_option_id,
                'text_answer'        => $text_answer,
                'is_correct'         => $is_correct,
                'marks_awarded'      => $marks_awarded,
            ]
        );

        $totalMarks = QuizAnswer::where('attempt_id', $attempt->id)->sum('marks_awarded');
        $attempt->update(['score' => $totalMarks]);

        return response()->json(['success' => true]);
    }

    public function submitAttempt(Request $request, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->firstOrFail();

        if ($attempt->status === 'in_progress') {
            $this->finalizeAttempt($attempt, 'submitted');
        }

        return response()->json([
            'success'  => true,
            'redirect' => route('quizzes.result', $quiz->id),
        ]);
    }

    public function result(Quiz $quiz)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$quiz->is_published && !$user->hasPermission('manage_quizzes')) {
            abort(404);
        }

        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->whereNotIn('status', ['in_progress', 'pending'])
            ->latest('attempt_number')
            ->first();

        if (!$attempt) {
            return redirect()->route('quizzes.show', $quiz->id)
                ->with('error', 'No completed attempt found.');
        }

        $questions = $quiz->questions()->with('options')->orderBy('sort_order')->get();

        $answers = QuizAnswer::where('attempt_id', $attempt->id)
            ->get()->keyBy('question_id');

        $correct    = 0;
        $wrong      = 0;
        $unanswered = 0;

        foreach ($questions as $q) {
            $ans = $answers->get($q->id);
            if (!$ans) {
                $unanswered++;
            } elseif ($q->question_type === 'mcq') {
                $ans->is_correct ? $correct++ : $wrong++;
            }
        }

        return view('quizzes.result', compact('quiz', 'attempt', 'questions', 'answers', 'correct', 'wrong', 'unanswered'));
    }

    public function reportViolation(Request $request, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $type = $request->input('violation_type');

        QuizViolation::create([
            'attempt_id'     => $attempt->id,
            'violation_type' => $type,
            'details'        => $request->input('details'),
        ]);

        $attempt->increment('violation_count');

        $shouldCancel = false;

        if ($quiz->auto_cancel_on_copy && $type === 'copy') {
            $shouldCancel = true;
        } elseif ($quiz->auto_cancel_on_paste && $type === 'paste') {
            $shouldCancel = true;
        } elseif ($attempt->violation_count >= $quiz->max_violations) {
            $shouldCancel = true;
        }

        if ($shouldCancel) {
            $this->finalizeAttempt($attempt, 'disqualified', 'Cheating or excessive violations.');
            return response()->json(['cancelled' => true, 'redirect' => route('quizzes.show', $quiz->id)]);
        }

        return response()->json(['cancelled' => false, 'violations' => $attempt->violation_count]);
    }

    private function finalizeAttempt(QuizAttempt $attempt, string $status, ?string $reason = null): void
    {
        $totalMarks = QuizAnswer::where('attempt_id', $attempt->id)->sum('marks_awarded');
        $attempt->update([
            'status'           => $status,
            'submitted_at'     => now(),
            'score'            => $totalMarks,
            'cancelled_reason' => $reason,
        ]);
    }

    public function submitRetryRequest(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->latest('attempt_number')
            ->firstOrFail();

        if (in_array($attempt->status, ['in_progress', 'pending'])) {
            return back()->with('error', 'You cannot request a retry for an active attempt.');
        }

        $existing = QuizRetryRequest::where('attempt_id', $attempt->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have a pending retry request.');
        }

        QuizRetryRequest::create([
            'quiz_id'    => $quiz->id,
            'user_id'    => Auth::id(),
            'attempt_id' => $attempt->id,
            'reason'     => $validated['reason'],
            'status'     => 'pending',
        ]);

        return back()->with('success', 'Retry request submitted successfully. Please wait for an administrator to review it.');
    }
}
