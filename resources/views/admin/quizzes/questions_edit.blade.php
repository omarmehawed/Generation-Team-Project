@extends('layouts.batu')

@section('content')
<div class="max-w-4xl mx-auto py-8 text-gray-800 dark:text-gray-200" x-data="{
    questionType: '{{ $question->question_type }}',
    options: [
        @if($question->question_type === 'mcq' && count($question->options) > 0)
            @foreach($question->options as $opt)
                {text: '{{ addslashes($opt->option_text) }}', isCorrect: {{ $opt->is_correct ? 'true' : 'false' }}},
            @endforeach
        @else
            {text: '', isCorrect: true}, {text: '', isCorrect: false}
        @endif
    ]
}">
    <div class="mb-6">
        <a href="{{ route('admin.quizzes.questions', $quiz->id) }}" class="text-gray-500 dark:text-gray-400 hover:text-black font-bold text-sm mb-2 inline-block"><i class="fas fa-arrow-left"></i> Back to Questions</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6 border-b pb-4"><i class="fas fa-edit text-blue-500"></i> Edit Question</h2>
        
        <form action="{{ route('admin.quizzes.questions.update', [$quiz->id, $question->id]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Question Text <span class="text-red-500">*</span></label>
                    <textarea name="question_text" required rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">{{ $question->question_text }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Question Type <span class="text-red-500">*</span></label>
                    <select name="question_type" x-model="questionType" class="w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">
                        <option value="mcq">Multiple Choice Question (MCQ)</option>
                        <option value="written">Written / Text Essay</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Marks <span class="text-red-500">*</span></label>
                    <input type="number" name="marks" required min="0" step="0.5" value="{{ $question->marks }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">
                </div>
            </div>

            <!-- Options Section (For MCQ) -->
            <div x-show="questionType === 'mcq'" class="bg-gray-50 dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200">Answers Options</h3>
                    <button type="button" @click="options.push({text: '', isCorrect: false})" class="text-sm font-bold text-blue-600 hover:underline"><i class="fas fa-plus"></i> Add Option</button>
                </div>

                <div class="space-y-3">
                    <template x-for="(opt, index) in options" :key="index">
                        <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm">
                            <input type="radio" required name="correct_option" :value="index" :checked="opt.isCorrect" class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 dark:border-gray-600">
                            <input type="text" :name="'options['+index+'][text]'" x-model="opt.text" required class="flex-1 rounded border-transparent focus:border-yellow-500 focus:ring-0 text-sm" placeholder="Enter option text...">
                            <button type="button" @click="if(options.length > 2) options.splice(index, 1)" class="text-red-400 hover:text-red-600 p-2" x-show="options.length > 2"><i class="fas fa-times"></i></button>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3"><i class="fas fa-info-circle"></i> Select the radio button next to the correct answer. You must have at least 2 options.</p>
            </div>

            <div class="flex items-center gap-2 mb-6">
                <input type="checkbox" name="is_required" value="1" {{ $question->is_required ? 'checked' : '' }} id="isReq" class="text-yellow-600 rounded border-gray-300 dark:border-gray-600">
                <label for="isReq" class="font-bold text-sm text-gray-700 dark:text-gray-300">Question is Required to answer</label>
            </div>

            <div class="text-right border-t pt-4">
                <a href="{{ route('admin.quizzes.questions', $quiz->id) }}" class="inline-block bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-bold px-4 py-2 rounded-xl border hover:bg-gray-200 mr-2">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-xl shadow hover:bg-blue-700">Update Question</button>
            </div>
        </form>
    </div>
</div>
@endsection
