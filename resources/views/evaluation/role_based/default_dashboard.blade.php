@extends('layouts.batu')

@section('title', 'Weekly Evaluation Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 text-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">Weekly Evaluation</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Manage evaluations for your team members and track performance.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('final_project.dashboard', $team->id) }}" class="rounded-xl bg-white text-gray-700 border border-gray-200 px-4 py-2 text-sm font-medium hover:bg-gray-50 transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-arrow-left"></i> Team Dashboard
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Team Members</p>
                <h3 class="text-2xl font-black mt-2 text-indigo-600">{{ $stats['members_count'] ?? 0 }}</h3>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Sub Leaders</p>
                <h3 class="text-2xl font-black mt-2 text-purple-600">{{ $stats['sub_leaders_count'] ?? 0 }}</h3>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Pending Reviews</p>
                <h3 class="text-2xl font-black mt-2 text-amber-500">{{ $stats['pending_reviews'] ?? 0 }}</h3>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Completed</p>
                <h3 class="text-2xl font-black mt-2 text-emerald-500">{{ $stats['completed_this_week'] ?? 0 }}</h3>
            </div>
        </div>

        {{-- Main Content (Tabs structure from previous index) --}}
        <div x-data="{ tab: 'members' }" class="space-y-6">
            <div class="flex flex-wrap gap-2 rounded-2xl bg-white border border-gray-200 p-2 shadow-sm sticky top-0 z-20">
                <button @click="tab='members'" :class="tab==='members' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200/50' : 'text-gray-600 hover:bg-gray-50'" class="px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fas fa-users"></i> Members
                </button>
                <button @click="tab='workshops'" :class="tab==='workshops' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200/50' : 'text-gray-700 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fas fa-hammer"></i> Workshops
                </button>
                <button @click="tab='meetings'" :class="tab==='meetings' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200/50' : 'text-gray-700 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fas fa-handshake"></i> Meetings
                </button>
            </div>

            {{-- Tab Contents (Simplified for brevity) --}}
            <div x-show="tab==='members'" x-transition>
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr class="text-left border-b border-gray-200">
                                    <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Member</th>
                                    <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-widest text-[10px]">Role</th>
                                    <th class="px-6 py-4 font-bold text-right text-gray-400 uppercase tracking-widest text-[10px]">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($members as $m)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($m->user->name) }}&background=F3F4F6&color=6B7280&bold=true" class="w-10 h-10 rounded-xl object-cover">
                                                <div>
                                                    <p class="font-bold text-gray-800">{{ $m->user->name }}</p>
                                                    <p class="text-[10px] text-gray-400">{{ $m->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-600">
                                                {{ $m->technical_role ?? 'Member' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button onclick="openEvaluationModal('{{ $m->id }}', '{{ $m->user->name }}', 'member')" class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-black hover:bg-gray-50 transition-all text-gray-700">
                                                Evaluate
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-6 py-12 text-center text-gray-400 font-bold">No members to evaluate.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Workshops Tab --}}
            <div x-show="tab==='workshops'" x-transition style="display:none;">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($workshops as $w)
                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-black uppercase tracking-widest">
                                    {{ $w->technical_role ?? 'General' }}
                                </span>
                                <p class="text-[10px] font-bold text-gray-400">{{ \Carbon\Carbon::parse($w->workshop_date)->format('M d, Y') }}</p>
                            </div>
                            <h3 class="text-lg font-black text-gray-900 mb-4">{{ $w->name }}</h3>
                            <a href="{{ route('final_project.dashboard', $team->id) }}#workshops" class="block w-full rounded-xl border border-gray-100 bg-gray-50 py-3 text-[10px] font-black text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors text-center uppercase tracking-widest">
                                View Attendance
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 text-gray-400 font-bold">No workshops recorded.</div>
                    @endforelse
                </div>
            </div>

            {{-- Meetings Tab --}}
            <div x-show="tab==='meetings'" x-transition style="display:none;">
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-left border-b border-gray-200">
                                <th class="px-6 py-4 font-bold text-gray-400 text-[10px] uppercase tracking-widest">Title</th>
                                <th class="px-6 py-4 font-bold text-gray-400 text-[10px] uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 font-bold text-right text-gray-400 text-[10px] uppercase tracking-widest">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($meetings as $mtg)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-gray-800">{{ $mtg->title }}</p>
                                        <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($mtg->meeting_date)->format('M d, Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600">Completed</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('final_project.dashboard', $team->id) }}#meetings" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-[10px] font-black text-gray-700 shadow-sm inline-block">VIEW</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-12 text-center text-gray-400 font-bold">No meetings recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('evaluation.partials.evaluation_modal')

@endsection
