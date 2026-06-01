@extends('layouts.batu')
@section('title', 'Required: ' . $form->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    
    <!-- Mandatory Form Banner -->
    <div class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-6 mb-6 flex items-start gap-4">
        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center shrink-0">
            <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
        </div>
        <div>
            <h2 class="font-bold text-amber-800 text-lg">Mandatory Form</h2>
            <p class="text-amber-700 text-sm mt-1">You must complete this form before you can access the rest of the platform. This is required by your team leadership.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 border border-red-200">
            <div class="font-bold mb-2"><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</div>
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-200 flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 border-t-8 border-t-amber-500 mb-6">
        <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100 mb-4">{{ $form->title }}</h1>
        @if($form->description)
            <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $form->description }}</p>
        @endif
        @if($form->deadline)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-sm text-orange-600 font-medium flex items-center gap-2">
                <i class="fas fa-clock"></i> Deadline: {{ $form->deadline->format('l, M d, Y g:i A') }}
            </div>
        @endif
    </div>

    <form action="{{ route('forms.mandatory.store', $form->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @foreach($form->questions as $question)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 {{ $errors->has('answers.'.$question->id) ? 'border-red-500' : '' }}">
                <div class="mb-4">
                    <label class="text-lg font-bold text-gray-800 dark:text-gray-200 block">
                        {{ $question->title }}
                        @if($question->is_required)
                            <span class="text-red-500 ml-1">*</span>
                        @endif
                    </label>
                </div>

                @php $ans = old('answers.'.$question->id); @endphp

                @if($question->type === 'short_answer')
                    <input type="text" name="answers[{{ $question->id }}]" value="{{ is_string($ans) ? $ans : '' }}" class="w-full border-0 border-b-2 border-gray-200 focus:border-[#2596be] focus:ring-0 px-0 py-2 bg-transparent text-gray-900 dark:text-gray-100" {{ $question->is_required ? 'required' : '' }}>
                
                @elseif($question->type === 'paragraph')
                    <textarea name="answers[{{ $question->id }}]" class="w-full border-0 border-b-2 border-gray-200 focus:border-[#2596be] focus:ring-0 px-0 py-2 bg-transparent text-gray-900 dark:text-gray-100 resize-y" rows="3" {{ $question->is_required ? 'required' : '' }}>{{ is_string($ans) ? $ans : '' }}</textarea>
                
                @elseif($question->type === 'multiple_choice')
                    <div class="space-y-3">
                        @foreach($question->options as $opt)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt }}" {{ $ans === $opt ? 'checked' : '' }} class="text-[#2596be] focus:ring-[#2596be] w-5 h-5" {{ $question->is_required ? 'required' : '' }}>
                                <span class="text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                
                @elseif($question->type === 'checkboxes')
                    <div class="space-y-3">
                        @php $ansArr = is_array($ans) ? $ans : []; @endphp
                        @foreach($question->options as $opt)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $opt }}" {{ in_array($opt, $ansArr) ? 'checked' : '' }} class="text-[#2596be] focus:ring-[#2596be] rounded w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                
                @elseif($question->type === 'dropdown')
                    <select name="answers[{{ $question->id }}]" class="w-full border border-gray-300 rounded-xl focus:border-[#2596be] focus:ring-[#2596be] px-4 py-3 bg-white dark:bg-gray-900" {{ $question->is_required ? 'required' : '' }}>
                        <option value="">Choose an option</option>
                        @foreach($question->options as $opt)
                            <option value="{{ $opt }}" {{ $ans === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>

                @elseif($question->type === 'file_upload')
                    <div x-data="{ previewUrl: '' }" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <input type="file" name="answers[{{ $question->id }}]" id="file_{{ $question->id }}" class="hidden" 
                               @change="if($event.target.files.length) previewUrl = URL.createObjectURL($event.target.files[0])"
                               {{ $question->is_required ? 'required' : '' }}>
                        <label for="file_{{ $question->id }}" class="cursor-pointer block">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3" x-show="!previewUrl"></i>
                            <div class="text-[#2596be] font-bold" x-text="previewUrl ? 'Click to change file' : 'Click to upload file'"></div>
                            <div class="text-xs text-gray-500 mt-1">Maximum file size: 10MB</div>
                            
                            <template x-if="previewUrl">
                                <div class="mt-4 flex justify-center">
                                    <img :src="previewUrl" class="max-h-48 rounded-lg shadow-sm object-contain" alt="Preview">
                                </div>
                            </template>
                        </label>
                    </div>

                @elseif($question->type === 'date')
                    <input type="date" name="answers[{{ $question->id }}]" value="{{ is_string($ans) ? $ans : '' }}" class="border border-gray-300 rounded-xl focus:border-[#2596be] focus:ring-[#2596be] px-4 py-3" {{ $question->is_required ? 'required' : '' }}>

                @elseif($question->type === 'time')
                    <input type="time" name="answers[{{ $question->id }}]" value="{{ is_string($ans) ? $ans : '' }}" class="border border-gray-300 rounded-xl focus:border-[#2596be] focus:ring-[#2596be] px-4 py-3" {{ $question->is_required ? 'required' : '' }}>

                @elseif($question->type === 'rating')
                    <div class="flex items-center gap-4 text-3xl" x-data="{ rating: {{ is_numeric($ans) ? $ans : 0 }}, hover: 0 }">
                        <input type="hidden" name="answers[{{ $question->id }}]" x-model="rating" {{ $question->is_required ? 'required' : '' }}>
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                @click="rating = {{ $i }}" 
                                @mouseover="hover = {{ $i }}" 
                                @mouseleave="hover = 0"
                                class="text-gray-300 transition"
                                :class="{ 'text-yellow-400': hover >= {{ $i }} || (!hover && rating >= {{ $i }}) }">
                                <i class="fas fa-star"></i>
                            </button>
                        @endfor
                    </div>
                @endif
            </div>
        @endforeach

        <div class="flex justify-end pt-4 mb-16">
            <button type="submit" class="btn-primary bg-amber-500 hover:bg-amber-600 text-white px-8 py-3 rounded-xl font-bold transition flex items-center gap-2 shadow-lg shadow-amber-500/30 text-lg">
                <i class="fas fa-paper-plane"></i> Submit Required Form
            </button>
        </div>
    </form>
</div>
@endsection
