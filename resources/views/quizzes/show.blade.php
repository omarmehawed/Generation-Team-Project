@extends('layouts.batu')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('quizzes.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-black font-bold text-sm mb-2 inline-block"><i class="fas fa-arrow-left"></i> All Evaluations</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <!-- Header -->
        <div class="bg-gradient-to-br from-gray-900 to-black p-10 relative">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-yellow-500 rounded-full blur-3xl opacity-10"></div>
            
            <h1 class="text-4xl font-black text-white relative z-10 mb-2">{{ $quiz->title }}</h1>
            <p class="text-gray-400 relative z-10 text-lg">{{ $quiz->description ?? 'No description provided.' }}</p>
        </div>

        <div class="p-10">
            @if($attempt && !in_array($attempt->status, ['in_progress', 'pending']) && !$hasApprovedRetry)
                <!-- Already taken -->
                <div class="{{ $attempt->status =='disqualified' || $attempt->status == 'cancelled' ? 'bg-red-50 border-red-500' : 'bg-green-50 border-green-500' }} border-2 rounded-2xl p-6 text-center transform transition hover:scale-[1.01]">
                    <div class="w-20 h-20 {{ $attempt->status =='disqualified' || $attempt->status == 'cancelled' ? 'bg-red-500 border-red-200' : 'bg-green-500 border-green-200' }} text-white rounded-full flex items-center justify-center mx-auto mb-4 border-4">
                        <i class="fas {{ $attempt->status =='disqualified' || $attempt->status == 'cancelled' ? 'fa-ban' : 'fa-check-double' }} text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black {{ $attempt->status =='disqualified' || $attempt->status == 'cancelled' ? 'text-red-800' : 'text-green-800' }} mb-2">
                        {{ $attempt->status == 'disqualified' || $attempt->status == 'cancelled' ? 'Evaluation Disqualified / Cancelled' : 'Evaluation Completed' }}
                    </h3>
                    <p class="{{ $attempt->status =='disqualified' || $attempt->status == 'cancelled' ? 'text-red-700' : 'text-green-700' }} font-bold mb-4">
                        {{ $attempt->status == 'disqualified' || $attempt->status == 'cancelled' ? 'Your attempt was terminated due to a violation.' : 'You have already completed this evaluation.' }}
                    </p>
                    <div class="inline-block bg-white dark:bg-gray-800 px-6 py-3 rounded-xl shadow-md border {{ $attempt->status =='disqualified' || $attempt->status == 'cancelled' ? 'border-red-100' : 'border-green-100' }}">
                        <span class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest text-xs block mb-1">Your Score</span>
                        <span class="text-4xl font-black {{ $attempt->status =='disqualified' || $attempt->status == 'cancelled' ? 'text-red-600' : 'text-gray-900' }}">{{ $attempt->score }} <span class="text-xl text-gray-400">/ {{ $quiz->total_marks }}</span></span>
                    </div>
                    
                    @if(in_array($attempt->status, ['disqualified', 'cancelled']))\
                        <div class="mt-4 p-4 bg-white/60 rounded-xl text-left border border-red-100 inline-block max-w-lg mx-auto">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Reason for Disqualification:</p>
                            <p class="text-red-900 font-black text-lg">{{ $attempt->cancelled_reason ?? 'Repeated rule violations.' }}</p>
                        </div>
                    @else
                        {{-- View results link for submitted/auto_submitted --}}
                        <div class="mt-5">
                            <a href="{{ route('quizzes.result', $quiz->id) }}" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-black text-white font-black px-6 py-3 rounded-xl shadow-lg transition hover:-translate-y-0.5">
                                <i class="fas fa-list-check"></i> View Answer Review
                            </a>
                        </div>
                    @endif
                </div>

                @if(in_array($attempt->status, ['disqualified', 'cancelled', 'submitted']) && $attempt->score == 0)
                    @php
                        $pendingRequest = \App\Models\QuizRetryRequest::where('attempt_id', $attempt->id)->where('status', 'pending')->first();
                        $rejectedRequest = \App\Models\QuizRetryRequest::where('attempt_id', $attempt->id)->where('status', 'rejected')->latest('id')->first();
                    @endphp

                    @if($pendingRequest)
                        <div class="mt-8 bg-blue-50 border-2 border-blue-400 rounded-2xl p-6 text-center">
                            <i class="fas fa-clock text-blue-500 text-4xl mb-3"></i>
                            <h4 class="text-xl font-black text-blue-900">Retry Request Pending</h4>
                            <p class="text-blue-700 font-bold mt-2">Your request to retake this evaluation is currently being reviewed by an administrator. Please check back later.</p>
                        </div>
                    @else
                        <div class="mt-8 bg-gray-50 dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-gray-200 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center shrink-0">
                                    <i class="fas fa-undo-alt text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-xl font-black text-gray-800 dark:text-gray-200">Request a Retake</h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm font-bold">If you believe this was a technical error or an unfair disqualification, you can request a retry.</p>
                                </div>
                            </div>
                            
                            @if($rejectedRequest)
                                <div class="mb-4 bg-red-50 p-4 border border-red-200 rounded-xl text-left">
                                    <p class="text-red-800 font-bold mb-1"><i class="fas fa-times-circle mr-2"></i> Previous Request Rejected</p>
                                    @if($rejectedRequest->admin_notes)
                                        <p class="text-sm text-red-600 italic">"{{ $rejectedRequest->admin_notes }}"</p>
                                    @endif
                                </div>
                            @endif

                            <form action="{{ route('quizzes.retry', $quiz->id) }}" method="POST">
                                @csrf
                                <textarea name="reason" required rows="3" placeholder="Explain the technical issue or reason you should be granted a retry..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 mb-4 text-sm"></textarea>
                                <div class="text-right">
                                    <button type="submit" class="bg-gray-800 text-white hover:bg-black font-bold py-2 px-6 rounded-lg uppercase tracking-wider text-sm transition">Submit Request <i class="fas fa-paper-plane ml-1"></i></button>
                                </div>
                            </form>
                        </div>
                    @endif
                @endif
            @else
                <!-- Pre-exam Instructions -->
                <h3 class="text-xl font-black text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2"><i class="fas fa-clipboard-list text-yellow-600"></i> Exam Rules / Instructions</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl shrink-0"><i class="fas fa-hourglass-half"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</p>
                            <p class="font-black text-gray-800 dark:text-gray-200 text-lg">{{ $quiz->duration_minutes }} Minutes</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-xl shrink-0"><i class="fas fa-star"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Marks</p>
                            <p class="font-black text-gray-800 dark:text-gray-200 text-lg">{{ $quiz->total_marks }} Marks</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center text-xl shrink-0"><i class="fas fa-desktop"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fullscreen</p>
                            <p class="font-black text-gray-800 dark:text-gray-200 text-lg">{{ $quiz->require_fullscreen ? 'Strict Required' : 'Not Required' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center text-xl shrink-0"><i class="fas fa-exclamation-triangle"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Max Violations</p>
                            <p class="font-black text-gray-800 dark:text-gray-200 text-lg">{{ $quiz->max_violations }} Warning(s)</p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-xl mb-8 space-y-4 shadow-sm">
                    <h4 class="font-black text-red-800 text-lg"><i class="fas fa-shield-alt"></i> Anti-Cheat Systems Active</h4>
                    <ul class="space-y-2 text-red-700 font-bold text-sm">
                        @if($quiz->require_fullscreen)<li><i class="fas fa-times-circle mr-2 opacity-50"></i> Exiting fullscreen mode will result in a recorded violation.</li>@endif
                        <li><i class="fas fa-times-circle mr-2 opacity-50"></i> Switching browser tabs, minimizing the window, or losing focus is strictly monitored.</li>
                        @if($quiz->auto_cancel_on_copy)<li><i class="fas fa-times-circle mr-2 opacity-50"></i> Copying ANY text from the exam will lead to IMMEDIATE disqualification.</li>@endif
                        @if($quiz->auto_cancel_on_paste)<li><i class="fas fa-times-circle mr-2 opacity-50"></i> Pasting ANY text into answers will lead to IMMEDIATE disqualification.</li>@endif
                        <li><i class="fas fa-times-circle mr-2 opacity-50"></i> Leaving the page once the exam starts will terminate your attempt.</li>
                    </ul>
                </div>

                <form action="{{ route('quizzes.start', $quiz->id) }}" method="POST">
                    @csrf
                    <div class="flex flex-col sm:flex-row items-center gap-4 pb-2">
                        <label class="flex items-start gap-3 cursor-pointer group flex-1">
                            <input type="checkbox" required class="w-6 h-6 mt-0.5 text-green-600 border-2 border-gray-300 dark:border-gray-600 rounded focus:ring-green-500 group-hover:border-green-500 transition">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 leading-tight">I have read and understood all instructions. I acknowledge that cheating or violating rules will result in immediate termination of the exam and a zero score.</span>
                        </label>
                        <button type="submit" class="btn-royal-gold px-8 py-4 rounded-2xl font-black text-lg shadow-xl hover:-translate-y-1 w-full sm:w-auto shrink-0 uppercase tracking-wider">
                            Start Exam <i class="fas fa-chevron-right ml-2 opacity-70"></i>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
