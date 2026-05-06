@extends('layouts.batu')
@section('title', isset($form) ? 'Edit Form' : 'Create Form')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<style>
    .sortable-ghost { opacity: 0.4; background-color: #f3f4f6; }
    .question-card { transition: all 0.2s ease; }
    .question-card:focus-within { border-color: #2596be; box-shadow: 0 4px 6px -1px rgba(37, 150, 190, 0.1); }
</style>

<div class="max-w-4xl mx-auto px-4 py-8" x-data="formBuilder()">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('forms.manage.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Forms
        </a>
        <button @click="saveForm" class="btn-primary bg-[#2596be] hover:bg-[#1a7a9c] text-white px-6 py-2.5 rounded-xl font-bold transition flex items-center gap-2 shadow-lg shadow-[#2596be]/30" :class="{ 'opacity-50 cursor-not-allowed': isSaving }" :disabled="isSaving">
            <i class="fas fa-save" x-show="!isSaving"></i>
            <i class="fas fa-spinner fa-spin" x-show="isSaving"></i>
            <span x-text="isSaving ? 'Saving...' : 'Save Form'"></span>
        </button>
    </div>

    <!-- Error Messages -->
    <div x-show="errors.length > 0" class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 border border-red-200" style="display: none;">
        <div class="font-bold mb-2"><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</div>
        <ul class="list-disc pl-5 text-sm space-y-1">
            <template x-for="error in errors">
                <li x-text="error"></li>
            </template>
        </ul>
    </div>

    <!-- Form Settings -->
    <div class="bg-white dark:bg-gray-800 rounded-t-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 border-t-8 border-t-[#2596be] mb-6">
        <input type="text" x-model="form.title" placeholder="Form Title" class="w-full text-4xl font-black text-gray-900 dark:text-gray-100 bg-transparent border-0 border-b-2 border-transparent hover:border-gray-200 focus:border-[#2596be] focus:ring-0 px-0 py-2 transition mb-4">
        
        <textarea x-model="form.description" placeholder="Form Description (Optional)" class="w-full text-gray-600 dark:text-gray-400 bg-transparent border-0 border-b-2 border-transparent hover:border-gray-200 focus:border-[#2596be] focus:ring-0 px-0 py-2 transition resize-none h-20"></textarea>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-cog text-gray-500"></i> Settings</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                <input type="datetime-local" x-model="form.deadline" class="w-full rounded-lg border-gray-300 focus:ring-[#2596be] focus:border-[#2596be]">
            </div>
            
            <div class="flex items-center gap-3 pt-8">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" x-model="form.allow_edit_response" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2596be]"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700">Allow Response Editing</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Questions Area -->
    <div id="questionsList" class="space-y-6">
        <template x-for="(question, index) in questions" :key="question.id">
            <div class="question-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative group" :data-id="question.id">
                
                <!-- Drag Handle -->
                <div class="absolute top-2 left-1/2 -translate-x-1/2 text-gray-300 cursor-grab hover:text-gray-500 drag-handle p-1 opacity-0 group-hover:opacity-100 transition">
                    <i class="fas fa-grip-horizontal"></i>
                </div>

                <div class="flex flex-col md:flex-row gap-4 mt-4">
                    <div class="flex-grow">
                        <input type="text" x-model="question.title" placeholder="Question Title" class="w-full text-lg font-medium bg-gray-50 dark:bg-gray-900 border border-gray-200 rounded-xl px-4 py-3 focus:ring-[#2596be] focus:border-[#2596be]">
                    </div>
                    <div class="w-full md:w-48 shrink-0">
                        <select x-model="question.type" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-[#2596be] focus:border-[#2596be]">
                            <option value="short_answer">Short Answer</option>
                            <option value="paragraph">Paragraph</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="checkboxes">Checkboxes</option>
                            <option value="dropdown">Dropdown</option>
                            <option value="file_upload">File Upload</option>
                            <option value="date">Date</option>
                            <option value="time">Time</option>
                            <option value="rating">Rating</option>
                        </select>
                    </div>
                </div>

                <!-- Dynamic Input Preview / Options -->
                <div class="mt-6 pl-4 border-l-2 border-gray-100">
                    
                    <!-- Text Preview -->
                    <template x-if="['short_answer', 'date', 'time', 'file_upload'].includes(question.type)">
                        <div class="text-gray-400 text-sm border-b border-dotted border-gray-300 pb-2 w-1/2">
                            <span x-text="question.type === 'short_answer' ? 'Short answer text' : (question.type === 'file_upload' ? 'File upload area' : 'Date/Time input')"></span>
                        </div>
                    </template>
                    
                    <template x-if="question.type === 'paragraph'">
                        <div class="text-gray-400 text-sm border-b border-dotted border-gray-300 pb-8 w-3/4">
                            Long answer text
                        </div>
                    </template>

                    <!-- Options Editor (Multiple Choice, Checkboxes, Dropdown) -->
                    <template x-if="['multiple_choice', 'checkboxes', 'dropdown'].includes(question.type)">
                        <div class="space-y-3">
                            <template x-for="(opt, oIndex) in question.options" :key="oIndex">
                                <div class="flex items-center gap-3">
                                    <div class="text-gray-300">
                                        <i class="far fa-circle" x-show="question.type === 'multiple_choice'"></i>
                                        <i class="far fa-square" x-show="question.type === 'checkboxes'"></i>
                                        <span x-show="question.type === 'dropdown'" x-text="(oIndex + 1) + '.'"></span>
                                    </div>
                                    <input type="text" x-model="question.options[oIndex]" class="border-0 border-b border-gray-200 focus:border-[#2596be] focus:ring-0 px-0 py-1 w-2/3 text-sm">
                                    <button @click="removeOption(question, oIndex)" x-show="question.options.length > 1" class="text-gray-400 hover:text-red-500 p-1">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                            <div class="flex items-center gap-3 mt-2">
                                <div class="text-gray-300">
                                    <i class="far fa-circle" x-show="question.type === 'multiple_choice'"></i>
                                    <i class="far fa-square" x-show="question.type === 'checkboxes'"></i>
                                    <span x-show="question.type === 'dropdown'" x-text="(question.options.length + 1) + '.'"></span>
                                </div>
                                <button @click="addOption(question)" class="text-sm text-gray-400 hover:text-[#2596be] border-b border-transparent hover:border-[#2596be] pb-0.5">
                                    Add option
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="question.type === 'rating'">
                        <div class="flex gap-2 text-gray-300 text-2xl">
                            <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                        </div>
                    </template>
                </div>

                <!-- Footer Actions -->
                <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-end gap-4">
                    <button @click="duplicateQuestion(index)" class="text-gray-400 hover:text-gray-700" title="Duplicate">
                        <i class="far fa-copy"></i>
                    </button>
                    <button @click="removeQuestion(index)" class="text-gray-400 hover:text-red-500" title="Delete">
                        <i class="far fa-trash-alt"></i>
                    </button>
                    
                    <div class="w-px h-6 bg-gray-200"></div>
                    
                    <label class="flex items-center gap-2 cursor-pointer">
                        <span class="text-sm text-gray-600">Required</span>
                        <div class="relative inline-flex items-center">
                            <input type="checkbox" x-model="question.is_required" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#2596be]"></div>
                        </div>
                    </label>
                </div>
            </div>
        </template>
    </div>

    <!-- Floating Add Button -->
    <div class="fixed bottom-8 right-8 flex flex-col gap-3 z-50">
        <button @click="addQuestion" class="w-14 h-14 bg-white text-[#2596be] rounded-full shadow-xl border border-gray-100 flex items-center justify-center text-xl hover:bg-gray-50 hover:scale-105 transition transform tooltip" data-tip="Add Question">
            <i class="fas fa-plus"></i>
        </button>
    </div>

</div>

<script>
function formBuilder() {
    return {
        form: {
            id: {{ isset($form) ? $form->id : 'null' }},
            title: {!! isset($form) ? json_encode($form->title) : "'Untitled Form'" !!},
            description: {!! isset($form) ? json_encode($form->description) : "''" !!},
            deadline: {!! isset($form) && $form->deadline ? json_encode($form->deadline->format('Y-m-d\TH:i')) : "''" !!},
            allow_edit_response: {{ isset($form) && $form->allow_edit_response ? 'true' : 'false' }},
            is_active: true
        },
        questions: {!! isset($form) ? json_encode($form->questions) : "[{ id: 'new_' + Date.now(), title: '', type: 'multiple_choice', options: ['Option 1'], is_required: false }]" !!},
        isSaving: false,
        errors: [],
        
        init() {
            // Ensure options array exists for existing questions
            this.questions.forEach(q => {
                if (['multiple_choice', 'checkboxes', 'dropdown'].includes(q.type) && (!q.options || !Array.isArray(q.options))) {
                    q.options = ['Option 1'];
                }
            });

            this.$nextTick(() => {
                let el = document.getElementById('questionsList');
                Sortable.create(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: (evt) => {
                        // Reorder array
                        const movedItem = this.questions.splice(evt.oldIndex, 1)[0];
                        this.questions.splice(evt.newIndex, 0, movedItem);
                    }
                });
            });
        },
        
        addQuestion() {
            this.questions.push({
                id: 'new_' + Date.now(),
                title: '',
                type: 'multiple_choice',
                options: ['Option 1'],
                is_required: false
            });
            
            this.$nextTick(() => {
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            });
        },
        
        duplicateQuestion(index) {
            let q = JSON.parse(JSON.stringify(this.questions[index]));
            q.id = 'new_' + Date.now();
            if (q.db_id) delete q.db_id;
            this.questions.splice(index + 1, 0, q);
        },
        
        removeQuestion(index) {
            if (this.questions.length > 1) {
                this.questions.splice(index, 1);
            }
        },
        
        addOption(question) {
            if (!question.options) question.options = [];
            question.options.push(`Option ${question.options.length + 1}`);
        },
        
        removeOption(question, index) {
            question.options.splice(index, 1);
        },
        
        async saveForm() {
            this.errors = [];
            if (!this.form.title.trim()) this.errors.push("Form Title is required.");
            if (this.questions.length === 0) this.errors.push("At least one question is required.");
            
            this.questions.forEach((q, i) => {
                if (!q.title.trim()) this.errors.push(`Question ${i + 1} title is required.`);
                if (['multiple_choice', 'checkboxes', 'dropdown'].includes(q.type) && (!q.options || q.options.length === 0)) {
                    this.errors.push(`Question ${i + 1} must have at least one option.`);
                }
            });
            
            if (this.errors.length > 0) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }
            
            this.isSaving = true;
            
            const payload = {
                _token: '{{ csrf_token() }}',
                title: this.form.title,
                description: this.form.description,
                deadline: this.form.deadline || null,
                allow_edit_response: this.form.allow_edit_response,
                is_active: this.form.is_active,
                questions: this.questions.map(q => ({
                    id: String(q.id).startsWith('new_') ? null : q.id,
                    title: q.title,
                    type: q.type,
                    is_required: q.is_required,
                    options: q.options
                }))
            };
            
            const url = this.form.id ? `{{ url('/forms/manage') }}/${this.form.id}` : `{{ route('forms.manage.store') }}`;
            const method = this.form.id ? 'PUT' : 'POST';
            
            if (this.form.id) {
                payload._method = 'PUT';
            }
            
            try {
                const response = await fetch(url, {
                    method: 'POST', // always POST for fetch, using _method spoofing
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    window.location.href = data.redirect;
                } else {
                    this.errors.push(data.message || 'An error occurred while saving the form.');
                    this.isSaving = false;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } catch (error) {
                this.errors.push('Network error occurred.');
                this.isSaving = false;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    }
}
</script>
@endsection
