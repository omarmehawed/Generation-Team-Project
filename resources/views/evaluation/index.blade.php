@extends('layouts.batu')

@section('title', 'Weekly Evaluation')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-[#0f172a] text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        @php
            $roleLabel = match($viewRole ?? 'leader') {
                'leader' => 'Leader View',
                'general_vice_leader' => 'General Vice Leader View',
                'software_vice_leader' => 'Software Vice Leader View',
                'hardware_vice_leader' => 'Hardware Vice Leader View',
                'sub_leader' => 'Sub Leader View',
                default => 'Weekly Evaluation',
            };

            $isLeader = ($viewRole ?? '') === 'leader';
            $isGeneralVice = ($viewRole ?? '') === 'general_vice_leader';
            $isSoftwareVice = ($viewRole ?? '') === 'software_vice_leader';
            $isHardwareVice = ($viewRole ?? '') === 'hardware_vice_leader';
            $isSubLeader = ($viewRole ?? '') === 'sub_leader';

            $showBothDomains = $isLeader || $isGeneralVice;
            $showSoftwareOnly = $isSoftwareVice;
            $showHardwareOnly = $isHardwareVice;
        @endphp

        {{-- Header --}}
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">Weekly Evaluation</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $roleLabel }} — structured evaluation for members, sub leaders, and vice leaders.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Select Week</label>
                    <select onchange="window.location.href='?period_id='+this.value" 
                        class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#111827] px-4 py-2.5 text-sm font-black focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm transition-all">
                        @foreach($allPeriods as $period)
                            <option value="{{ $period->id }}" {{ $currentPeriod && $currentPeriod->id == $period->id ? 'selected' : '' }}>
                                Week #{{ $period->week_number }} ({{ $period->start_date->format('M d') }} - {{ $period->end_date ? $period->end_date->format('M d') : 'Active' }})
                            </option>
                        @endforeach
                        @if($allPeriods->isEmpty())
                            <option disabled selected>No weeks defined</option>
                        @endif
                    </select>
                </div>

                @if($isLeader || $isGeneralVice)
                <button type="button" onclick="openDefinePeriodModal()" 
                    class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 text-sm font-black shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-2">
                    <i class="fas fa-calendar-plus"></i> Define New Week
                </button>
                @endif

                @if($isLeader || $isGeneralVice || $isSoftwareVice || $isHardwareVice)
                <a href="{{ route('evaluation.export', ['team' => $team->id, 'period_id' => request('period_id')]) }}" class="rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-4 py-2 text-sm font-medium shadow hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-file-csv mr-1"></i> Export Summary
                </a>
                @if($currentPeriod)
                <form method="POST" action="{{ route('evaluation.completeWeek', $team->id) }}" onsubmit="return confirm('Finalize Week #{{ $currentPeriod->week_number }}? This cannot be undone.')">
                    @csrf
                    <button type="submit" class="rounded-xl bg-green-600 hover:bg-green-700 text-white px-4 py-2 text-sm font-medium shadow transition">
                        <i class="fas fa-check-double mr-1"></i> Complete Week
                    </button>
                </form>
                @endif
                @endif
            </div>
        </div>

        {{-- Main Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
            <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Members</p>
                <h3 class="text-2xl font-bold mt-2">{{ $stats['members_count'] ?? 0 }}</h3>
            </div>

            <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400">Sub Leaders</p>
                <h3 class="text-2xl font-bold mt-2">{{ $stats['sub_leaders_count'] ?? 0 }}</h3>
            </div>

            <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pending Reviews</p>
                <h3 class="text-2xl font-bold mt-2">{{ $stats['pending_reviews'] ?? 0 }}</h3>
            </div>

            <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400">Completed This Week</p>
                <h3 class="text-2xl font-bold mt-2">{{ $stats['completed_this_week'] ?? 0 }}</h3>
            </div>
        </div>

        {{-- Leader / General split Software + Hardware --}}
        @if($showBothDomains)
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
                <div class="rounded-2xl bg-white dark:bg-[#111827] border border-blue-200 dark:border-blue-900 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Software Team</p>
                            <h3 class="text-xl font-bold mt-1">{{ $softwareStats['members_count'] ?? 0 }} Members</h3>
                        </div>
                        <span class="rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 text-xs font-semibold">
                            Software
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mt-4">
                        <div class="rounded-xl bg-gray-50 dark:bg-[#0b1220] p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Sub Leaders</p>
                            <p class="font-semibold mt-1">{{ $softwareStats['sub_leaders_count'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-[#0b1220] p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                            <p class="font-semibold mt-1">{{ $softwareStats['pending_reviews'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-[#0b1220] p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Completed</p>
                            <p class="font-semibold mt-1">{{ $softwareStats['completed_this_week'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white dark:bg-[#111827] border border-amber-200 dark:border-amber-900 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-amber-600 dark:text-amber-400 font-medium">Hardware Team</p>
                            <h3 class="text-xl font-bold mt-1">{{ $hardwareStats['members_count'] ?? 0 }} Members</h3>
                        </div>
                        <span class="rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-3 py-1 text-xs font-semibold">
                            Hardware
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mt-4">
                        <div class="rounded-xl bg-gray-50 dark:bg-[#0b1220] p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Sub Leaders</p>
                            <p class="font-semibold mt-1">{{ $hardwareStats['sub_leaders_count'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-[#0b1220] p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                            <p class="font-semibold mt-1">{{ $hardwareStats['pending_reviews'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-[#0b1220] p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Completed</p>
                            <p class="font-semibold mt-1">{{ $hardwareStats['completed_this_week'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Single domain summary for domain vice leaders --}}
        @if($showSoftwareOnly)
            <div class="rounded-2xl bg-white dark:bg-[#111827] border border-blue-200 dark:border-blue-900 p-5 shadow-sm mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Software Team</p>
                        <h3 class="text-xl font-bold mt-1">{{ $softwareStats['members_count'] ?? 0 }} Members</h3>
                    </div>
                    <span class="rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 text-xs font-semibold">
                        Software Vice Leader Scope
                    </span>
                </div>
            </div>
        @endif

        @if($showHardwareOnly)
            <div class="rounded-2xl bg-white dark:bg-[#111827] border border-amber-200 dark:border-amber-900 p-5 shadow-sm mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 dark:text-amber-400 font-medium">Hardware Team</p>
                        <h3 class="text-xl font-bold mt-1">{{ $hardwareStats['members_count'] ?? 0 }} Members</h3>
                    </div>
                    <span class="rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-3 py-1 text-xs font-semibold">
                        Hardware Vice Leader Scope
                    </span>
                </div>
            </div>
        @endif

        {{-- Tabs --}}
        <div x-data="{ tab: 'members' }" class="space-y-6">
            <div class="flex flex-wrap gap-2 rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 p-2 shadow-sm">
                <button @click="tab='members'"
                        :class="tab==='members' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition">
                    Members Evaluation
                </button>

                @if(!$isSubLeader)
                    <button @click="tab='subleaders'"
                            :class="tab==='subleaders' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition">
                        Sub Leaders
                    </button>
                @endif

                @if($isLeader || $isGeneralVice)
                    <button @click="tab='viceleaders'"
                            :class="tab==='viceleaders' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition">
                        Vice Leaders
                    </button>
                @endif

                <button @click="tab='workshops'"
                        :class="tab==='workshops' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition">
                    Workshops
                </button>

                <button @click="tab='tasks'"
                        :class="tab==='tasks' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition">
                    Tasks
                </button>

                <button @click="tab='meetings'"
                        :class="tab==='meetings' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition">
                    Meetings & Attendance
                </button>
            </div>

            {{-- Members --}}
            <div x-show="tab==='members'" x-transition>
                <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                        <h2 class="text-lg font-semibold">Members Evaluation</h2>
                        <form action="{{ request()->url() }}" method="GET" class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                            @if(request('period_id'))
                                <input type="hidden" name="period_id" value="{{ request('period_id') }}">
                            @endif
                            <input type="hidden" name="tab" value="members">

                            {{-- Team Filter (Leader & Vice Leaders only) --}}
                            @if($isLeader || str_contains($viewRole, 'vice_leader'))
                                <select name="team_filter" onchange="this.form.submit()" 
                                    class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#0b1220] px-3 py-2 text-xs font-bold text-indigo-600 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                                    <option value="">All Teams (Filter)</option>
                                    @foreach($uniqueTeams as $ut)
                                        @php
                                            $val = $ut['number'] . '-' . $ut['role'];
                                            $showOption = true;
                                            if ($isSoftwareVice && $ut['role'] !== 'software') $showOption = false;
                                            if ($isHardwareVice && $ut['role'] !== 'hardware') $showOption = false;
                                        @endphp
                                        @if($showOption)
                                            <option value="{{ $val }}" {{ ($currentTeamFilter ?? '') == $val ? 'selected' : '' }}>
                                                {{ $ut['label'] }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            @endif

                            <div class="relative w-full lg:w-64">
                                <input type="text" name="search" value="{{ $currentSearch ?? '' }}" placeholder="Name, Email, or Academic #" 
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#0b1220] pl-4 pr-10 py-2 text-xs font-medium focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-500 transition-colors">
                                    <i class="fas fa-search text-xs"></i>
                                </button>
                            </div>

                            @if($isSubLeader || $viewRole === 'leader' || str_contains($viewRole, 'vice_leader'))
                                <button type="button" onclick="openAssignMemberModal()" class="shrink-0 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-xs font-bold shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                                    <i class="fas fa-plus mr-1"></i> Add Member
                                </button>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-[#0b1220]">
                                <tr class="text-left">
                                    <th class="px-4 py-3 font-semibold">Member</th>
                                    <th class="px-4 py-3 font-semibold text-center">Team</th>
                                    <th class="px-4 py-3 font-semibold text-center">Score</th>
                                    <th class="px-4 py-3 font-semibold text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($members ?? collect()) as $member)
                                    <tr class="border-t border-gray-200 dark:border-gray-800 group hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                @php
                                                    $avatarUrl = ($member->user?->profile_photo_url ?? $member->avatar) ?? 'https://ui-avatars.com/api/?name=' . urlencode($member?->user?->name ?? 'User') . '&background=6366f1&color=fff&bold=true';
                                                @endphp
                                                <img src="{{ $avatarUrl }}"
                                                     class="w-10 h-10 rounded-xl object-cover border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800">
                                                <div>
                                                    @if($isLeader || $isGeneralVice || $isSoftwareVice || $isHardwareVice)
                                                        <a href="{{ route('profile.show', $member->user_id) }}" class="font-bold text-gray-900 dark:text-white hover:text-indigo-500 transition-colors">{{ $member?->user?->name ?? 'Unknown' }}</a>
                                                    @else
                                                        <p class="font-bold text-gray-900 dark:text-white">{{ $member?->user?->name ?? 'Unknown' }}</p>
                                                    @endif
                                                    <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-wider">{{ $member?->technical_role ?? 'Member' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-600 dark:text-gray-400">
                                            {{ $member?->team_number ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @php $rec = $existingRecords[$member->id] ?? null; @endphp
                                            <span class="inline-flex items-center rounded-xl px-3 py-1.5 text-xs font-black {{ $rec ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-100' : 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border border-indigo-100' }} dark:border-indigo-900/30">
                                                {{ $rec ? number_format($rec->total_overall_score, 1) : '0' }} / 30
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @php $rec = $existingRecords[$member->id] ?? null; @endphp
                                            <div class="flex items-center justify-end gap-2">
                                                @if($rec)
                                                <button onclick="openEvaluationModal('{{ $member?->id }}', '{{ addslashes($member?->user?->name ?? 'Unknown') }}', 'member', '{{ $currentPeriod->id ?? '' }}', {{ json_encode(['task_score' => $rec->total_task_score, 'workshop_score' => $rec->total_workshop_score, 'meeting_score' => $rec->total_meeting_score, 'notes' => $rec->general_notes]) }})"
                                                    class="rounded-xl bg-amber-500 hover:bg-amber-600 border border-amber-400 px-4 py-2 text-xs font-black text-white transition-all shadow-sm">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </button>
                                                @else
                                                <button onclick="openEvaluationModal('{{ $member?->id }}', '{{ addslashes($member?->user?->name ?? 'Unknown') }}', 'member', '{{ $currentPeriod->id ?? '' }}')"
                                                    class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-2 text-xs font-black text-gray-700 dark:text-gray-200 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                                    Evaluate
                                                </button>
                                                @endif

                                                @if($isLeader || $isGeneralVice || $isSoftwareVice || $isHardwareVice)
                                                <button onclick="openEditTeamModal({{ $member->id }}, {{ $member->team_number ?? 'null' }}, '{{ addslashes($member->user->name ?? 'Unknown') }}')"
                                                    class="rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-2 text-xs font-black text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all shadow-sm"
                                                    title="Edit Team Number">
                                                    <i class="fas fa-users-cog"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400 font-bold italic">
                                            No members found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination Footer --}}
                        @if($members->total() > 0)
                        <div class="mt-4 px-6 pb-6 pt-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/30 dark:bg-[#0b1220]/20">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                    Showing <span class="text-indigo-500 dark:text-indigo-400">{{ $members->firstItem() }}</span> to <span class="text-indigo-500 dark:text-indigo-400">{{ $members->lastItem() }}</span> of <span class="text-indigo-500 dark:text-indigo-400">{{ $members->total() }}</span> members
                                </p>
                                <div class="flex items-center gap-2">
                                    {{ $members->appends(['tab' => 'members'])->links('pagination::simple-tailwind') }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sub Leaders --}}
            @if(!$isSubLeader)
            <div x-show="tab==='subleaders'" x-transition style="display:none;">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        @forelse(($subLeaders ?? collect()) as $sl)
                        <div class="relative rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#111827] p-6 group hover:border-indigo-500 transition-all shadow-sm">
                            {{-- Admin Actions (Top Right) --}}
                            @if($isLeader || $isGeneralVice || $isSoftwareVice || $isHardwareVice)
                            <div class="absolute top-4 right-4 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                {{-- Edit Team --}}
                                <button onclick="openEditTeamModal({{ $sl->id }}, {{ $sl->team_number ?? 'null' }}, '{{ addslashes($sl->user->name ?? 'Unknown') }}')"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-indigo-500 hover:text-white transition-all shadow-sm" title="Edit Team Number">
                                    <i class="fas fa-users-cog text-xs"></i>
                                </button>

                                {{-- Remove Role --}}
                                <form action="{{ route('evaluation.remove-sub-leader', $team->id) }}" method="POST" onsubmit="return confirm('Remove as Sub Leader? They will become a normal member.')">
                                    @csrf
                                    <input type="hidden" name="member_id" value="{{ $sl->id }}">
                                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Remove Sub Leader Role">
                                        <i class="fas fa-user-minus text-xs"></i>
                                    </button>
                                </form>
                            </div>
                            @endif

                            <div class="flex items-center gap-4 mb-4">
                                @php
                                    $slAvatarUrl = ($sl->user?->profile_photo_url ?? $sl->avatar) ?? 'https://ui-avatars.com/api/?name=' . urlencode($sl?->user?->name ?? 'SL') . '&background=8b5cf6&color=fff&bold=true';
                                @endphp
                                <img src="{{ $slAvatarUrl }}"
                                     class="w-12 h-12 rounded-2xl object-cover bg-white dark:bg-gray-900 p-1 border border-indigo-100 dark:border-indigo-900/50 shadow-sm">
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-white">{{ $sl?->user?->name ?? 'Unknown' }}</h4>
                                    <p class="text-[10px] text-purple-500 font-black uppercase tracking-widest">{{ $sl?->technical_role ?? 'Unknown' }} Team #{{ $sl?->team_number ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-6">
                                <div class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Performance</div>
                                <div class="font-black text-purple-600 dark:text-purple-400">{{ $sl?->weekly_score ?? 'Pending' }}</div>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-800 h-1.5 rounded-full mt-2 overflow-hidden">
                                <div class="bg-purple-500 h-full rounded-full transition-all duration-1000" style="width: {{ $sl?->weekly_score ?? 0 }}%"></div>
                            </div>
                            <button onclick="openEvaluationModal('{{ $sl?->id }}', '{{ addslashes($sl?->user?->name ?? 'Unknown') }}', 'sub_leader', '{{ $currentPeriod->id ?? '' }}')"
                                class="w-full mt-6 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black py-3 text-xs shadow-lg shadow-purple-500/20 transition-all opacity-0 group-hover:opacity-100">
                                Submit Evaluation
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-400 dark:text-gray-500 font-bold italic">No sub-leaders assigned.</div>
                    @endforelse

                    {{-- Assign Button Card --}}
                    @if($isLeader || $isGeneralVice || $isSoftwareVice || $isHardwareVice)
                    <div class="rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 flex flex-col items-center justify-center p-8 group hover:border-indigo-500 transition-all cursor-pointer bg-gray-50/30 dark:bg-gray-800/10"
                         onclick="openAssignSubLeaderModal()">
                        <div class="w-14 h-14 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-plus text-xl"></i>
                        </div>
                        <span class="font-black text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest">Assign New Sub Leader</span>
                    </div>
                    @endif
                </div>

                {{-- SubLeader Pagination Footer --}}
                @if($subLeaders->total() > 0)
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 px-4">
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                        Showing <span class="text-purple-500">{{ $subLeaders->firstItem() }}</span> to <span class="text-purple-500">{{ $subLeaders->lastItem() }}</span> of <span class="text-purple-500">{{ $subLeaders->total() }}</span> sub-leaders
                    </p>
                    <div class="flex items-center gap-2">
                        {{ $subLeaders->appends(['tab' => 'subleaders'])->links('pagination::simple-tailwind') }}
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- Vice Leaders --}}
            @if($isLeader || $isGeneralVice)
            <div x-show="tab==='viceleaders'" x-transition style="display:none;">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse(($viceLeaders ?? collect()) as $vl)
                        <div class="rounded-3xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 p-6 shadow-sm group hover:-translate-y-1 transition-all">
                            <div class="flex items-center gap-4 mb-6">
                                @php
                                    $vlAvatarUrl = ($vl->user?->profile_photo_url ?? $vl->avatar) ?? 'https://ui-avatars.com/api/?name=' . urlencode($vl?->user?->name ?? 'VL') . '&background=6366f1&color=fff&bold=true';
                                @endphp
                                <img src="{{ $vlAvatarUrl }}"
                                     class="w-14 h-14 rounded-2xl object-cover bg-white dark:bg-gray-900 p-1 border border-indigo-100 dark:border-indigo-900/30 shadow-md">
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-white leading-tight">{{ $vl?->user?->name ?? 'Unknown' }}</h4>
                                    <p class="text-[10px] text-indigo-500 font-black uppercase tracking-widest mt-1">{{ strtoupper($vl?->technical_role ?? 'Vice') }} Vice Leader</p>
                                </div>
                            </div>
                            <button onclick="openEvaluationModal('{{ $vl?->id }}', '{{ addslashes($vl?->user?->name ?? 'Unknown') }}', 'vice_leader', '{{ $currentPeriod->id ?? '' }}')"
                                class="w-full rounded-2xl bg-gray-50 dark:bg-[#0b1220] border border-gray-100 dark:border-gray-800 text-gray-700 dark:text-gray-300 font-black py-3 text-xs hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                Evaluate Vice Leader
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-400 dark:text-gray-500 font-bold italic">No vice leaders found.</div>
                    @endforelse
                </div>

                {{-- ViceLeader Pagination Footer --}}
                @if($viceLeaders->total() > 0)
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 px-4">
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                        Showing <span class="text-indigo-500">{{ $viceLeaders->firstItem() }}</span> to <span class="text-indigo-500">{{ $viceLeaders->lastItem() }}</span> of <span class="text-indigo-500">{{ $viceLeaders->total() }}</span> vice-leaders
                    </p>
                    <div class="flex items-center gap-2">
                        {{ $viceLeaders->appends(['tab' => 'viceleaders'])->links('pagination::simple-tailwind') }}
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- Tasks --}}
            <div x-show="tab==='tasks'" x-transition style="display:none;">
                <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
                        <h2 class="text-xl font-black">Tasks Overview</h2>
                        @if(in_array($viewRole, ['leader', 'software_vice_leader', 'hardware_vice_leader', 'general_vice_leader']))
                        <button onclick="openEvalAssignTaskModal()"
                            class="flex items-center gap-2 bg-gray-900 dark:bg-white hover:bg-gray-700 dark:hover:bg-gray-100 text-white dark:text-gray-900 font-black text-xs uppercase tracking-widest px-4 py-2.5 rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                            <i class="fas fa-plus text-xs"></i> Assign Task
                        </button>
                        @endif
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-[#0b1220]">
                            <tr class="text-left font-black text-[10px] text-gray-400 uppercase tracking-widest">
                                <th class="px-6 py-4">Task Name</th>
                                <th class="px-6 py-4">Deadline</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse(($tasks ?? collect()) as $t)
                                <tr class="hover:bg-gray-50 dark:hover:bg-[#0b1220]/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-black text-gray-900 dark:text-white leading-none">{{ $t->title }}</p>
                                        <p class="text-[10px] text-indigo-500 mt-2 font-bold uppercase tracking-wider">{{ strtoupper($t->technical_role) }}</p>
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach($t->members as $memberName)
                                                <span class="text-[9px] bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded-md font-bold text-gray-500 dark:text-gray-400">{{ $memberName }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-600 dark:text-gray-400">
                                        {{ $t->deadline ? (is_string($t->deadline) ? date('M d, Y', strtotime($t->deadline)) : $t->deadline->format('M d, Y')) : 'No Deadline' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColor = match($t->status) {
                                                'approved' => 'bg-emerald-500',
                                                'rejected' => 'bg-red-500',
                                                default => 'bg-amber-500'
                                            };
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full {{ $statusColor }}"></div>
                                            <span class="text-[10px] font-black uppercase tracking-widest">{{ $t->status }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('final_project.start') }}#tasks-section" class="inline-block px-5 py-2.5 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-widest shadow-lg active:scale-95 transition-all">
                                            VIEW TASK
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 font-bold italic">No tasks found for this week.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Tasks Pagination Footer --}}
                    @if($tasks->total() > 0)
                    <div class="mt-4 px-6 pb-6 pt-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/20">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                Showing <span class="text-indigo-500">{{ $tasks->firstItem() }}</span> to <span class="text-indigo-500">{{ $tasks->lastItem() }}</span> of <span class="text-indigo-500">{{ $tasks->total() }}</span> tasks
                            </p>
                            <div class="flex items-center gap-2">
                                {{ $tasks->appends(['tab' => 'tasks'])->links('pagination::simple-tailwind') }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Workshops --}}
            <div x-show="tab==='workshops'" x-transition style="display:none;">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-black text-gray-800 dark:text-white">Workshops</h2>
                    @if(!$isSubLeader)
                    <button onclick="openWorkshopModal()" class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-medium shadow transition">
                        <i class="fas fa-plus mr-1.5"></i> Create Workshop
                    </button>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse(($workshops ?? collect()) as $w)
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#111827] p-6 group hover:border-amber-500 transition-all shadow-sm">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-white leading-tight text-lg">{{ $w?->title ?? 'Untitled Workshop' }}</h4>
                                    <p class="text-[10px] text-gray-400 font-bold mt-2">
                                        <i class="far fa-calendar mr-1"></i> {{ $w?->workshop_date ? (is_string($w?->workshop_date) ? date('M d, Y', strtotime($w?->workshop_date)) : $w?->workshop_date->format('M d, Y')) : 'N/A' }}
                                    </p>
                                </div>
                                <span class="rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 px-2 py-1 text-[8px] font-black uppercase border border-amber-100 dark:border-amber-900/30">
                                    {{ strtoupper($w?->technical_role ?? $w?->domain ?? 'General') }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-6">
                                <button onclick="openWorkshopAttendanceModal({{ $w?->id }}, '{{ addslashes($w?->title ?? 'Untitled') }}')" 
                                    class="px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-[10px] font-black transition-all hover:shadow-lg active:scale-95">
                                    ATTENDANCE
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-400 dark:text-gray-500 font-bold italic">No workshops available.</div>
                    @endforelse
                </div>

                {{-- Workshops Pagination Footer --}}
                @if($workshops->total() > 0)
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 px-4">
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                        Showing <span class="text-amber-500">{{ $workshops->firstItem() }}</span> to <span class="text-amber-500">{{ $workshops->lastItem() }}</span> of <span class="text-amber-500">{{ $workshops->total() }}</span> workshops
                    </p>
                    <div class="flex items-center gap-2">
                        {{ $workshops->appends(['tab' => 'workshops'])->links('pagination::simple-tailwind') }}
                    </div>
                </div>
                @endif
            </div>

            {{-- Meetings --}}
            <div x-show="tab==='meetings'" x-transition style="display:none;">
                <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
                        <h2 class="text-xl font-black">Meetings & Attendance</h2>
                        
                        @if($viewRole !== 'member')
                        <button onclick="openMeetingModal()" class="rounded-xl bg-green-600 hover:bg-green-700 text-white px-4 py-2 text-sm font-medium shadow transition hover:scale-105">
                            Create Meeting
                        </button>
                        @endif
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-[#0b1220]">
                            <tr class="text-left font-black text-[10px] text-gray-400 uppercase tracking-widest">
                                <th class="px-6 py-4">Title</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse(($meetings ?? collect()) as $m)
                                <tr class="hover:bg-gray-50 dark:hover:bg-[#0b1220]/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-black text-gray-900 dark:text-white leading-none">{{ $m?->topic ?? 'Untitled Meeting' }}</p>
                                        <p class="text-[10px] text-gray-400 mt-2 font-bold">{{ $m?->meeting_date ? (is_string($m?->meeting_date) ? date('M d, Y', strtotime($m?->meeting_date)) : $m?->meeting_date->format('M d, Y')) : 'N/A' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full {{ ($m?->status ?? '') === 'completed' ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse' }}"></div>
                                            <span class="text-[10px] font-black uppercase text-gray-500 dark:text-gray-400">{{ $m?->status ?? 'Pending' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @php
                                            $meetingRoute = Route::has('meetings.show') ? route('meetings.show', $m->id) : '#';
                                            $canView = Route::has('meetings.show');
                                        @endphp
                                        <a href="{{ $meetingRoute }}" 
                                           class="px-5 py-2.5 rounded-xl {{ $canView ? 'bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/20' : 'bg-gray-400 cursor-not-allowed' }} text-white text-[10px] font-black uppercase tracking-widest transition-all inline-block">
                                            VIEW LOG
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 font-bold italic">No meetings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@include('evaluation.partials.evaluation_modal')
@include('evaluation.partials.define_period_modal')

@include("evaluation.partials.assign_sub_leader_modal")
@include("evaluation.partials.assign_member_modal")
@include("evaluation.partials.meeting_modal")
@include("evaluation.partials.workshop_modal")
@include("evaluation.partials.attendance_modal")
@include("evaluation.partials.assign_task_modal")
@include("evaluation.partials.edit_team_modal")
@endsection