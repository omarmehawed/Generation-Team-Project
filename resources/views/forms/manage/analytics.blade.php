@extends('layouts.batu')
@section('title', 'Form Analytics - ' . $form->title)

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <a href="{{ route('forms.manage.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Forms
        </a>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <span class="px-4 py-1.5 rounded-full text-sm font-bold {{ $form->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $form->is_active ? 'Active' : 'Inactive' }}
            </span>
            <a href="{{ route('forms.manage.edit', $form->id) }}" class="btn-primary bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2 rounded-xl font-bold transition flex items-center gap-2 shadow-lg shadow-indigo-500/30">
                <i class="fas fa-edit"></i> Edit Form
            </a>
        </div>
    </div>

    <!-- Stats Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 mb-8 border-t-8 border-t-[#2596be]">
        <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100 mb-2">{{ $form->title }}</h1>
        <p class="text-gray-500">{{ $form->description }}</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                <div class="text-blue-500 mb-2"><i class="fas fa-users text-2xl"></i></div>
                <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $totalResponses }}</div>
                <div class="text-sm text-gray-500 font-medium">Total Responses</div>
            </div>
            
            <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-2xl border border-purple-100 dark:border-purple-800">
                <div class="text-purple-500 mb-2"><i class="fas fa-question-circle text-2xl"></i></div>
                <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $form->questions->count() }}</div>
                <div class="text-sm text-gray-500 font-medium">Questions</div>
            </div>

            <div class="bg-orange-50 dark:bg-orange-900/20 p-6 rounded-2xl border border-orange-100 dark:border-orange-800">
                <div class="text-orange-500 mb-2"><i class="fas fa-clock text-2xl"></i></div>
                <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $form->deadline ? $form->deadline->format('M d, Y g:i A') : 'No Deadline' }}</div>
                <div class="text-sm text-gray-500 font-medium mt-1">Deadline</div>
            </div>
        </div>
    </div>

    <!-- Charts / Questions Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        @foreach($form->questions as $question)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4">{{ $question->title }}</h3>
                
                @if(isset($chartData[$question->id]) && count($chartData[$question->id]['labels']) > 0)
                    <div class="h-64 relative w-full flex justify-center">
                        <canvas id="chart-{{ $question->id }}"></canvas>
                    </div>
                @elseif(in_array($question->type, ['short_answer', 'paragraph', 'file_upload']))
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 max-h-64 overflow-y-auto space-y-3">
                        @forelse($question->answers->take(10) as $answer)
                            @if($question->type === 'file_upload' && $answer->answer_file)
                                @php
                                    $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $answer->answer_file);
                                @endphp
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        @if($isImage)
                                            <img src="{{ Storage::url($answer->answer_file) }}" alt="Thumbnail" class="w-10 h-10 object-cover rounded shadow-sm">
                                        @else
                                            <i class="fas fa-file-alt text-[#2596be] text-2xl"></i>
                                        @endif
                                        <span class="text-sm text-gray-700 dark:text-gray-300">File uploaded by <strong>{{ $answer->response->user->name }}</strong></span>
                                    </div>
                                    <a href="{{ Storage::url($answer->answer_file) }}" target="_blank" class="text-blue-500 hover:underline text-sm font-bold shrink-0">View File</a>
                                </div>
                            @elseif($answer->answer_text)
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300">
                                    "{{ $answer->answer_text }}"
                                </div>
                            @endif
                        @empty
                            <div class="text-center text-gray-400 py-4 italic">No answers yet.</div>
                        @endforelse
                        @if($question->answers->count() > 10)
                            <div class="text-center text-xs text-gray-500 font-bold pt-2">Showing 10 most recent</div>
                        @endif
                    </div>
                @else
                    <div class="h-64 flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-chart-pie text-4xl mb-3 opacity-50"></i>
                        <p>Not enough data for chart.</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Individual Responses Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <i class="fas fa-list-ul text-[#2596be]"></i> Individual Responses
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">User</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Submitted At</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($form->responses as $response)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-gray-100">{{ $response->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $response->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $response->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('forms.manage.response.show', $response->id) }}" class="text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg transition-colors inline-block">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">No responses received.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Activity Logs -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <i class="fas fa-history text-gray-500"></i> Audit Logs
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($logs as $log)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                            @if($log->action === 'Created') <i class="fas fa-plus text-green-500"></i>
                            @elseif($log->action === 'Updated') <i class="fas fa-pen text-blue-500"></i>
                            @else <i class="fas fa-info text-gray-500"></i> @endif
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $log->causer->name ?? 'System' }} {{ strtolower($log->action) }} this form
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $log->created_at->diffForHumans() }} - {{ $log->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500 text-sm italic">No activity recorded.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    
    // Modern vibrant colors
    const colors = [
        '#2596be', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#f43f5e', '#14b8a6'
    ];

    Object.keys(chartData).forEach(questionId => {
        const data = chartData[questionId];
        const ctx = document.getElementById('chart-' + questionId);
        
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: colors.slice(0, data.labels.length),
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: window.innerWidth < 768 ? 'bottom' : 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 12
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    });
});
</script>
@endsection
