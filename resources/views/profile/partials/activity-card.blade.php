@props(['activity'])

@php
    $user = auth()->user();

    // 1. Determine Icon & Color based on Action
    $icon = 'fas fa-history';
    $color = 'text-gray-500';
    $bgColor = 'bg-gray-100';
    $message = $activity->description;

    switch ($activity->action) {
        case 'task_assigned':
            $icon = 'fas fa-tasks';
            $color = 'text-blue-500';
            $bgColor = 'bg-blue-50';
            break;
        case 'task_submitted':
            $icon = 'fas fa-file-upload';
            $color = 'text-purple-500';
            $bgColor = 'bg-purple-50';
            break;
        case 'task_approved':
            $icon = 'fas fa-check-double';
            $color = 'text-green-500';
            $bgColor = 'bg-green-50';
            break;
        case 'task_rejected':
            $icon = 'fas fa-times-circle';
            $color = 'text-red-500';
            $bgColor = 'bg-red-50';
            break;
        case 'proposal_submitted':
            $icon = 'fas fa-file-contract';
            $color = 'text-indigo-600';
            $bgColor = 'bg-indigo-50';
            break;
        case 'expense_added':
            $icon = 'fas fa-receipt';
            $color = 'text-yellow-600';
            $bgColor = 'bg-yellow-50';
            break;
        case 'gallery_upload':
            $icon = 'fas fa-images';
            $color = 'text-pink-500';
            $bgColor = 'bg-pink-50';
            break;
        case 'member_approved':
            $icon = 'fas fa-user-check';
            $color = 'text-teal-500';
            $bgColor = 'bg-teal-50';
            break;
        case 'team_created':
            $icon = 'fas fa-flag';
            $color = 'text-orange-500';
            $bgColor = 'bg-orange-50';
            break;
    }

    // 2. Determine "Who" (Causer)
    $causer = $activity->causer;
    $causerName = $causer ? ($causer->id == $user->id ? 'You' : $causer->name) : 'System';
    $causerAvatar = $causer ? "https://ui-avatars.com/api/?name=" . urlencode($causer->name) . "&background=random" : null;

    // 3. Project Context
    $projectTag = $activity->team ? $activity->team->name : 'General';
@endphp

<div class="relative pl-8 py-4 group">
    <!-- Timeline Line -->
    <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-gray-200 group-last:bottom-auto group-last:h-4"></div>

    <!-- Icon -->
    <div
        class="absolute left-0 top-5 w-6 h-6 rounded-full {{ $bgColor }} {{ $color }} flex items-center justify-center shadow-sm border border-white">
        <i class="{{ $icon }} text-[10px]"></i>
    </div>

    <!-- Card Body -->
    <div
        class="glass-card hover:shadow-lg transition-all duration-300 rounded-xl p-4 border border-slate-100 flex flex-col gap-3">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                @if($activity->causer)
                    <x-user-avatar :user="$activity->causer" size="w-6 h-6" classes="border border-gray-100 shadow-sm" />
                @endif
                <span class="text-sm font-bold text-slate-700">
                    {{ $causerName }}
                </span>
                <span class="text-xs text-slate-400">• {{ $activity->created_at->diffForHumans() }}</span>
            </div>

            <!-- Project Tag -->
            <span
                class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                {{ $projectTag }}
            </span>
        </div>

        <!-- Content -->
        <div class="text-sm text-slate-600 pl-8 relative">
            <!-- Connector Line specific to content -->
            <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-slate-100/50"></div>

            <p>{{ $message }}</p>

            <!-- Metadata / Specific Properties -->
            @if($activity->changes)
                <div class="mt-2 text-xs bg-slate-50/50 rounded-lg p-2 border border-slate-100/50 inline-block">
                    @foreach($activity->changes as $key => $value)
                        <div class="flex items-center gap-1.5 text-slate-500">
                            <i class="fas fa-tag text-[9px] opacity-50"></i>
                            <span class="font-medium capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                            <span class="text-slate-700">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Link Action (If applicable) -->
            @if($activity->subject_type === 'App\Models\Task' && $activity->subject_id)
                <!-- Assuming we can link to the task or dashboard -->
                {{-- Logic to link to tasks here if needed --}}
            @endif
        </div>
    </div>
</div>