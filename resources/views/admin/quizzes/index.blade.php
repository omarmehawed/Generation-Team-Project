@extends('layouts.batu')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-gray-100 flex items-center gap-3">
            <span class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center shadow-sm">
                <i class="fas fa-cogs text-sm sm:text-base"></i>
            </span>
            Manage Quizzes
        </h1>
        <div class="flex flex-wrap gap-3 w-full sm:w-auto">
            <a href="{{ route('admin.quizzes.retries') }}" class="flex-1 sm:flex-none justify-center btn-royal-gold px-4 py-2.5 rounded-xl font-bold text-xs shadow-sm flex items-center transition hover:-translate-y-0.5">
                <i class="fas fa-undo-alt mr-2 text-yellow-800"></i> Retry Requests
                @php
                    $pendingRetriesCount = \App\Models\QuizRetryRequest::where('status', 'pending')->count();
                @endphp
                @if($pendingRetriesCount > 0)
                    <span class="ml-2 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $pendingRetriesCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.quizzes.create') }}" class="flex-1 sm:flex-none justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-indigo-100 transition flex items-center text-xs">
                <i class="fas fa-plus mr-2"></i> Create Quiz
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl shadow-gray-100/50 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm whitespace-nowrap">
                <thead class="uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 bg-gray-50/50">
                    <tr class="text-[10px] font-black text-gray-400">
                        <th scope="col" class="px-8 py-5">Quiz Information</th>
                        <th scope="col" class="px-6 py-5 text-center">Questions</th>
                        <th scope="col" class="px-6 py-5 text-center">Attempts</th>
                        <th scope="col" class="px-6 py-5 text-center">Status</th>
                        <th scope="col" class="px-8 py-5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($quizzes as $quiz)
                    <tr class="hover:bg-gray-50/50 transition duration-200 group">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-900 dark:text-gray-100 text-base mb-1 group-hover:text-indigo-600 transition">{{ $quiz->title }}</span>
                                <div class="flex items-center gap-3 text-[10px] sm:text-xs font-bold text-gray-400">
                                    <span class="flex items-center gap-1"><i class="far fa-clock text-indigo-400"></i> {{ $quiz->duration_minutes }} Mins</span>
                                    <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                    <span class="flex items-center gap-1"><i class="fas fa-star text-orange-400"></i> {{ $quiz->total_marks }} Marks</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-orange-50 text-orange-600 font-black text-sm">
                                {{ $quiz->questions_count }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-600 font-black text-sm">
                                {{ $quiz->attempts_count }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            @if($quiz->is_published)
                                <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest border border-emerald-100">Published</span>
                            @else
                                <span class="bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest border border-gray-200 dark:border-gray-700">Hidden</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.quizzes.questions', $quiz->id) }}" class="w-9 h-9 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-amber-600 rounded-xl shadow-sm hover:shadow-md hover:border-amber-200 transition" title="Questions">
                                    <i class="fas fa-list-ul text-xs"></i>
                                </a>
                                <a href="{{ route('admin.quizzes.live', $quiz->id) }}" class="w-9 h-9 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-rose-500 rounded-xl shadow-sm hover:shadow-md hover:border-rose-200 transition" title="Live Monitor">
                                    <i class="fas fa-satellite-dish text-xs animate-pulse"></i>
                                </a>
                                <a href="{{ route('admin.quizzes.results', $quiz->id) }}" class="w-9 h-9 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-indigo-600 rounded-xl shadow-sm hover:shadow-md hover:border-indigo-200 transition" title="Results">
                                    <i class="fas fa-chart-bar text-xs"></i>
                                </a>
                                <a href="{{ route('admin.quizzes.grading', $quiz->id) }}" class="w-9 h-9 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-purple-600 rounded-xl shadow-sm hover:shadow-md hover:border-purple-200 transition" title="Manual Grading">
                                    <i class="fas fa-check-double text-xs"></i>
                                </a>
                                <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="w-9 h-9 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 rounded-xl shadow-sm hover:shadow-md hover:border-gray-200 transition" title="Settings">
                                    <i class="fas fa-cog text-xs"></i>
                                </a>
                                <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quiz? All attempts, responses, and questions will be destroyed permanently!')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition shadow-sm" title="Delete Quiz">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center gap-4 text-gray-300">
                                <div class="w-20 h-20 rounded-[2rem] bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-4xl">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <p class="font-black uppercase tracking-[0.3em] text-xs">No quizzes found</p>
                                <a href="{{ route('admin.quizzes.create') }}" class="text-indigo-600 font-bold hover:underline">Create your first quiz</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
