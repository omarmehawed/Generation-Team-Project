@extends('layouts.batu')

@section('title', 'Weekly Evaluation - General Vice Leader')

@section('content')
<div class="min-h-screen bg-gray-50 text-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Weekly Evaluation - General Vice Leader</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Supervise both software and hardware evaluation flow and team management.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('final_project.dashboard', $team->id) }}" class="rounded-xl bg-white text-gray-700 border border-gray-200 px-4 py-2 text-sm font-medium hover:bg-gray-50 transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="rounded-3xl bg-white border border-blue-200 p-6 shadow-sm border-t-8 border-t-blue-600 transition-transform hover:-translate-y-1">
                <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest">Software Teams</p>
                <h3 class="text-3xl font-black mt-2 text-gray-800">{{ $stats['software_teams'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-widest">Active Units</p>
            </div>
            <div class="rounded-3xl bg-white border border-amber-200 p-6 shadow-sm border-t-8 border-t-amber-600 transition-transform hover:-translate-y-1">
                <p class="text-[10px] text-amber-600 font-black uppercase tracking-widest">Hardware Teams</p>
                <h3 class="text-3xl font-black mt-2 text-gray-800">{{ $stats['hardware_teams'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-widest">Active Units</p>
            </div>
            <div class="rounded-3xl bg-white border border-purple-200 p-6 shadow-sm border-t-8 border-t-purple-600 transition-transform hover:-translate-y-1">
                <p class="text-[10px] text-purple-600 font-black uppercase tracking-widest">Pending Reviews</p>
                <h3 class="text-3xl font-black mt-2 text-gray-800">{{ $stats['pending_reviews'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-widest">Across All Roles</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            {{-- Software Side --}}
            <div class="rounded-3xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-blue-50/50">
                    <h2 class="text-xl font-black text-blue-900">Software Sub Leaders</h2>
                    <p class="text-sm text-blue-700/60 font-bold">Oversight for software development teams.</p>
                </div>
                <div class="p-6 space-y-4">
                    @forelse(($softwareSubLeaders ?? []) as $subLeader)
                        <div class="rounded-2xl bg-gray-50/50 border border-gray-100 p-4 flex items-center justify-between group hover:border-blue-500 transition-all">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($subLeader->user->name) }}&background=EEF2FF&color=4F46E5&bold=true"
                                     class="w-12 h-12 rounded-xl object-cover shadow-sm bg-white">
                                <div>
                                    <p class="font-black text-gray-800">{{ $subLeader->user->name }}</p>
                                    <p class="text-[10px] text-indigo-500 font-black uppercase tracking-widest">Software Team #{{ $subLeader->team_number ?? '-' }}</p>
                                </div>
                            </div>
                            <button onclick="openEvaluationModal('{{ $subLeader->id }}', '{{ $subLeader->user->name }}', 'sub_leader')" 
                                class="rounded-xl border border-blue-200 px-5 py-2.5 text-xs font-black text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                Review
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 font-bold italic">
                            No software sub leaders listed.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Hardware Side --}}
            <div class="rounded-3xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-amber-50/50">
                    <h2 class="text-xl font-black text-amber-900">Hardware Sub Leaders</h2>
                    <p class="text-sm text-amber-700/60 font-bold">Oversight for hardware engineering teams.</p>
                </div>
                <div class="p-6 space-y-4">
                    @forelse(($hardwareSubLeaders ?? []) as $subLeader)
                        <div class="rounded-2xl bg-gray-50/50 border border-gray-100 p-4 flex items-center justify-between group hover:border-amber-500 transition-all">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($subLeader->user->name) }}&background=FFFBEB&color=B45309&bold=true"
                                     class="w-12 h-12 rounded-xl object-cover shadow-sm bg-white">
                                <div>
                                    <p class="font-black text-gray-800">{{ $subLeader->user->name }}</p>
                                    <p class="text-[10px] text-amber-500 font-black uppercase tracking-widest">Hardware Team #{{ $subLeader->team_number ?? '-' }}</p>
                                </div>
                            </div>
                            <button onclick="openEvaluationModal('{{ $subLeader->id }}', '{{ $subLeader->user->name }}', 'sub_leader')" 
                                class="rounded-xl border border-amber-200 px-5 py-2.5 text-xs font-black text-amber-600 hover:bg-amber-600 hover:text-white transition-all shadow-sm">
                                Review
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 font-bold italic">
                            No hardware sub leaders listed.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@include('evaluation.partials.evaluation_modal')

@endsection
