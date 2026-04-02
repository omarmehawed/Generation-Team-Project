@extends('layouts.batu')

@section('title', 'Weekly Evaluation - Leader')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-[#0f172a] text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Weekly Evaluation - Leader View</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Full weekly evaluation overview for software and hardware teams.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('final_project.dashboard', $team->id) }}" class="rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 px-4 py-2 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <button class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-black shadow-lg shadow-indigo-200 dark:shadow-none transition-all">
                    Export Summary
                </button>
            </div>
        </div>

        {{-- Main Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-black uppercase tracking-widest">Total Members</p>
                <h3 class="text-3xl font-black mt-2 text-indigo-600 dark:text-indigo-400">{{ $stats['members_count'] ?? 0 }}</h3>
            </div>

            <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-black uppercase tracking-widest">Sub Leaders</p>
                <h3 class="text-3xl font-black mt-2 text-purple-600 dark:text-purple-400">{{ $stats['sub_leaders_count'] ?? 0 }}</h3>
            </div>

            <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-black uppercase tracking-widest">Pending Reviews</p>
                <h3 class="text-3xl font-black mt-2 text-amber-500 dark:text-amber-400">{{ $stats['pending_reviews'] ?? 0 }}</h3>
            </div>

            <div class="rounded-2xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-black uppercase tracking-widest">Completed Reviews</p>
                <h3 class="text-3xl font-black mt-2 text-emerald-500 dark:text-emerald-400">{{ $stats['completed_this_week'] ?? 0 }}</h3>
            </div>
        </div>

        {{-- Split Software / Hardware --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="rounded-3xl bg-white dark:bg-[#111827] border border-blue-200 dark:border-blue-900/30 p-6 shadow-sm border-l-8 border-l-blue-600">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-[10px] text-blue-600 dark:text-blue-400 font-black uppercase tracking-widest">Software Team</p>
                        <h3 class="text-2xl font-black mt-1 text-gray-800 dark:text-gray-100">{{ $softwareStats['members_count'] ?? 0 }} Members</h3>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-blue-50/50 dark:bg-blue-900/10 p-3 border border-blue-100 dark:border-blue-900/20">
                        <p class="text-[9px] text-blue-600 dark:text-blue-400 font-black uppercase tracking-widest">Sub Leaders</p>
                        <p class="font-black mt-1 text-gray-800 dark:text-gray-100">{{ $softwareStats['sub_leaders'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-2xl bg-amber-50/50 dark:bg-amber-900/10 p-3 border border-amber-100 dark:border-amber-900/20">
                        <p class="text-[9px] text-amber-600 dark:text-amber-400 font-black uppercase tracking-widest">Pending</p>
                        <p class="font-black mt-1 text-gray-800 dark:text-gray-100">{{ $softwareStats['pending_reviews'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50/50 dark:bg-emerald-900/10 p-3 border border-emerald-100 dark:border-emerald-900/20">
                        <p class="text-[9px] text-emerald-600 dark:text-emerald-400 font-black uppercase tracking-widest">Completed</p>
                        <p class="font-black mt-1 text-gray-800 dark:text-gray-100">{{ $softwareStats['completed_reviews'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white dark:bg-[#111827] border border-amber-200 dark:border-amber-900/30 p-6 shadow-sm border-l-8 border-l-amber-600">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-[10px] text-amber-600 dark:text-amber-400 font-black uppercase tracking-widest">Hardware Team</p>
                        <h3 class="text-2xl font-black mt-1 text-gray-800 dark:text-gray-100">{{ $hardwareStats['members_count'] ?? 0 }} Members</h3>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-blue-50/50 dark:bg-blue-900/10 p-3 border border-blue-100 dark:border-blue-900/20">
                        <p class="text-[9px] text-blue-600 dark:text-blue-400 font-black uppercase tracking-widest">Sub Leaders</p>
                        <p class="font-black mt-1 text-gray-800 dark:text-gray-100">{{ $hardwareStats['sub_leaders'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-2xl bg-amber-50/50 dark:bg-amber-900/10 p-3 border border-amber-100 dark:border-amber-900/20">
                        <p class="text-[9px] text-amber-600 dark:text-amber-400 font-black uppercase tracking-widest">Pending</p>
                        <p class="font-black mt-1 text-gray-800 dark:text-gray-100">{{ $hardwareStats['pending_reviews'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50/50 dark:bg-emerald-900/10 p-3 border border-emerald-100 dark:border-emerald-900/20">
                        <p class="text-[9px] text-emerald-600 dark:text-emerald-400 font-black uppercase tracking-widest">Completed</p>
                        <p class="font-black mt-1 text-gray-800 dark:text-gray-100">{{ $hardwareStats['completed_reviews'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            {{-- Software Vice Leaders --}}
            <div class="rounded-3xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white">Software Vice Leaders</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Review software vice leaders and their sub leaders.</p>
                </div>
                <div class="p-6 space-y-4">
                    @forelse(($softwareViceLeaders ?? []) as $leader)
                        <div class="rounded-2xl bg-gray-50/50 dark:bg-[#0b1220] border border-gray-100 dark:border-gray-800 p-4 flex items-center justify-between group hover:border-indigo-500 transition-colors">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($leader->user->name) }}&background=EEF2FF&color=4F46E5&bold=true"
                                     class="w-12 h-12 rounded-xl object-cover border border-indigo-100 dark:border-indigo-900">
                                <div>
                                    <p class="font-black text-gray-800 dark:text-gray-100">{{ $leader->user->name }}</p>
                                    <p class="text-[10px] text-indigo-500 font-black uppercase tracking-widest">{{ $leader->technical_role }} Vice Leader</p>
                                </div>
                            </div>
                            <button onclick="openEvaluationModal('{{ $leader->id }}', '{{ $leader->user->name }}', 'vice_leader')" 
                                class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 text-xs font-black shadow-md transition-all">
                                Evaluate
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 dark:text-gray-500 font-bold italic">
                            No software vice leaders assigned.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Hardware Vice Leaders --}}
            <div class="rounded-3xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white">Hardware Vice Leaders</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Review hardware vice leaders and their sub leaders.</p>
                </div>
                <div class="p-6 space-y-4">
                    @forelse(($hardwareViceLeaders ?? []) as $leader)
                        <div class="rounded-2xl bg-gray-50/50 dark:bg-[#0b1220] border border-gray-100 dark:border-gray-800 p-4 flex items-center justify-between group hover:border-amber-500 transition-colors">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($leader->user->name) }}&background=FFFBEB&color=B45309&bold=true"
                                     class="w-12 h-12 rounded-xl object-cover border border-amber-100 dark:border-amber-900">
                                <div>
                                    <p class="font-black text-gray-800 dark:text-gray-100">{{ $leader->user->name }}</p>
                                    <p class="text-[10px] text-amber-500 font-black uppercase tracking-widest">{{ $leader->technical_role }} Vice Leader</p>
                                </div>
                            </div>
                            <button onclick="openEvaluationModal('{{ $leader->id }}', '{{ $leader->user->name }}', 'vice_leader')" 
                                class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 text-xs font-black shadow-md transition-all">
                                Evaluate
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 dark:text-gray-500 font-bold italic">
                            No hardware vice leaders assigned.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@include('evaluation.partials.evaluation_modal')

@endsection
