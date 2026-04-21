@extends('layouts.batu')

@section('content')
<div class="max-w-6xl mx-auto py-8 text-gray-800" x-data="{
    tab: 'list',
    questionType: 'mcq',
    options: [{text: '', isCorrect: true}, {text: '', isCorrect: false}]
}">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="text-gray-500 hover:text-black font-bold text-sm mb-2 inline-block"><i class="fas fa-arrow-left"></i> Back</a>
            <h1 class="text-3xl font-black text-gray-800">Questions: {{ $quiz->title }}</h1>
        </div>
        <button @click="tab = (tab === 'add' ? 'list' : 'add')" class="btn-royal-gold py-2 px-6 rounded-xl font-bold shadow-lg">
            <span x-show="tab === 'list'"><i class="fas fa-plus"></i> Add Question</span>
            <span x-show="tab === 'add'"><i class="fas fa-times"></i> Cancel</span>
        </button>
    </div>

    <!-- Questions List -->
    <div x-show="tab === 'list'" class="space-y-4">
        @forelse($quiz->questions as $i => $question)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex justify-between gap-4 group">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">Q{{ $i+1 }}</span>
                        <span class="text-xs font-bold text-gray-400 capitalize">{{ $question->question_type }}</span>
                        <span class="text-xs font-bold text-green-600">{{ $question->marks }} Marks</span>
                        @if($question->is_required)<span class="text-xs font-bold text-red-500">* Required</span>@endif
                    </div>
                    <p class="font-bold text-lg text-gray-800 mb-4">{{ $question->question_text }}</p>
                    
                    @if($question->question_type === 'mcq')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($question->options as $opt)
                                <div class="px-4 py-2 rounded-lg border text-sm {{ $opt->is_correct ? 'bg-green-50 border-green-200 font-bold text-green-800' : 'bg-gray-50 border-gray-200 text-gray-600' }}">
                                    @if($opt->is_correct) <i class="fas fa-check-circle mr-1"></i> @endif
                                    {{ $opt->option_text }}
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 bg-gray-50 border border-gray-200 border-dashed rounded-xl text-gray-400 text-sm">
                            <i class="fas fa-keyboard"></i> Text answer field will appear here.
                        </div>
                    @endif
                </div>
                
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.quizzes.questions.edit', [$quiz->id, $question->id]) }}" class="text-blue-500 hover:text-blue-700 p-2 text-center" title="Edit Question"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.quizzes.questions.destroy', [$quiz->id, $question->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 p-2"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-white rounded-2xl border border-dashed border-gray-300">
                <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                <p class="text-gray-500 font-bold">No questions added yet.</p>
                <button @click="tab = 'add'" class="text-yellow-600 font-bold hover:underline mt-2">Add your first question</button>
            </div>
        @endforelse
    </div>

    <!-- Add Question Form -->
    <div x-show="tab === 'add'" x-cloak class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-4">Create New Question</h2>
        
        <form action="{{ route('admin.quizzes.questions.store', $quiz->id) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Question Text <span class="text-red-500">*</span></label>
                    <textarea name="question_text" required rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Question Type <span class="text-red-500">*</span></label>
                    <select name="question_type" x-model="questionType" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">
                        <option value="mcq">Multiple Choice Question (MCQ)</option>
                        <option value="written">Written / Text Essay</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Marks <span class="text-red-500">*</span></label>
                    <input type="number" name="marks" required min="0" step="0.5" value="1" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">
                </div>
            </div>

            <!-- Options Section (For MCQ) -->
            <div x-show="questionType === 'mcq'" class="bg-gray-50 p-6 rounded-2xl border border-gray-200 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800">Answers Options</h3>
                    <button type="button" @click="options.push({text: '', isCorrect: false})" class="text-sm font-bold text-blue-600 hover:underline"><i class="fas fa-plus"></i> Add Option</button>
                </div>

                <div class="space-y-3">
                    <template x-for="(opt, index) in options" :key="index">
                        <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-gray-300 shadow-sm">
                            <input type="radio" required name="correct_option" :value="index" :checked="opt.isCorrect" class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                            <input type="text" :name="'options['+index+'][text]'" x-model="opt.text" required class="flex-1 rounded border-transparent focus:border-yellow-500 focus:ring-0 text-sm" placeholder="Enter option text...">
                            <button type="button" @click="if(options.length > 2) options.splice(index, 1)" class="text-red-400 hover:text-red-600 p-2" x-show="options.length > 2"><i class="fas fa-times"></i></button>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-gray-500 mt-3"><i class="fas fa-info-circle"></i> Select the radio button next to the correct answer. You must have at least 2 options.</p>
            </div>

            <div class="flex items-center gap-2 mb-6">
                <input type="checkbox" name="is_required" value="1" checked id="isReq" class="text-yellow-600 rounded border-gray-300">
                <label for="isReq" class="font-bold text-sm text-gray-700">Question is Required to answer</label>
            </div>

            <div class="text-right border-t pt-4">
                <button type="button" @click="tab = 'list'" class="bg-gray-100 text-gray-600 font-bold px-4 py-2 rounded-xl border hover:bg-gray-200 mr-2">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-xl shadow hover:bg-blue-700">Save Question</button>
            </div>
        </form>
    </div>
</div>
@endsection
