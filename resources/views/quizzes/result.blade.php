@extends('layouts.batu')

@section('content')
<div class="max-w-4xl mx-auto py-10">

    {{-- Back link --}}
    <a href="{{ route('quizzes.index') }}" class="text-gray-400 hover:text-black text-sm font-bold mb-6 inline-flex items-center gap-2 transition">
        <i class="fas fa-arrow-left"></i> Back to All Evaluations
    </a>

    {{-- ===== SCORE CARD ===== --}}
    @php
        $percentage = $quiz->total_marks > 0 ? round(($attempt->score / $quiz->total_marks) * 100, 1) : 0;
        $passed = $percentage >= 50;
        $isDisqualified = in_array($attempt->status, ['disqualified', 'cancelled']);
        $isAutoSubmitted = $attempt->status === 'auto_submitted';
    @endphp

    <div class="rounded-[2rem] overflow-hidden shadow-2xl mb-8 border {{ $isDisqualified ? 'border-red-200' : ($passed ? 'border-green-200' : 'border-yellow-200') }}">
        {{-- Header gradient --}}
        <div class="p-10 relative text-white {{ $isDisqualified ? 'bg-gradient-to-br from-red-900 to-red-700' : ($passed ? 'bg-gradient-to-br from-gray-900 to-black' : 'bg-gradient-to-br from-yellow-800 to-yellow-900') }}">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            <div class="relative z-10 flex flex-col sm:flex-row items-center gap-8">
                {{-- Big score circle --}}
                <div class="w-36 h-36 rounded-full border-8 {{ $isDisqualified ? 'border-red-400 bg-red-800/50' : ($passed ? 'border-yellow-400 bg-white/10' : 'border-yellow-500 bg-yellow-900/30') }} flex flex-col items-center justify-center shrink-0 shadow-2xl">
                    @if($isDisqualified)
                        <i class="fas fa-ban text-5xl text-red-300"></i>
                    @else
                        <span class="text-4xl font-black">{{ $percentage }}%</span>
                        <span class="text-xs font-bold opacity-70 mt-1">Score</span>
                    @endif
                </div>

                <div class="text-center sm:text-left">
                    <p class="text-sm uppercase tracking-widest opacity-60 font-bold mb-1">{{ $quiz->title }}</p>
                    @if($isDisqualified)
                        <h1 class="text-4xl font-black text-red-200 mb-2">Disqualified</h1>
                        <p class="text-red-300 font-bold">Reason: {{ $attempt->cancelled_reason ?? 'Repeated violations.' }}</p>
                    @elseif($isAutoSubmitted)
                        <h1 class="text-4xl font-black mb-2">Time Expired</h1>
                        <p class="text-gray-300 font-bold">Your exam was auto-submitted when time ran out.</p>
                    @elseif($passed)
                        <h1 class="text-4xl font-black text-yellow-300 mb-2">Excellent Work! 🎉</h1>
                        <p class="text-gray-300 font-bold">You passed this evaluation successfully.</p>
                    @else
                        <h1 class="text-4xl font-black text-yellow-300 mb-2">Exam Submitted</h1>
                        <p class="text-gray-300 font-bold">Review your answers below.</p>
                    @endif

                    @if($attempt->extra_time_minutes > 0)
                        <span class="inline-block mt-3 bg-yellow-500/20 border border-yellow-500/40 text-yellow-200 text-xs px-3 py-1 rounded-full font-bold">
                            <i class="fas fa-clock mr-1"></i> +{{ $attempt->extra_time_minutes }} min extra time was granted
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats bar --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-100 bg-white">
            <div class="p-5 text-center">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Total Score</p>
                <p class="text-2xl font-black text-gray-800">{{ $attempt->score }} <span class="text-sm text-gray-400 font-bold">/ {{ $quiz->total_marks }}</span></p>
            </div>
            <div class="p-5 text-center">
                <p class="text-xs font-bold uppercase tracking-wider text-green-400 mb-1">Correct</p>
                <p class="text-2xl font-black text-green-600">{{ $correct }}</p>
            </div>
            <div class="p-5 text-center">
                <p class="text-xs font-bold uppercase tracking-wider text-red-400 mb-1">Wrong</p>
                <p class="text-2xl font-black text-red-500">{{ $wrong }}</p>
            </div>
            <div class="p-5 text-center">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Unanswered</p>
                <p class="text-2xl font-black text-gray-500">{{ $unanswered }}</p>
            </div>
        </div>
    </div>

    {{-- ===== ANSWER REVIEW ===== --}}
    <h2 class="text-2xl font-black text-gray-800 mb-6"><i class="fas fa-list-check text-blue-500 mr-2"></i> Answer Review</h2>

    <div class="space-y-4">
        @foreach($questions as $i => $q)
            @php
                $ans = $answers->get($q->id);
                $isAnswered = (bool) $ans;
                $isCorrect = $isAnswered && $q->question_type === 'mcq' && $ans->is_correct;
                $isWrong = $isAnswered && $q->question_type === 'mcq' && !$ans->is_correct;
                $isWritten = $q->question_type === 'written';

                if ($isCorrect) {
                    $cardBg = 'bg-green-50 border-green-200';
                    $badge = '<span class="bg-green-100 text-green-700 text-xs font-black px-3 py-1 rounded-full"><i class=\"fas fa-check mr-1\"></i>Correct</span>';
                } elseif ($isWrong) {
                    $cardBg = 'bg-red-50 border-red-200';
                    $badge = '<span class="bg-red-100 text-red-700 text-xs font-black px-3 py-1 rounded-full"><i class=\"fas fa-times mr-1\"></i>Wrong</span>';
                } elseif (!$isAnswered) {
                    $cardBg = 'bg-gray-50 border-gray-200';
                    $badge = '<span class="bg-gray-100 text-gray-500 text-xs font-black px-3 py-1 rounded-full"><i class=\"fas fa-minus mr-1\"></i>Unanswered</span>';
                } else {
                    $cardBg = 'bg-blue-50 border-blue-200';
                    $badge = '<span class="bg-blue-100 text-blue-700 text-xs font-black px-3 py-1 rounded-full"><i class=\"fas fa-pen mr-1\"></i>Written (Pending Grade)</span>';
                }
            @endphp

            <div class="rounded-2xl border {{ $cardBg }} p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 text-sm font-black flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                        <p class="font-bold text-gray-900 text-base leading-snug">{{ $q->question_text }}</p>
                    </div>
                    {!! $badge !!}
                </div>

                @if($q->question_type === 'mcq')
                    <div class="space-y-2 ml-11">
                        @foreach($q->options as $opt)
                            @php
                                $selected = $ans && $ans->selected_option_id == $opt->id;
                                $optClass = 'border rounded-xl px-4 py-2.5 text-sm font-bold flex items-center gap-3 transition ';
                                if ($opt->is_correct) {
                                    $optClass .= 'bg-green-50 border-green-400 text-green-800';
                                } elseif ($selected && !$opt->is_correct) {
                                    $optClass .= 'bg-red-50 border-red-400 text-red-700';
                                } else {
                                    $optClass .= 'bg-white border-gray-200 text-gray-600';
                                }
                            @endphp
                            <div class="{{ $optClass }}">
                                @if($opt->is_correct)
                                    <i class="fas fa-check-circle text-green-500 shrink-0"></i>
                                @elseif($selected)
                                    <i class="fas fa-times-circle text-red-400 shrink-0"></i>
                                @else
                                    <i class="far fa-circle text-gray-300 shrink-0"></i>
                                @endif
                                {{ $opt->option_text }}
                                @if($selected && !$opt->is_correct)
                                    <span class="ml-auto text-xs text-red-400 font-bold">Your Answer</span>
                                @elseif($opt->is_correct)
                                    <span class="ml-auto text-xs text-green-600 font-bold">Correct Answer</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                @elseif($q->question_type === 'written')
                    <div class="ml-11 mt-2 bg-white p-4 rounded-xl border border-blue-200">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Your Answer</p>
                        <p class="text-gray-800 font-bold">{{ $ans ? ($ans->text_answer ?: '(blank)') : '(No answer submitted)' }}</p>
                        @if($ans && $ans->marks_awarded > 0)
                            <p class="text-xs text-green-600 font-bold mt-2"><i class="fas fa-star mr-1"></i> {{ $ans->marks_awarded }} / {{ $q->marks }} marks awarded</p>
                        @elseif(!$ans || !$ans->text_answer)
                            <p class="text-xs text-gray-400 font-bold mt-2">Not graded yet — pending admin review.</p>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Bottom CTA --}}
    <div class="mt-10 flex justify-center">
        <a href="{{ route('quizzes.index') }}" class="btn-royal-gold px-8 py-3 rounded-2xl font-black text-lg shadow-xl hover:-translate-y-1 transition">
            <i class="fas fa-home mr-2"></i> Back to Evaluations
        </a>
    </div>
</div>
@endsection
