@extends('layouts.batu')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="mb-6 flex justify-between items-end border-b pb-4">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="text-gray-500 hover:text-black font-bold text-sm mb-2 inline-block"><i class="fas fa-arrow-left"></i> Back to Quizzes</a>
            <h1 class="text-3xl font-black text-gray-800"><i class="fas fa-check-double text-purple-500 mr-2"></i> Grade Written Answers: {{ $quiz->title }}</h1>
        </div>
        <div class="text-right">
            <p class="text-xl font-black text-purple-600">{{ $pendingAnswers->count() }}</p>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Pending Review</p>
        </div>
    </div>

    @forelse($pendingAnswers as $answer)
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-6">
            <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <p class="font-bold text-gray-800"><i class="fas fa-user-graduate text-gray-400 mr-1"></i> {{ $answer->attempt->user->name }}</p>
                <span class="text-xs font-bold px-3 py-1 rounded bg-blue-100 text-blue-800">Submitted: {{ $answer->attempt->submitted_at->diffForHumans() }}</span>
            </div>
            <div class="p-6">
                <!-- Question Details -->
                <div class="mb-4 space-y-1">
                    <span class="text-xs font-bold uppercase text-purple-500">Question (Max: {{ $answer->question->marks }} marks)</span>
                    <p class="text-lg font-bold text-gray-800 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        {!! nl2br(e($answer->question->question_text)) !!}
                    </p>
                </div>

                <!-- User's Answer -->
                <div class="mb-6 space-y-1">
                    <span class="text-xs font-bold uppercase text-blue-500">Member's Answer</span>
                    <div class="text-base text-gray-800 bg-blue-50 p-4 rounded-xl border border-blue-100 whitespace-pre-wrap font-mono">{{ $answer->text_answer ?? 'No answer provided.' }}</div>
                </div>

                <!-- Grading Form -->
                <form action="{{ route('admin.quizzes.grade.save', $answer->id) }}" method="POST" class="flex items-end gap-4 border-t pt-4 border-gray-100">
                    @csrf
                    <div class="flex-1 max-w-xs">
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-widest">Assign Marks</label>
                        <div class="relative">
                            <input type="number" name="marks_awarded" step="0.5" min="0" max="{{ $answer->question->marks }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 pr-16 text-lg font-black text-purple-700 placeholder-gray-300">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">/ {{ $answer->question->marks }}</span>
                        </div>
                    </div>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-6 rounded-xl shadow transition">Save Grade</button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-dashed border-gray-300">
            <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-xl">
                <i class="fas fa-check text-4xl"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-800 mb-2">All Caught Up!</h3>
            <p class="text-gray-500 font-bold">There are no pending written answers to grade for this quiz.</p>
        </div>
    @endforelse
</div>
@endsection
