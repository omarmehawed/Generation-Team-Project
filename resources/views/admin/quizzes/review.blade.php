@extends('layouts.batu')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-gray-800">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-4 sm:gap-5">
            <a href="{{ route('admin.quizzes.results', $quiz->id) }}" class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-100 text-gray-400 hover:text-indigo-600 hover:shadow-lg transition shadow-sm">
                <i class="fas fa-arrow-left text-sm sm:text-base"></i>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 flex items-center gap-2 sm:gap-3">
                    <span class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                        <i class="fas fa-file-invoice text-sm sm:text-base"></i>
                    </span>
                    Review: {{ $attempt->user->name }}
                </h1>
                <p class="text-gray-500 font-bold text-xs sm:text-sm mt-1">Quiz: {{ $quiz->title }} • Attempt #{{ $attempt->attempt_number }}</p>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <div class="px-4 py-2 sm:px-6 sm:py-3 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3">
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Final Score</p>
                    <p class="text-xl sm:text-2xl font-black {{ $attempt->score >= ($quiz->total_marks/2) ? 'text-green-600' : 'text-red-500' }}">
                        {{ $attempt->score }} <span class="text-xs text-gray-400">/ {{ $quiz->total_marks }}</span>
                    </p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl {{ $attempt->score >= ($quiz->total_marks/2) ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} flex items-center justify-center text-lg sm:text-xl">
                    <i class="fas {{ $attempt->score >= ($quiz->total_marks/2) ? 'fa-award' : 'fa-exclamation-circle' }}"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Summary Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-10">
        <div class="p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2.5rem] bg-white border border-gray-100 shadow-xl shadow-gray-100/50">
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Status</p>
            <p class="font-black text-gray-900 text-sm sm:text-base uppercase">{{ $attempt->status }}</p>
        </div>
        <div class="p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2.5rem] bg-white border border-gray-100 shadow-xl shadow-gray-100/50">
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Questions</p>
            <p class="font-black text-gray-900 text-sm sm:text-base">{{ $quiz->questions->count() }} Total</p>
        </div>
        <div class="p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2.5rem] bg-white border border-gray-100 shadow-xl shadow-gray-100/50">
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Violations</p>
            <p class="font-black {{ $attempt->violation_count > 0 ? 'text-red-600' : 'text-emerald-600' }} text-sm sm:text-base">{{ $attempt->violation_count }} Detected</p>
        </div>
        <div class="p-4 sm:p-6 rounded-[1.5rem] sm:rounded-[2.5rem] bg-white border border-gray-100 shadow-xl shadow-gray-100/50">
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Duration</p>
            <p class="font-black text-gray-900 text-sm sm:text-base">
                @if($attempt->started_at && $attempt->submitted_at)
                    {{ $attempt->started_at->diffForHumans($attempt->submitted_at, true) }}
                @else
                    N/A
                @endif
            </p>
        </div>
    </div>

    <!-- Questions Review List -->
    <div class="space-y-8">
        @foreach($questions as $index => $q)
            @php
                $answer = $answers->get($q->id);
                $isCorrect = $answer && $answer->is_correct;
                $isUnanswered = !$answer;
            @endphp
            <div class="bg-white rounded-[2rem] sm:rounded-[3rem] border-2 {{ $isUnanswered ? 'border-gray-100' : ($isCorrect ? 'border-emerald-100 shadow-emerald-50' : 'border-red-100 shadow-red-50') }} shadow-xl overflow-hidden group hover:border-indigo-200 transition duration-300">
                <!-- Question Header -->
                <div class="p-6 sm:p-8 border-b {{ $isUnanswered ? 'bg-gray-50/30' : ($isCorrect ? 'bg-emerald-50/30' : 'bg-red-50/30') }} border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-white border border-gray-100 shadow-sm flex items-center justify-center font-black text-gray-400 text-sm sm:text-lg">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-widest {{ $isUnanswered ? 'text-gray-400' : ($isCorrect ? 'text-emerald-500' : 'text-red-500') }}">
                                {{ $isUnanswered ? 'Unanswered' : ($isCorrect ? 'Correct Answer' : 'Incorrect Answer') }}
                            </span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="bg-indigo-600 text-white text-[10px] px-2 py-0.5 rounded font-black uppercase tracking-tighter">{{ $q->question_type }}</span>
                                <span class="text-xs font-bold text-gray-500">{{ $q->marks }} Points</span>
                            </div>
                        </div>
                    </div>

                    @if(!$isUnanswered)
                        <div class="flex items-center gap-2 px-4 py-2 rounded-xl {{ $isCorrect ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white' }} text-xs font-black uppercase tracking-widest shadow-lg shadow-gray-200">
                            <i class="fas {{ $isCorrect ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $answer->marks_awarded }} Marks Gained
                        </div>
                    @else
                        <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-400 text-white text-xs font-black uppercase tracking-widest">
                            <i class="fas fa-minus"></i>
                            0 Marks Gained
                        </div>
                    @endif
                </div>

                <div class="p-6 sm:p-10">
                    <p class="font-bold text-gray-800 text-lg sm:text-xl leading-relaxed mb-8">{{ $q->question_text }}</p>

                    @if($q->question_type == 'mcq')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($q->options as $opt)
                                @php
                                    $isSelected = $answer && $answer->selected_option_id == $opt->id;
                                    $isCorrectOpt = $opt->is_correct;
                                @endphp
                                <div class="p-5 rounded-2xl border-2 font-bold flex items-center gap-4 transition-all duration-300 relative
                                    {{ $isSelected && $isCorrectOpt ? 'bg-emerald-50 border-emerald-500 text-emerald-900' : '' }}
                                    {{ $isSelected && !$isCorrectOpt ? 'bg-red-50 border-red-500 text-red-900' : '' }}
                                    {{ !$isSelected && $isCorrectOpt ? 'bg-indigo-50 border-indigo-200 text-indigo-900 border-dashed' : '' }}
                                    {{ !$isSelected && !$isCorrectOpt ? 'bg-white border-gray-100 text-gray-400 opacity-60' : '' }}
                                ">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 
                                        {{ $isSelected && $isCorrectOpt ? 'bg-emerald-500 border-emerald-400 text-white' : '' }}
                                        {{ $isSelected && !$isCorrectOpt ? 'bg-red-500 border-red-400 text-white' : '' }}
                                        {{ !$isSelected && $isCorrectOpt ? 'bg-white border-indigo-500 text-indigo-500' : '' }}
                                        {{ !$isSelected && !$isCorrectOpt ? 'bg-white border-gray-100 text-gray-100' : '' }}
                                    ">
                                        @if($isSelected)
                                            <i class="fas {{ $isCorrectOpt ? 'fa-check' : 'fa-times text-xs' }}"></i>
                                        @elseif($isCorrectOpt)
                                            <i class="fas fa-check text-xs"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-sm sm:text-base">{{ $opt->option_text }}</span>
                                        @if($isSelected && $isCorrectOpt)
                                            <span class="block text-[10px] uppercase font-black tracking-widest mt-1 opacity-70">Member Selected (Correct)</span>
                                        @elseif($isSelected && !$isCorrectOpt)
                                            <span class="block text-[10px] uppercase font-black tracking-widest mt-1 opacity-70">Member Selected (Wrong)</span>
                                        @elseif(!$isSelected && $isCorrectOpt)
                                            <span class="block text-[10px] uppercase font-black tracking-widest mt-1 opacity-70">Correct Answer</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Written Response -->
                        <div class="mb-6">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Member's Response</h4>
                            <div class="p-6 rounded-[1.5rem] sm:rounded-[2rem] border-2 border-gray-100 bg-gray-50/50 shadow-inner">
                                <p class="text-gray-700 font-bold whitespace-pre-wrap leading-relaxed italic">
                                    {{ $answer && $answer->text_answer ? $answer->text_answer : '--- No response provided ---' }}
                                </p>
                            </div>
                        </div>

                        @if($answer && $answer->is_graded)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-6 rounded-2xl bg-indigo-50 border border-indigo-100 gap-4">
                                <div>
                                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Graded By Leader</p>
                                    <p class="font-black text-indigo-900 text-sm">Score Awarded: {{ $answer->marks_awarded }} / {{ $q->marks }}</p>
                                </div>
                                @if($answer->feedback)
                                    <div class="flex-1 bg-white p-4 rounded-xl shadow-sm italic text-xs text-gray-500">
                                        <i class="fas fa-comment-dots text-indigo-400 mr-2"></i> "{{ $answer->feedback }}"
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-6 rounded-2xl bg-amber-50 border border-amber-100 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-200">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <p class="font-black text-amber-900 text-xs sm:text-sm uppercase tracking-tight">Pending Grading by Leader</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Final Summary Banner -->
    <div class="mt-16 bg-gray-900 rounded-[2.5rem] sm:rounded-[4rem] p-8 sm:p-12 text-center text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl -ml-32 -mb-32"></div>
        
        <div class="relative z-10 flex flex-col items-center">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white/10 flex items-center justify-center text-3xl sm:text-4xl mb-6">
                <i class="fas fa-poll-h"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black mb-4">Review Complete</h2>
            <div class="flex flex-wrap justify-center gap-6 sm:gap-12">
                <div>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Correct</p>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-400">{{ $attempt->answers->where('is_correct', true)->count() }}</p>
                </div>
                 <div>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Incorrect</p>
                    <p class="text-2xl sm:text-3xl font-black text-red-400">{{ $questions->count() - $attempt->answers->where('is_correct', true)->count() - ($questions->count() - $attempt->answers->count()) }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Unanswered</p>
                    <p class="text-2xl sm:text-3xl font-black text-gray-400">{{ $questions->count() - $attempt->answers->count() }}</p>
                </div>
            </div>
            
            <a href="{{ route('admin.quizzes.results', $quiz->id) }}" class="mt-10 px-10 py-4 bg-white text-gray-900 rounded-2xl font-black text-xs sm:text-sm uppercase tracking-widest hover:bg-gray-100 transition shadow-xl shadow-white/5 active:scale-95 inline-block">
                Back to Results List
            </a>
        </div>
    </div>
</div>
@endsection
