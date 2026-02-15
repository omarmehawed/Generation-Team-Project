<div
    class="bg-gray-800 rounded-xl p-4 border border-gray-700 hover:border-{{ $color }}-500 transition-colors relative group">
    <div class="flex justify-between items-start mb-3">
        <h3 class="font-bold text-white text-lg truncate pr-2">{{ $task->title }}</h3>
        <span
            class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-{{ $task->status_color }}-500/20 text-{{ $task->status_color }}-400">
            {{ $task->status_text }}
        </span>
    </div>

    <p class="text-gray-400 text-sm mb-4 line-clamp-2 h-10">{{ $task->description ?? 'No description provided.' }}</p>

    {{-- File / Link --}}
    @if($task->submission_file)
        <div class="bg-black/30 rounded p-2 mb-3 flex items-center justify-between border border-gray-700">
            <div class="flex items-center gap-2 overflow-hidden">
                <i class="fas fa-file-alt text-gray-500"></i>
                <span class="text-xs text-gray-300 truncate">{{ basename($task->submission_file) }}</span>
            </div>
            <a href="{{ route('tasks.download', $task->id) }}" class="text-cyan-400 hover:text-cyan-300 text-xs">
                <i class="fas fa-download"></i>
            </a>
        </div>
    @elseif($task->submission_value)
        <div class="bg-black/30 rounded p-2 mb-3 flex items-center justify-between border border-gray-700">
            <div class="flex items-center gap-2 overflow-hidden">
                <i class="fas fa-link text-gray-500"></i>
                <a href="{{ $task->submission_value }}" target="_blank"
                    class="text-xs text-cyan-400 hover:underline truncate">{{ $task->submission_value }}</a>
            </div>
        </div>
    @endif

    {{-- Footer: User & Grader --}}
    <div class="flex items-center justify-between pt-3 border-t border-gray-700 mt-auto">
        <div class="flex items-center gap-2" title="Submitted By">
            <x-user-avatar :user="$task->user" size="w-5 h-5" />
            <span class="text-[10px] text-gray-400">{{ $task->user->name ?? 'Team Task' }}</span>
        </div>

        @if($task->grader)
            <div class="flex items-center gap-1" title="Approved By {{ $task->grader->name }}">
                <i class="fas fa-check-circle text-green-500 text-xs"></i>
                <span class="text-[10px] text-green-400">{{ Str::limit($task->grader->name, 10) }}</span>
            </div>
        @elseif($task->status == 'pending')
            <span class="text-[10px] text-yellow-500"><i class="fas fa-clock"></i> Pending</span>
        @endif
    </div>
</div>