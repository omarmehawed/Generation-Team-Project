<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizAdminController extends Controller
{
    /**
     * Display a listing of the quizzes.
     */
    public function index()
    {
        $quizzes = Quiz::withCount('questions', 'attempts')->latest()->get();
        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.quizzes.create');
    }

    /**
     * Store a newly created quiz in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'duration_minutes'   => 'required|integer|min:1',
            'total_marks'        => 'required|integer|min:0',
            'start_at'           => 'nullable|date',
            'end_at'             => 'nullable|date|after_or_equal:start_at',
            'require_fullscreen' => 'boolean',
            'shuffle_questions'  => 'boolean',
            'shuffle_options'    => 'boolean',
            'max_violations'     => 'required|integer|min:1',
            'auto_cancel_on_copy'  => 'boolean',
            'auto_cancel_on_paste' => 'boolean',
            'is_published'       => 'boolean',
            'targeted_roles'     => 'nullable|array',
        ]);

        $validated['created_by']         = Auth::id();
        $validated['require_fullscreen']  = $request->has('require_fullscreen');
        $validated['shuffle_questions']   = $request->has('shuffle_questions');
        $validated['shuffle_options']     = $request->has('shuffle_options');
        $validated['auto_cancel_on_copy'] = $request->has('auto_cancel_on_copy');
        $validated['auto_cancel_on_paste']= $request->has('auto_cancel_on_paste');
        $validated['is_published']        = $request->has('is_published');

        if (empty($validated['targeted_roles'])) {
            $validated['targeted_roles'] = ['all'];
        }

        Quiz::create($validated);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created successfully.');
    }

    /**
     * Show the form for editing the quiz.
     */
    public function edit(Quiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    /**
     * Update the quiz in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'duration_minutes'   => 'required|integer|min:1',
            'total_marks'        => 'required|integer|min:0',
            'start_at'           => 'nullable|date',
            'end_at'             => 'nullable|date|after_or_equal:start_at',
            'max_violations'     => 'required|integer|min:1',
            'auto_cancel_on_copy'  => 'boolean',
            'auto_cancel_on_paste' => 'boolean',
            'is_published'       => 'boolean',
            'targeted_roles'     => 'nullable|array',
        ]);

        $validated['require_fullscreen']  = $request->has('require_fullscreen');
        $validated['shuffle_questions']   = $request->has('shuffle_questions');
        $validated['shuffle_options']     = $request->has('shuffle_options');
        $validated['auto_cancel_on_copy'] = $request->has('auto_cancel_on_copy');
        $validated['auto_cancel_on_paste']= $request->has('auto_cancel_on_paste');
        $validated['is_published']        = $request->has('is_published');

        if (empty($validated['targeted_roles'])) {
            $validated['targeted_roles'] = ['all'];
        }

        $quiz->update($validated);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz updated successfully.');
    }

    /**
     * Remove the specified quiz from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted successfully.');
    }

    /**
     * Manage Questions for a Quiz
     */
    public function questions(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('admin.quizzes.questions', compact('quiz'));
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,written',
            'marks' => 'required|numeric|min:0',
            'is_required' => 'boolean',
            'options' => 'nullable|array',
            'options.*.text' => 'required_if:question_type,mcq|string',
            'correct_option' => 'required_if:question_type,mcq',
        ]);

        DB::transaction(function () use ($validated, $quiz, $request) {
            $question = $quiz->questions()->create([
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'marks' => $validated['marks'],
                'is_required' => $request->has('is_required'),
                'sort_order' => $quiz->questions()->count() + 1,
            ]);

            if ($validated['question_type'] === 'mcq' && !empty($validated['options'])) {
                foreach ($validated['options'] as $index => $opt) {
                    $question->options()->create([
                        'option_text' => $opt['text'],
                        'is_correct' => ($index == $validated['correct_option']),
                        'sort_order' => $index,
                    ]);
                }
            }
        });

        return back()->with('success', 'Question added.');
    }

    public function editQuestion(Quiz $quiz, QuizQuestion $question)
    {
        $question->load('options');
        return view('admin.quizzes.questions_edit', compact('quiz', 'question'));
    }

    public function updateQuestion(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,written',
            'marks' => 'required|numeric|min:0',
            'is_required' => 'boolean',
            'options' => 'nullable|array',
            'options.*.text' => 'required_if:question_type,mcq|string',
            'correct_option' => 'required_if:question_type,mcq',
        ]);

        DB::transaction(function () use ($validated, $question, $request) {
            $question->update([
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'marks' => $validated['marks'],
                'is_required' => $request->has('is_required'),
            ]);

            // If changing type to written, wipe options
            if ($validated['question_type'] === 'written') {
                $question->options()->delete();
            } 
            elseif ($validated['question_type'] === 'mcq' && !empty($validated['options'])) {
                $question->options()->delete(); // recreate options cleanly
                foreach ($validated['options'] as $index => $opt) {
                    $question->options()->create([
                        'option_text' => $opt['text'],
                        'is_correct' => ($index == $validated['correct_option']),
                        'sort_order' => $index,
                    ]);
                }
            }
        });

        return redirect()->route('admin.quizzes.questions', $quiz->id)->with('success', 'Question updated.');
    }

    public function destroyQuestion(Quiz $quiz, QuizQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }

    /**
     * Live Monitor
     */
    public function liveDashboard(Quiz $quiz)
    {
        // For the regular view loading
        return view('admin.quizzes.live', compact('quiz'));
    }

    public function liveDashboardData(Quiz $quiz)
    {
        $attempts = $quiz->attempts()->with('user')
            ->whereIn('status', ['in_progress', 'pending'])
            ->get()->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'user_name' => $attempt->user->name,
                    'status' => $attempt->status,
                    'started_at' => $attempt->started_at ? $attempt->started_at->diffForHumans() : null,
                    'last_activity' => $attempt->last_activity_at ? $attempt->last_activity_at->diffForHumans() : null,
                    'time_remaining' => $attempt->ends_at && $attempt->ends_at->isFuture() ? $attempt->ends_at->diffInSeconds(now()) : 0,
                    'current_step' => $attempt->current_step,
                    'violation_count' => $attempt->violation_count,
                ];
            });

        return response()->json(['attempts' => $attempts]);
    }

    public function attemptDetails(QuizAttempt $attempt)
    {
        $attempt->load(['user', 'quiz.questions.options']);
        $quiz = $attempt->quiz;

        $step = $attempt->current_step ?? 1;
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

        // Restore the per-attempt order
        $questions = collect($pageIds)
            ->filter(fn($id) => $questionsRaw->has($id))
            ->map(function ($id) use ($questionsRaw) {
                return $questionsRaw->get($id);
            })
            ->values();

        $answered = QuizAnswer::where('attempt_id', $attempt->id)
            ->whereIn('question_id', $pageIds)
            ->get()->keyBy('question_id');

        $deadline = ($attempt->extra_time_ends_at && $attempt->extra_time_ends_at->gt($attempt->ends_at))
            ? $attempt->extra_time_ends_at
            : $attempt->ends_at;

        $timeRemaining = max(0, now()->diffInSeconds($deadline, false));

        return response()->json([
            'attempt' => [
                'id' => $attempt->id,
                'user_name' => $attempt->user->name,
                'status' => $attempt->status,
                'current_step' => $step,
                'last_page' => $lastPage,
                'violation_count' => $attempt->violation_count,
                'time_remaining' => (int) $timeRemaining,
                'last_activity' => $attempt->last_activity_at ? $attempt->last_activity_at->diffForHumans() : 'No activity yet',
            ],
            'questions' => $questions,
            'answers' => $answered,
        ]);
    }

    /**
     * Show live observer page for an attempt
     */
    public function observeAttempt(QuizAttempt $attempt)
    {
        $attempt->load(['user', 'quiz']);
        return view('admin.quizzes.observe', compact('attempt'));
    }

    /**
     * Review submitted answers for an attempt
     */
    public function reviewAttempt(QuizAttempt $attempt)
    {
        $attempt->load(['user', 'quiz.questions.options', 'answers']);
        $quiz = $attempt->quiz;

        // Use the saved question order
        $questionIds = $attempt->question_order
            ?? $quiz->questions()->orderBy('sort_order')->pluck('id')->toArray();

        $questionsRaw = $quiz->questions()->with('options')
            ->whereIn('id', $questionIds)
            ->get()
            ->keyBy('id');

        $questions = collect($questionIds)
            ->filter(fn($id) => $questionsRaw->has($id))
            ->map(fn($id) => $questionsRaw->get($id))
            ->values();

        $answers = $attempt->answers->keyBy('question_id');

        return view('admin.quizzes.review', compact('attempt', 'quiz', 'questions', 'answers'));
    }
    
    public function forceEndAttempt(QuizAttempt $attempt)
    {
        $attempt->update([
            'status' => 'cancelled',
            'cancelled_reason' => 'Manually cancelled by Team Leader.',
            'submitted_at' => now(),
        ]);
        return back()->with('success', 'Attempt cancelled permanently.');
    }

    /**
     * Grading / Results Dashboard
     */
    public function results(Quiz $quiz)
    {
        // Get all attempts (completed and in-progress)
        $allAttempts = $quiz->attempts()->with('user')->orderBy('attempt_number')->get();
        
        // Separate current attempts for the table (historical results)
        $attempts = $allAttempts->whereNotIn('status', ['in_progress', 'pending'])->values();
        
        // Prepare results for each attempt (unanswered questions)
        foreach ($attempts as $attempt) {
            $answeredIds = QuizAnswer::where('attempt_id', $attempt->id)->pluck('question_id');
            $attempt->unanswered_questions = $quiz->questions()
                ->whereNotIn('id', $answeredIds)
                ->get(['id', 'question_text', 'marks']);
        }

        // --- PARTICIPATION STATUS LOGIC ---
        // 1. Identify all eligible members (excluding admins to keep lists clean)
        $eligibleUsers = User::where('role', '!=', 'admin')
            ->with('teamMemberships')
            ->get()
            ->filter(fn($u) => $quiz->isAvailableFor($u))
            ->values();

        // 2. Map users to their LATEST attempt
        $latestAttempts = $allAttempts->groupBy('user_id')->map(fn($group) => $group->last());

        $participation = [
            'completed' => [],
            'in_progress' => [],
            'not_started' => []
        ];

        foreach ($eligibleUsers as $user) {
            $attempt = $latestAttempts->get($user->id);
            
            $memberInfo = [
                'user' => $user,
                'role' => $user->teamMemberships->first()?->technical_role ?? 'Member',
                'attempt' => $attempt
            ];

            if (!$attempt) {
                $participation['not_started'][] = $memberInfo;
            } elseif (in_array($attempt->status, ['in_progress', 'pending'])) {
                $participation['in_progress'][] = $memberInfo;
            } else {
                $participation['completed'][] = $memberInfo;
            }
        }

        return view('admin.quizzes.results', compact('quiz', 'attempts', 'participation'));
    }

    public function grantExtraTime(Request $request, QuizAttempt $attempt)
    {
        $request->validate([
            'extra_minutes' => 'required|integer|min:1|max:120',
            'notes' => 'nullable|string|max:500',
        ]);

        $extraMinutes = (int) $request->extra_minutes;
        $newDeadline = now()->addMinutes($extraMinutes);

        $attempt->update([
            'status' => 'in_progress',
            'extra_time_minutes' => $attempt->extra_time_minutes + $extraMinutes,
            'extra_time_ends_at' => $newDeadline,
            'extra_time_granted_by' => Auth::id(),
            'extra_time_granted_at' => now(),
            'extra_time_notes' => $request->notes,
            'submitted_at' => null,
        ]);

        return back()->with('success', "Extra {$extraMinutes} minutes granted. The member can now resume their exam.");
    }

    public function gradingDashboard(Quiz $quiz)
    {
        // Get all answers from this quiz that are written, and not graded yet
        $pendingAnswers = QuizAnswer::with(['attempt.user', 'question'])
            ->whereHas('attempt', function($q) use ($quiz) {
                $q->where('quiz_id', $quiz->id)->whereIn('status', ['submitted', 'auto_submitted']);
            })
            ->whereHas('question', function($q) {
                $q->where('question_type', 'written');
            })
            ->whereNull('reviewed_by')
            ->get();

        return view('admin.quizzes.grading', compact('quiz', 'pendingAnswers'));
    }

    public function saveGrade(Request $request, QuizAnswer $answer)
    {
        $validated = $request->validate([
            'marks_awarded' => 'required|numeric|min:0|max:'.$answer->question->marks,
        ]);

        $answer->update([
            'marks_awarded' => $validated['marks_awarded'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Recalculate Attempt score
        $attempt = $answer->attempt;
        $totalMarks = $attempt->answers()->sum('marks_awarded');
        $attempt->update(['score' => $totalMarks]);

        return back()->with('success', 'Grade saved successfully.');
    }

    /**
     * Retry Requests Management
     */
    public function retryRequestsDashboard()
    {
        $requests = \App\Models\QuizRetryRequest::with(['quiz', 'user', 'attempt'])
            ->latest()
            ->paginate(20);

        return view('admin.quizzes.retries', compact('requests'));
    }

    public function reviewRetryRequest(Request $request, \App\Models\QuizRetryRequest $retryRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $retryRequest->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes']
        ]);

        return back()->with('success', 'Retry request has been ' . $validated['status']);
    }
}
