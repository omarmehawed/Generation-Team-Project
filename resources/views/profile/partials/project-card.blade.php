@props(['data', 'type', 'color'])

@php
    $project = $data['project'];
    $team = $data['team'];
    $assets = $data['assets'];

    $icon = $type === 'graduation' ? 'fas fa-graduation-cap' : 'fas fa-project-diagram';
    $bgColor = $type === 'graduation' ? 'bg-purple-500/20' : 'bg-blue-500/20';
    $textColor = $type === 'graduation' ? 'text-purple-400' : 'text-blue-400';
    $borderColor = $type === 'graduation' ? 'border-purple-500/30' : 'border-blue-500/30';
    $gradient = $type === 'graduation' ? 'from-purple-900/50' : 'from-blue-900/50';
@endphp

<div x-data="{ open: false }" class="mb-6">
    <!-- Card Header -->
    <div @click="open = !open"
        class="cursor-pointer bg-gradient-to-r {{ $gradient }} to-gray-900 border {{ $borderColor }} rounded-2xl p-6 flex justify-between items-center hover:border-opacity-60 transition-colors shadow-lg relative overflow-hidden group">

        <div class="flex items-center gap-4 relative z-10">
            <div
                class="w-12 h-12 rounded-full {{ $bgColor }} flex items-center justify-center {{ $textColor }} text-2xl">
                <i class="{{ $icon }}"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white font-tech">{{ $project->title ?? $project->name }}</h2>
                <p class="{{ $textColor }} text-sm opacity-80 mt-1">
                    {{ $type === 'subject' ? 'Course: ' . ($project->course->name ?? 'N/A') . ' | ' : '' }}
                    Team: {{ $team->name }}
                    @php $myRoleInTeam = $team->members->where('user_id', $user->id)->first()?->role; @endphp
                    | Role: <span class="font-bold">
                        @if($myRoleInTeam === 'leader') Leader (Group A)
                        @elseif($myRoleInTeam === 'leader_b') Leader (Group B)
                        @elseif($myRoleInTeam === 'vice_leader') Vice Leader
                        @else Member @endif
                    </span>
                </p>
            </div>
        </div>

        <i class="fas fa-chevron-down {{ $textColor }} transition-transform duration-300"
            :class="{'rotate-180': open}"></i>

        <!-- Glow Effect -->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
            style="background: radial-gradient(circle at center, rgba(255,255,255,0.05) 0%, transparent 70%);"></div>
    </div>

    <!-- Expanded Assets Grid -->
    <div x-show="open" x-collapse
        class="mt-4 bg-gray-900/50 backdrop-blur-md rounded-2xl border border-gray-800 overflow-hidden">

        @include('profile.partials.assets-grid', ['assets' => $assets, 'color' => $color])

    </div>
</div>