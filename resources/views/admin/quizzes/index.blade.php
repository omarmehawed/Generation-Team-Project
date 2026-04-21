@extends('layouts.batu')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-cogs text-orange-500 mr-2"></i> Manage Quizzes (Admin)</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.quizzes.retries') }}" class="btn-royal-gold px-4 py-2 rounded-xl font-bold text-sm shadow-sm flex items-center transition hover:-translate-y-0.5">
                <i class="fas fa-undo-alt mr-2 text-yellow-800"></i> Retry Requests
                @php
                    $pendingRetriesCount = \App\Models\QuizRetryRequest::where('status', 'pending')->count();
                @endphp
                @if($pendingRetriesCount > 0)
                    <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingRetriesCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.quizzes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl shadow transition">
                <i class="fas fa-plus mr-1"></i> Create Quiz
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full text-left text-sm whitespace-nowrap">
            <thead class="uppercase tracking-wider border-b-2 border-gray-100 bg-gray-50/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold">Quiz Info</th>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold text-center">Questions</th>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold text-center">Attempts</th>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold text-center">Status</th>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $quiz)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-800 text-base">{{ $quiz->title }}</p>
                        <p class="text-xs text-gray-500"><i class="far fa-clock"></i> {{ $quiz->duration_minutes }} mins | {{ $quiz->total_marks }} marks</p>
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-orange-600">
                        {{ $quiz->questions_count }}
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-blue-600">
                        {{ $quiz->attempts_count }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($quiz->is_published)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Published</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold">Hidden</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.quizzes.questions', $quiz->id) }}" class="btn-royal-gold px-3 py-1.5 rounded shadow text-xs" title="Questions">
                                <i class="fas fa-list-ul"></i>
                            </a>
                            <a href="{{ route('admin.quizzes.live', $quiz->id) }}" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded shadow text-xs" title="Live Monitor">
                                <i class="fas fa-satellite-dish animate-pulse"></i>
                            </a>
                            <a href="{{ route('admin.quizzes.results', $quiz->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded shadow text-xs" title="Results">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                            <a href="{{ route('admin.quizzes.grading', $quiz->id) }}" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1.5 rounded shadow text-xs" title="Manual Grading">
                                <i class="fas fa-check-double"></i>
                            </a>
                            <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1.5 rounded shadow text-xs" title="Settings">
                                <i class="fas fa-cog"></i>
                            </a>
                            <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quiz? All attempts, responses, and questions will be destroyed permanently!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-600 px-3 py-1.5 rounded shadow text-xs" title="Delete Quiz">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-400 font-bold">No quizzes found. Create one!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
