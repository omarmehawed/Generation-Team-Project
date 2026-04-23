@extends('layouts.batu')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-gray-800" x-data="{
    tab: 'list',
    questionType: 'mcq',
    options: [{text: '', isCorrect: true}, {text: '', isCorrect: false}]
}">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-indigo-600 font-bold text-xs uppercase tracking-widest mb-3 transition group">
                <i class="fas fa-arrow-left transition group-hover:-translate-x-1"></i> Back to Quizzes
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Manage Questions</h1>
        </div>
        <button @click="tab = (tab === 'add' ? 'list' : 'add')" :class="tab === 'list' ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-rose-50 text-rose-600 hover:bg-rose-100'" class="w-full sm:w-auto py-3 px-8 rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
            <span x-show="tab === 'list'"><i class="fas fa-plus"></i> Add Question</span>
            <span x-show="tab === 'add'"><i class="fas fa-times"></i> Cancel</span>
        </button>
    </div>

    <!-- Questions List -->
    <div x-show="tab === 'list'" class="space-y-6">
        @forelse($quiz->questions as $i => $question)
            <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 p-6 sm:p-8 flex flex-col md:flex-row justify-between gap-8 group hover:border-indigo-100 transition duration-300">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-lg">Question {{ $i+1 }}</span>
                        <span class="bg-gray-50 text-gray-500 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-lg">{{ $question->question_type }}</span>
                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-lg">{{ $question->marks }} Marks</span>
                        @if($question->is_required)
                            <span class="bg-rose-50 text-rose-500 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-lg border border-rose-100">Required</span>
                        @endif
                    </div>
                    <p class="font-black text-lg sm:text-xl text-gray-800 mb-6 leading-tight">{{ $question->question_text }}</p>
                    
                    @if($question->question_type === 'mcq')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($question->options as $opt)
                                <div class="px-5 py-3 rounded-2xl border-2 text-sm transition {{ $opt->is_correct ? 'bg-emerald-50 border-emerald-100 font-black text-emerald-700' : 'bg-gray-50/50 border-transparent text-gray-400 font-bold' }}">
                                    <div class="flex items-center gap-3">
                                        @if($opt->is_correct) 
                                            <i class="fas fa-check-circle"></i> 
                                        @else
                                            <i class="far fa-circle text-gray-200"></i>
                                        @endif
                                        {{ $opt->option_text }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 bg-gray-50/50 border border-gray-100 rounded-2xl flex items-center gap-3 text-gray-400 font-bold text-sm italic">
                            <i class="fas fa-keyboard text-indigo-300"></i> Open-ended written response field
                        </div>
                    @endif
                </div>
                
                <div class="flex md:flex-col items-center justify-end gap-3 pt-6 md:pt-0 border-t md:border-t-0 md:border-l border-gray-50 md:pl-8">
                    <a href="{{ route('admin.quizzes.questions.edit', [$quiz->id, $question->id]) }}" class="flex-1 md:flex-none w-12 h-12 flex items-center justify-center bg-gray-50 text-indigo-400 rounded-2xl hover:bg-indigo-50 hover:text-indigo-600 transition shadow-sm" title="Edit Question">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.quizzes.questions.destroy', [$quiz->id, $question->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?')" class="flex-1 md:flex-none">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full md:w-12 h-12 flex items-center justify-center bg-rose-50 text-rose-400 rounded-2xl hover:bg-rose-100 hover:text-rose-600 transition shadow-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-24 bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                <div class="w-20 h-20 rounded-[2rem] bg-gray-50 flex items-center justify-center text-4xl text-gray-200 mx-auto mb-6">
                    <i class="fas fa-box-open"></i>
                </div>
                <p class="text-gray-400 font-black uppercase tracking-widest text-sm">No questions added yet</p>
                <button @click="tab = 'add'" class="text-indigo-600 font-black uppercase tracking-widest text-[10px] hover:underline mt-4">Add your first question</button>
            </div>
        @endforelse
    </div>

    <!-- Add Question Form -->
    <div x-show="tab === 'add'" x-cloak class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 p-8 sm:p-12 border border-gray-100">
        <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gray-50">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center shadow-sm">
                <i class="fas fa-plus"></i>
            </div>
            <h2 class="text-2xl font-black text-gray-900 leading-tight">Create New Question</h2>
        </div>
        
        <form action="{{ route('admin.quizzes.questions.store', $quiz->id) }}" method="POST" class="space-y-10">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Question Prompt <span class="text-red-500">*</span></label>
                    <textarea name="question_text" required rows="4" placeholder="What is the output of the following code snippet?" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 p-6 font-bold text-gray-800 focus:border-indigo-500 focus:ring-indigo-200 transition"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Response Mechanism <span class="text-red-500">*</span></label>
                    <select name="question_type" x-model="questionType" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 p-4 font-black text-gray-800 focus:border-indigo-500 focus:ring-indigo-200 transition">
                        <option value="mcq">Multiple Choice (MCQ)</option>
                        <option value="written">Open-Ended (Written)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Point Value <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-star absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="number" name="marks" required min="0" step="0.5" value="1" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 p-4 pl-12 font-black text-gray-800 focus:border-indigo-500 focus:ring-indigo-200 transition">
                    </div>
                </div>
            </div>

            <!-- Options Section (For MCQ) -->
            <div x-show="questionType === 'mcq'" class="bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-6 bg-indigo-600 rounded-full"></div>
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em]">Answer Config</h3>
                    </div>
                    <button type="button" @click="options.push({text: '', isCorrect: false})" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline transition">
                        <i class="fas fa-plus mr-1"></i> Add Option
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(opt, index) in options" :key="index">
                        <div class="flex flex-col sm:flex-row items-center gap-4 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm group hover:border-indigo-200 transition">
                            <div class="flex items-center gap-4 w-full sm:w-auto">
                                <label class="flex items-center cursor-pointer group/radio">
                                    <input type="radio" required name="correct_option" :value="index" :checked="opt.isCorrect" class="w-6 h-6 text-emerald-500 focus:ring-emerald-200 border-gray-200">
                                    <span class="ml-2 text-[10px] font-black uppercase text-gray-400 group-hover/radio:text-emerald-500 transition sm:hidden">Correct?</span>
                                </label>
                                <span class="hidden sm:block text-[10px] font-black uppercase text-gray-300">Answer</span>
                            </div>
                            <input type="text" :name="'options['+index+'][text]'" x-model="opt.text" required class="flex-1 w-full bg-gray-50/50 border-transparent rounded-xl focus:border-indigo-100 focus:ring-0 font-bold text-gray-700" placeholder="Type answer choice...">
                            <button type="button" @click="if(options.length > 2) options.splice(index, 1)" class="w-10 h-10 flex items-center justify-center text-rose-300 hover:text-rose-500 transition" x-show="options.length > 2">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                </div>
                <div class="mt-6 flex items-center gap-3 p-4 bg-indigo-50/30 rounded-xl border border-indigo-50">
                    <i class="fas fa-info-circle text-indigo-400"></i>
                    <p class="text-[10px] font-bold text-indigo-900 uppercase tracking-wider">Select the radio button next to the correct answer choice.</p>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <input type="checkbox" name="is_required" value="1" checked id="isReq" class="w-6 h-6 text-indigo-600 rounded-lg border-gray-200">
                <label for="isReq" class="flex-1 pt-1">
                    <span class="block font-black text-gray-800 text-sm">Strict Requirement</span>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase">Members must provide an answer to proceed</span>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-8 border-t border-gray-50">
                <button type="button" @click="tab = 'list'" class="w-full sm:w-auto px-8 py-4 bg-gray-50 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition">Cancel</button>
                <button type="submit" class="w-full sm:w-auto px-12 py-4 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:bg-black transition transform active:scale-95">Save Question</button>
            </div>
        </form>
    </div>
</div>
        </form>
    </div>
</div>
@endsection
