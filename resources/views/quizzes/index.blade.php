@extends('layouts.batu')

@section('content')
    <div class="max-w-6xl mx-auto py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-black text-gray-800 dark:text-gray-200 tracking-tight"><i
                    class="fas fa-file-signature text-yellow-500 mr-2"></i> Team Quizzes</h1>
            <p class="text-gray-500 dark:text-gray-400 font-bold mt-2 text-lg">Test your skills and complete required assignments.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($quizzes as $quiz)
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden group hover:shadow-[0_20px_40px_-15px_rgba(234,179,8,0.3)] transition duration-300">
                    <div class="bg-gradient-to-r from-gray-900 to-black p-6 relative overflow-hidden">
                        <div
                            class="absolute -right-10 -top-10 w-32 h-32 bg-yellow-500 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition duration-500">
                        </div>
                        <h3 class="text-2xl font-black text-white relative z-10">{{ $quiz->title }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 px-3 py-1 rounded-full text-xs font-bold border"><i
                                    class="far fa-clock"></i> {{ $quiz->duration_minutes }} Mins</span>
                            <span
                                class="bg-orange-50 text-orange-600 px-3 py-1 rounded-full text-xs font-bold border border-orange-200"><i
                                    class="fas fa-list-ol"></i> {{ $quiz->questions()->count() }} Questions</span>
                            <span
                                class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold border border-blue-200"><i
                                    class="fas fa-star"></i> {{ $quiz->total_marks }} Marks</span>
                        </div>

                        @if($quiz->is_published)
                            <div class="mt-8 space-y-3">
                                <a href="{{ route('quizzes.show', $quiz->id) }}"
                                    class="block w-full btn-royal-gold py-3 px-4 rounded-xl font-bold font-tech text-center shadow-md transform active:scale-95 transition">
                                    <i class="fas fa-play mr-2"></i> View & Start
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full py-16 text-center bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-dashed border-gray-300 dark:border-gray-600">
                    <i class="fas fa-bed text-5xl text-gray-300 mb-4 block"></i>
                    <h3 class="text-2xl font-black text-gray-800 dark:text-gray-200">No evaluations right now</h3>
                    <p class="text-gray-500 dark:text-gray-400 font-bold mt-2">Take a rest, no pending tasks available.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection