@extends('layouts.batu')
@section('title', 'My Forms')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100 flex items-center gap-3">
            <i class="fas fa-tasks text-[#2596be]"></i> Available Forms
        </h1>
        <p class="text-gray-500 mt-1">View and respond to forms assigned to you.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-200 flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-lg"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($forms as $form)
            @php
                $existingResponse = $form->responses->first();
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition flex flex-col h-full border-t-4 {{ $existingResponse ? 'border-t-green-500' : 'border-t-[#2596be]' }}">
                <div class="p-6 flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-xl text-gray-900 dark:text-gray-100 line-clamp-2">{{ $form->title }}</h3>
                        <div class="flex flex-col items-end gap-1 shrink-0">
                            @if($form->is_required && !$existingResponse)
                                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider"><i class="fas fa-lock mr-1"></i> Required</span>
                            @endif
                            @if($existingResponse)
                                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider"><i class="fas fa-check mr-1"></i> Submitted</span>
                            @endif
                        </div>
                    </div>
                    
                    <p class="text-gray-500 text-sm line-clamp-3 mb-4">{{ $form->description ?? 'No description provided.' }}</p>
                    
                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-auto">
                        <i class="fas fa-clock"></i>
                        @if($form->deadline)
                            <span class="{{ $form->deadline < now() ? 'text-red-500 font-bold' : '' }}">
                                Deadline: {{ $form->deadline->format('M d, Y') }}
                            </span>
                        @else
                            <span>No Deadline</span>
                        @endif
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    @if($existingResponse && !$form->allow_edit_response)
                        <button disabled class="w-full py-2 bg-gray-200 text-gray-500 rounded-xl font-bold cursor-not-allowed">
                            Already Submitted
                        </button>
                    @elseif($form->deadline && $form->deadline < now() && !$existingResponse)
                        <button disabled class="w-full py-2 bg-red-100 text-red-600 rounded-xl font-bold cursor-not-allowed">
                            Deadline Passed
                        </button>
                    @else
                        <a href="{{ route('forms.member.show', $form->id) }}" class="w-full block text-center py-2 bg-[#2596be] hover:bg-[#1a7a9c] text-white rounded-xl font-bold transition shadow-sm">
                            {{ $existingResponse ? 'Edit Response' : 'Fill Form' }}
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <i class="fas fa-inbox text-3xl"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-700 dark:text-gray-300">No pending forms</h3>
                <p class="text-sm text-gray-500 mt-1">You're all caught up! There are no forms assigned to you at the moment.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
