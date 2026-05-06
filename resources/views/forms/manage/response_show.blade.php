@extends('layouts.batu')
@section('title', 'Response Details - ' . $response->form->title)

@section('content')
<div class="px-8 py-6 max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100 flex items-center gap-3">
                <i class="fas fa-file-invoice text-[#2596be]"></i> Response Details
            </h1>
            <p class="text-gray-500 text-sm mt-1">Submitted by <strong>{{ $response->user->name }}</strong> for the form <strong>{{ $response->form->title }}</strong></p>
        </div>
        <a href="{{ route('forms.manage.analytics', $response->form->id) }}" class="btn-secondary bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-5 py-2.5 rounded-xl font-bold transition flex items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left"></i> Back to Analytics
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100 dark:border-gray-700">
            <div class="w-16 h-16 rounded-full bg-[#2596be]/10 flex items-center justify-center text-[#2596be] text-2xl font-bold">
                {{ substr($response->user->name, 0, 1) }}
            </div>
            <div>
                <h3 class="font-bold text-xl text-gray-900 dark:text-gray-100">{{ $response->user->name }}</h3>
                <p class="text-sm text-gray-500">{{ $response->user->email }}</p>
                <div class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                    <i class="fas fa-clock"></i> Submitted on {{ $response->created_at->format('M d, Y h:i A') }}
                </div>
            </div>
        </div>

        <div class="space-y-8">
            @foreach($response->answers as $index => $answer)
                <div class="p-5 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700">
                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-start gap-2">
                        <span class="text-[#2596be]">{{ $index + 1 }}.</span> 
                        {{ $answer->question->question_text }}
                    </h4>
                    
                    <div class="ml-6 text-gray-700 dark:text-gray-300">
                        @if($answer->question->type === 'file_upload')
                            @if($answer->answer_file)
                                @php
                                    $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $answer->answer_file);
                                @endphp
                                @if($isImage)
                                    <div class="mb-3">
                                        <img src="{{ Storage::url($answer->answer_file) }}" alt="Uploaded Image" class="max-h-64 rounded-lg shadow-sm object-contain">
                                    </div>
                                @endif
                                <a href="{{ Storage::url($answer->answer_file) }}" target="_blank" class="inline-flex items-center gap-2 bg-[#2596be]/10 text-[#2596be] px-4 py-2 rounded-lg font-medium hover:bg-[#2596be]/20 transition">
                                    <i class="fas fa-paperclip"></i> View Uploaded File
                                </a>
                            @else
                                <span class="text-gray-400 italic">No file uploaded</span>
                            @endif
                        @elseif($answer->question->type === 'checkboxes')
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($answer->answer_json ?? [] as $val)
                                    <li>{{ $val }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="whitespace-pre-line bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">{{ $answer->answer_text ?: 'N/A' }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
