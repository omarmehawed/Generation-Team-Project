@extends('layouts.batu')
@section('title', 'Manage Forms')

@section('content')
<div class="px-8 py-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-gray-100 flex items-center gap-3">
                <i class="fas fa-file-alt text-[#2596be]"></i> Manage Forms
            </h1>
            <p class="text-gray-500 text-sm mt-1">Create and manage custom forms for your teams</p>
        </div>
        <a href="{{ route('forms.manage.create') }}" class="btn-primary bg-[#2596be] hover:bg-[#1a7a9c] text-white px-5 py-2.5 rounded-xl font-bold transition flex items-center gap-2 shadow-lg shadow-[#2596be]/30 w-full sm:w-auto justify-center">
            <i class="fas fa-plus"></i> Create New Form
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-200 flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Form Title</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Responses</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Deadline</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($forms as $form)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 dark:text-gray-100 text-lg">{{ $form->title }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($form->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('forms.manage.toggle-status', $form->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-1.5 text-xs font-bold rounded-full transition {{ $form->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $form->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                    {{ $form->responses_count }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium {{ $form->deadline && $form->deadline < now() ? 'text-red-500' : 'text-gray-600 dark:text-gray-400' }}">
                            {{ $form->deadline ? $form->deadline->format('M d, Y') : 'No Deadline' }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('forms.manage.analytics', $form->id) }}" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition" title="Analytics">
                                <i class="fas fa-chart-pie"></i>
                            </a>
                            <a href="{{ route('forms.manage.edit', $form->id) }}" class="text-indigo-500 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition" title="Edit Builder">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="{{ route('forms.manage.destroy', $form->id) }}" method="POST" class="inline-block" onsubmit="return confirmFormSubmit(event, this, 'Are you sure you want to delete this form entirely?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 text-[#2596be] opacity-50">
                                <i class="fas fa-folder-open text-3xl"></i>
                            </div>
                            <p class="font-bold text-lg text-gray-700 dark:text-gray-300">No forms created yet</p>
                            <p class="text-sm text-gray-400 mt-1">Get started by creating your first custom form.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
