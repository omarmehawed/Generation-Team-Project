@extends('layouts.batu')

@section('content')
<div class="container-fluid py-6" x-data="formBuilder()">
    <!-- Premium Header -->
    <div class="relative mb-10">
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-3xl blur opacity-20 dark:opacity-40"></div>
        <div class="relative flex flex-wrap items-center justify-between gap-6 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border border-gray-100 dark:border-gray-800 p-6 rounded-3xl shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                    <i class="fas fa-tools text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white font-amiri tracking-tight">
                        Form <span class="text-blue-600 dark:text-blue-400">Builder</span>
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium flex items-center gap-2">
                        <i class="fas fa-magic text-blue-500"></i> Question Settings & Logic
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('join.admin') }}" 
                   class="group flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-2xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-300">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> 
                    Back to List
                </a>
                <button @click="openCreateModal()" 
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black transition-all duration-300 shadow-xl shadow-blue-600/30 flex items-center gap-3 active:scale-95">
                    <i class="fas fa-plus"></i> Add Question
                </button>
            </div>
        </div>
    </div>

    <!-- Questions list -->
    <div class="space-y-6 relative">
        <!-- Visual Connector Line for Logic (Optional aesthetic choice) -->
        <div class="absolute left-16 top-10 bottom-10 w-px bg-gradient-to-b from-blue-500/50 via-purple-500/50 to-blue-500/50 hidden lg:block opacity-20"></div>

        <template x-for="(q, index) in questions" :key="q.id">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="group relative"
                 :class="{'mt-10': q.conditional_logic && q.conditional_logic.show_if_question_id}">
                
                <!-- Logic Flow Indicator -->
                <template x-if="q.conditional_logic && q.conditional_logic.show_if_question_id">
                    <div class="absolute -top-8 left-16 flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-amber-500 animate-pulse">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <i class="fas fa-code-branch rotate-90"></i>
                        <span>Conditional Branch</span>
                    </div>
                </template>

                <div class="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-[2rem] p-6 shadow-xl hover:shadow-2xl hover:border-blue-500/30 dark:hover:border-blue-500/40 transition-all duration-500 relative overflow-hidden">
                    <!-- Accent Strip -->
                    <div class="absolute top-0 left-0 bottom-0 w-2" :class="getTypeColor(q.question_type)"></div>

                    <div class="flex items-start gap-6">
                        <!-- Left Sidebar: Order & Icon -->
                        <div class="flex flex-col items-center gap-4 shrink-0">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-inner bg-slate-50 dark:bg-gray-800/80"
                                 :class="getTypeIconColor(q.question_type)">
                                <i :class="getTypeIcon(q.question_type)"></i>
                            </div>

                            <div class="flex flex-col gap-2 bg-slate-50 dark:bg-gray-800/50 p-1.5 rounded-xl border border-slate-100 dark:border-gray-700/50">
                                <button @click="moveUp(index)" :disabled="index === 0" 
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-500 hover:bg-white dark:hover:bg-gray-700 transition-all disabled:opacity-20 active:scale-90">
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                                <div class="h-px bg-slate-200 dark:bg-gray-700 w-1/2 mx-auto"></div>
                                <button @click="moveDown(index)" :disabled="index === questions.length - 1" 
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-500 hover:bg-white dark:hover:bg-gray-700 transition-all disabled:opacity-20 active:scale-90">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="flex-grow pt-1">
                            <div class="flex items-center flex-wrap gap-2 mb-3">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm"
                                      :class="getBadgeStyles(q.question_type)" x-text="q.question_type"></span>
                                
                                <template x-if="q.is_required">
                                    <span class="px-3 py-1 rounded-full bg-red-500/10 text-red-500 text-[10px] font-black uppercase tracking-wider border border-red-500/20">Required</span>
                                </template>

                                <span class="ml-auto text-xs font-mono text-slate-400">QUESTION ID #<span x-text="q.id"></span></span>
                            </div>

                            <h4 class="text-xl font-bold text-slate-800 dark:text-white mb-4 pr-10" x-text="q.question_text"></h4>
                            
                            <!-- Options Grid Preview -->
                            <template x-if="['radio', 'select', 'checkbox', 'matrix'].includes(q.question_type)">
                                <div class="space-y-4">
                                    <template x-if="q.question_type !== 'matrix'">
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="opt in q.options" :key="opt">
                                                <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-gray-800/80 rounded-xl border border-slate-100 dark:border-gray-700/50 text-[11px] text-slate-600 dark:text-slate-300 font-medium">
                                                    <div class="w-1.5 h-1.5 rounded-full" :class="getTypeColor(q.question_type)"></div>
                                                    <span x-text="opt"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <template x-if="q.question_type === 'matrix'">
                                        <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-gray-800/50 p-4 rounded-2xl border border-slate-100 dark:border-gray-700/50">
                                            <div>
                                                <p class="text-[10px] font-black uppercase text-slate-400 mb-2">Rows</p>
                                                <div class="space-y-1">
                                                    <template x-for="row in q.options.rows" :key="row">
                                                        <div class="text-[11px] text-slate-600 dark:text-slate-400 truncate" x-text="row"></div>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="border-l border-slate-200 dark:border-gray-700 pl-4">
                                                <p class="text-[10px] font-black uppercase text-slate-400 mb-2">Columns</p>
                                                <div class="flex flex-wrap gap-1">
                                                    <template x-for="col in q.options.cols" :key="col">
                                                        <span class="px-1.5 py-0.5 bg-white dark:bg-gray-700 rounded text-[9px] text-slate-500" x-text="col"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Logic Connection Badge -->
                            <template x-if="q.conditional_logic && q.conditional_logic.show_if_question_id">
                                <div class="mt-6 flex items-center gap-3 p-3 bg-amber-500/5 dark:bg-amber-500/10 rounded-2xl border border-amber-500/20">
                                    <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/20">
                                        <i class="fas fa-code-branch"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black uppercase text-amber-600 tracking-wider">Conditional Visibility</span>
                                        <p class="text-xs text-amber-700 dark:text-amber-400">
                                            Shown if <span class="font-bold">Q#<span x-text="getQuestionIndex(q.conditional_logic.show_if_question_id)"></span></span> 
                                            is answered with "<span class="font-bold underline" x-text="q.conditional_logic.show_if_value"></span>"
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Actions: Floating Right -->
                        <div class="flex flex-col gap-2 scale-90 group-hover:scale-100 transition-transform">
                            <button @click="openEditModal(q)" 
                                    class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 text-blue-500 border border-slate-100 dark:border-gray-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form :action="'{{ url('/join-questions') }}/' + q.id" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 text-red-500 border border-slate-100 dark:border-gray-700 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all shadow-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="questions.length === 0">
            <div class="text-center py-20 bg-gray-50 dark:bg-gray-900 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-3xl">
                <i class="fas fa-question-circle text-4xl text-gray-300 mb-4 block"></i>
                <h3 class="text-gray-500 font-bold">No questions added yet.</h3>
                <p class="text-gray-400 text-sm">Start by clicking 'Add Question'</p>
            </div>
        </template>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="modalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>
            
            <div class="inline-block align-middle bg-white dark:bg-gray-900 rounded-3xl sm:rounded-[2.5rem] text-left overflow-hidden shadow-3xl transform transition-all my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-100 dark:border-gray-800 mx-2 sm:mx-0">
                <form :action="isEdit ? '{{ url('/join-questions') }}/' + editingId : '{{ route('join-questions.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="px-5 py-6 sm:px-8 sm:py-8 border-b border-slate-100 dark:border-gray-800 flex justify-between items-center bg-slate-50 dark:bg-gray-800/50">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-600 flex items-center justify-center text-white shrink-0">
                                <i :class="isEdit ? 'fas fa-edit' : 'fas fa-plus'"></i>
                            </div>
                            <div>
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white" x-text="isEdit ? 'Edit Question' : 'Add Question'"></h3>
                                <p class="text-[10px] sm:text-xs text-slate-500 font-medium tracking-tight">Configure details and behavior.</p>
                            </div>
                        </div>
                        <button type="button" @click="modalOpen = false" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors shadow-sm shrink-0">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <div class="p-5 sm:p-8 space-y-6 sm:space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        <!-- Text -->
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Question Prompt</label>
                            <input type="text" name="question_text" x-model="form.question_text" required
                                placeholder="e.g. What is your current area of study?"
                                class="w-full bg-slate-50 dark:bg-gray-800 border-2 border-slate-100 dark:border-gray-700 rounded-2xl px-4 py-3 sm:px-5 sm:py-4 focus:ring-0 focus:border-blue-600 dark:focus:border-blue-500 outline-none transition-all text-slate-800 dark:text-white font-bold placeholder:font-normal">
                        </div>

                        <!-- Type & Required -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Input Structure</label>
                                <select name="question_type" x-model="form.question_type" required
                                    class="w-full bg-slate-50 dark:bg-gray-800 border-2 border-slate-100 dark:border-gray-700 rounded-2xl px-5 py-3 sm:py-4 focus:ring-0 focus:border-blue-600 dark:focus:border-blue-500 outline-none transition-all text-slate-800 dark:text-white font-bold">
                                    <option value="text">Short Text</option>
                                    <option value="textarea">Paragraph</option>
                                    <option value="radio">Multiple Choice (Single)</option>
                                    <option value="select">Dropdown Menu</option>
                                    <option value="checkbox">Multiple Choice (Checkboxes)</option>
                                    <option value="date">Date Selector</option>
                                    <option value="scale">Linear Scale (1-5)</option>
                                    <option value="matrix">Choice Grid (Matrix)</option>
                                </select>
                            </div>
                            <div class="flex items-center sm:pt-8 bg-slate-50 dark:bg-slate-800/10 p-4 sm:p-0 rounded-2xl border border-slate-100 dark:border-gray-800 sm:border-0">
                                <label class="flex items-center gap-4 cursor-pointer group w-full">
                                    <div class="relative">
                                        <input type="checkbox" name="is_required" value="1" x-model="form.is_required" class="peer sr-only">
                                        <div class="w-14 h-7 bg-slate-200 dark:bg-gray-700 rounded-full peer-checked:bg-blue-600 transition-all shadow-inner"></div>
                                        <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full peer-checked:translate-x-7 transition-all shadow-md"></div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-800 dark:text-white mt-1">Required</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase -mt-1">Must be answered</span>
                                    </div>
                                </label>
                                <input type="hidden" name="is_required" value="0" x-show="!form.is_required">
                            </div>
                        </div>

                        <!-- Content Sections based on Type -->
                        <div class="bg-slate-50 dark:bg-gray-800/30 p-6 rounded-[2rem] border border-slate-100 dark:border-gray-800">
                            <!-- Options for choice types -->
                            <template x-if="['radio', 'select', 'checkbox'].includes(form.question_type)">
                                <div class="space-y-4">
                                    <label class="block text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest">Configure Options</label>
                                    <div class="space-y-3">
                                        <template x-for="(opt, i) in form.options" :key="i">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-blue-600/10 text-blue-600 flex items-center justify-center text-xs font-black" x-text="i+1"></div>
                                                <input type="text" :name="'options['+i+']'" x-model="form.options[i]" required
                                                    class="flex-grow bg-white dark:bg-gray-950 border border-slate-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-blue-600 dark:focus:border-blue-500 font-bold">
                                                <button type="button" @click="removeOption(i)" class="w-10 h-10 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-all">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </template>
                                        <button type="button" @click="addOption()" class="w-full py-4 border-2 border-dashed border-slate-200 dark:border-gray-700 rounded-2xl text-slate-400 hover:text-blue-500 hover:border-blue-500 transition-all text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2">
                                            <i class="fas fa-plus"></i> Add New Option
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <!-- Options for Matrix -->
                            <template x-if="form.question_type === 'matrix'">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="space-y-4">
                                        <label class="block text-[10px] font-black text-purple-600 uppercase tracking-widest">Rows (Items to evaluate)</label>
                                        <div class="space-y-3">
                                            <template x-for="(row, i) in form.options.rows" :key="i">
                                                <div class="flex items-center gap-2">
                                                    <input type="text" :name="'options[rows]['+i+']'" x-model="form.options.rows[i]" required
                                                        class="flex-grow bg-white dark:bg-gray-950 border border-slate-200 dark:border-gray-700 rounded-xl px-4 py-2 text-xs outline-none">
                                                    <button type="button" @click="removeMatrixItem('rows', i)" class="text-red-400 p-2">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </template>
                                            <button type="button" @click="addMatrixItem('rows')" class="text-purple-600 text-[10px] font-black uppercase">
                                                + Add Row
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <label class="block text-[10px] font-black text-cyan-600 uppercase tracking-widest">Columns (Performance levels)</label>
                                        <div class="space-y-3">
                                            <template x-for="(col, i) in form.options.cols" :key="i">
                                                <div class="flex items-center gap-2">
                                                    <input type="text" :name="'options[cols]['+i+']'" x-model="form.options.cols[i]" required
                                                        class="flex-grow bg-white dark:bg-gray-950 border border-slate-200 dark:border-gray-700 rounded-xl px-4 py-2 text-xs outline-none">
                                                    <button type="button" @click="removeMatrixItem('cols', i)" class="text-red-400 p-2">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </template>
                                            <button type="button" @click="addMatrixItem('cols')" class="text-cyan-600 text-[10px] font-black uppercase">
                                                + Add Column
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!['radio', 'select', 'checkbox', 'matrix'].includes(form.question_type)">
                                <div class="text-center py-4">
                                    <p class="text-sm text-slate-500 italic">No additional configuration required for this type.</p>
                                </div>
                            </template>
                        </div>

                        <!-- Conditional Logic -->
                        <div class="pt-8 border-t-2 border-slate-100 dark:border-gray-800">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h5 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest">Branching Logic</h5>
                                    <p class="text-xs text-slate-500 font-medium">Control the flow based on previous answers.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="hasLogic" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-500"></div>
                                </label>
                            </div>
    
                            <div x-show="hasLogic" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-4"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="bg-amber-500/5 dark:bg-amber-500/10 p-6 rounded-[2rem] border-2 border-dashed border-amber-500/30 space-y-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-3 italic">Show this question ONLY IF...</label>
                                        <select name="conditional_logic[show_if_question_id]" x-model="form.conditional_logic.show_if_question_id"
                                            class="w-full bg-white dark:bg-gray-900 border border-amber-500/30 rounded-2xl px-4 py-3 text-sm font-bold text-slate-800 dark:text-white outline-none focus:border-amber-500">
                                            <option value="">Select Target Question</option>
                                            <template x-for="prevQ in previousQuestions" :key="prevQ.id">
                                                <option :value="prevQ.id" x-text="'#' + (questions.indexOf(prevQ) + 1) + ': ' + prevQ.question_text"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-3 italic">Value is exactly...</label>
                                        <input type="text" name="conditional_logic[show_if_value]" x-model="form.conditional_logic.show_if_value"
                                            class="w-full bg-white dark:bg-gray-900 border border-amber-500/30 rounded-2xl px-4 py-3 text-sm font-bold text-slate-800 dark:text-white outline-none focus:border-amber-500"
                                            placeholder="e.g. Yes">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-5 sm:px-8 sm:py-6 bg-slate-50 dark:bg-gray-800/50 flex flex-col-reverse sm:flex-row-reverse gap-3 sm:gap-4 border-t border-slate-100 dark:border-gray-800">
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 sm:py-3 bg-blue-600 text-white rounded-2xl font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 active:scale-95">
                            Apply Changes
                        </button>
                        <button type="button" @click="modalOpen = false" class="w-full sm:w-auto px-6 py-3 text-slate-500 font-bold hover:bg-slate-200 dark:hover:bg-gray-700 rounded-2xl transition-all text-center">
                            Discard
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function formBuilder() {
    return {
        questions: @json($questions),
        modalOpen: false,
        isEdit: false,
        editingId: null,
        hasLogic: false,
        form: {
            question_text: '',
            question_type: 'text',
            is_required: true,
            options: [],
            conditional_logic: {
                show_if_question_id: '',
                show_if_value: ''
            }
        },

        getTypeIcon(type) {
            const icons = {
                'text': 'fas fa-font',
                'textarea': 'fas fa-align-left',
                'radio': 'fas fa-dot-circle',
                'select': 'fas fa-list-ul',
                'checkbox': 'fas fa-check-square',
                'date': 'fas fa-calendar-alt',
                'scale': 'fas fa-sliders-h',
                'matrix': 'fas fa-th-large'
            };
            return icons[type] || 'fas fa-question';
        },

        getTypeColor(type) {
            const colors = {
                'text': 'bg-blue-500',
                'textarea': 'bg-indigo-500',
                'radio': 'bg-cyan-500',
                'select': 'bg-teal-500',
                'checkbox': 'bg-green-500',
                'date': 'bg-orange-500',
                'scale': 'bg-pink-500',
                'matrix': 'bg-purple-500'
            };
            return colors[type] || 'bg-slate-500';
        },

        getTypeIconColor(type) {
            const colors = {
                'text': 'text-blue-500',
                'textarea': 'text-indigo-500',
                'radio': 'text-cyan-500',
                'select': 'text-teal-500',
                'checkbox': 'text-green-500',
                'date': 'text-orange-500',
                'scale': 'text-pink-500',
                'matrix': 'text-purple-500'
            };
            return colors[type] || 'text-slate-500';
        },

        getBadgeStyles(type) {
            const styles = {
                'text': 'bg-blue-500/10 text-blue-500 border border-blue-500/20',
                'textarea': 'bg-indigo-500/10 text-indigo-500 border border-indigo-500/20',
                'radio': 'bg-cyan-500/10 text-cyan-500 border border-cyan-500/20',
                'select': 'bg-teal-500/10 text-teal-500 border border-teal-500/20',
                'checkbox': 'bg-green-500/10 text-green-500 border border-green-500/20',
                'date': 'bg-orange-500/10 text-orange-500 border border-orange-500/20',
                'scale': 'bg-pink-500/10 text-pink-500 border border-pink-500/20',
                'matrix': 'bg-purple-500/10 text-purple-500 border border-purple-500/20'
            };
            return styles[type] || 'bg-slate-500/10 text-slate-500 border border-slate-500/20';
        },

        get previousQuestions() {
            if (!this.isEdit) return this.questions;
            return this.questions.filter(q => q.id !== this.editingId);
        },

        getQuestionIndex(id) {
            const idx = this.questions.findIndex(q => q.id == id);
            return idx !== -1 ? (idx + 1) : '?';
        },

        openCreateModal() {
            this.isEdit = false;
            this.editingId = null;
            this.hasLogic = false;
            this.form = {
                question_text: '',
                question_type: 'text',
                is_required: true,
                options: [],
                conditional_logic: { show_if_question_id: '', show_if_value: '' }
            };
            this.modalOpen = true;
        },

        openEditModal(q) {
            this.isEdit = true;
            this.editingId = q.id;
            this.form = JSON.parse(JSON.stringify(q));
            
            if (!this.form.options) {
                if (this.form.question_type === 'matrix') {
                    this.form.options = { rows: [], cols: [] };
                } else {
                    this.form.options = [];
                }
            }

            if (this.form.conditional_logic && this.form.conditional_logic.show_if_question_id) {
                this.hasLogic = true;
            } else {
                this.hasLogic = false;
                this.form.conditional_logic = { show_if_question_id: '', show_if_value: '' };
            }

            this.modalOpen = true;
        },

        addOption() {
            if (!Array.isArray(this.form.options)) this.form.options = [];
            this.form.options.push('');
        },

        removeOption(index) {
            this.form.options.splice(index, 1);
        },

        addMatrixItem(type) {
            if (!this.form.options[type]) this.form.options[type] = [];
            this.form.options[type].push('');
        },

        removeMatrixItem(type, index) {
            this.form.options[type].splice(index, 1);
        },

        async moveUp(index) {
            if (index === 0) return;
            const item = this.questions.splice(index, 1)[0];
            this.questions.splice(index - 1, 0, item);
            await this.saveOrder();
        },

        async moveDown(index) {
            if (index === this.questions.length - 1) return;
            const item = this.questions.splice(index, 1)[0];
            this.questions.splice(index + 1, 0, item);
            await this.saveOrder();
        },

        async saveOrder() {
            const orders = this.questions.map((q, i) => ({ id: q.id, order: i + 1 }));
            await fetch('{{ route('join-questions.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ orders })
            });
        }
    }
}
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); }
.shadow-3xl { shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.3); }
</style>
@endsection
