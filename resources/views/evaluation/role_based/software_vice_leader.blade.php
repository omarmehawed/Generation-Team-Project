@extends('layouts.batu')

@section('title', 'Weekly Evaluation - Software Vice Leader')

@section('content')
<div class="min-h-screen bg-gray-50 text-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Weekly Evaluation - Software Vice Leader</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Manage software sub leaders, members, workshops, and weekly reviews.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('final_project.dashboard', $team->id) }}" class="rounded-xl bg-white text-gray-700 border border-gray-200 px-4 py-2 text-sm font-medium hover:bg-gray-50 transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Software Members</p>
                <h3 class="text-3xl font-black mt-2 text-indigo-600">{{ $stats['members_count'] ?? 0 }}</h3>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Sub Leaders</p>
                <h3 class="text-3xl font-black mt-2 text-purple-600">{{ $stats['sub_leaders_count'] ?? 0 }}</h3>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Pending Reviews</p>
                <h3 class="text-3xl font-black mt-2 text-amber-500">{{ $stats['pending_reviews'] ?? 0 }}</h3>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-black text-gray-900">Software Sub Leaders</h2>
                <p class="text-sm text-gray-500">Monitor software teams and their evaluations.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left border-b border-gray-200">
                            <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Sub Leader</th>
                            <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Team No.</th>
                            <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Domain</th>
                            <th class="px-6 py-4 font-bold text-right text-gray-400 uppercase tracking-widest text-[10px]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse(($subLeaders ?? []) as $subLeader)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($subLeader->user->name) }}&background=F3E8FF&color=9333EA&bold=true"
                                             class="w-10 h-10 rounded-xl object-cover shadow-sm border border-gray-100">
                                        <div>
                                            <p class="font-bold text-gray-800">{{ $subLeader->user->name }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium">{{ $subLeader->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-indigo-600">Team #{{ $subLeader->team_number ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-600">
                                        {{ $subLeader->technical_role ?? 'Software' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="openEvaluationModal('{{ $subLeader->id }}', '{{ $subLeader->user->name }}', 'sub_leader')" 
                                        class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-black hover:bg-gray-50 transition-all text-gray-700">
                                        Review
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold">
                                    No software sub leaders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('evaluation.partials.evaluation_modal')

@endsection
