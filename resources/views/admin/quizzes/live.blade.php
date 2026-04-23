@extends('layouts.batu')

@section('content')
<div class="max-w-7xl mx-auto py-8 text-gray-800">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="text-gray-500 hover:text-gray-900 font-bold text-sm mb-2 inline-flex items-center gap-1 transition">
                <i class="fas fa-arrow-left"></i> Back to Quizzes
            </a>
            <h1 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                <span class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center shadow-sm">
                    <i class="fas fa-satellite-dish animate-pulse"></i>
                </span>
                Live Monitor: {{ $quiz->title }}
            </h1>
        </div>
        <div class="flex items-center gap-3 px-5 py-3 bg-white rounded-2xl shadow-sm border border-gray-100">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-sm font-black text-gray-600 tracking-tight">Live Updates Active</span>
        </div>
    </div>

    <!-- Live Card -->
    <div x-data="liveMonitor()" x-init="startPolling()" class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100 flex flex-col">
        <div class="p-6 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="bg-indigo-600 px-4 py-2 rounded-2xl shadow-lg shadow-indigo-200">
                    <p class="text-white text-xs font-black uppercase tracking-widest opacity-80 mb-0.5">Active</p>
                    <p class="text-white text-2xl font-black leading-none" x-text="attempts.length">0</p>
                </div>
                <h2 class="text-xl font-black text-gray-800">Candidates Online</h2>
            </div>
            <button @click="fetchData()" class="flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-black text-gray-700 hover:bg-gray-50 transition shadow-sm active:scale-95">
                <i class="fas fa-sync-alt" :class="{'fa-spin': isFetching}"></i> Refresh Now
            </button>
        </div>

        <div class="overflow-x-auto overflow-y-hidden">
            <table class="min-w-full text-left whitespace-nowrap">
                <thead class="bg-white border-b border-gray-100">
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                        <th class="px-8 py-6">Member Name</th>
                        <th class="px-6 py-6 text-center">Status</th>
                        <th class="px-6 py-6 text-center">Time Left</th>
                        <th class="px-6 py-6 text-center">Step</th>
                        <th class="px-6 py-6 text-center">Violations</th>
                        <th class="px-6 py-6 text-center">Last Active</th>
                        <th class="px-6 py-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="attempt in attempts" :key="attempt.id">
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-colors">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="font-bold text-gray-800" x-text="attempt.user_name"></span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span x-show="attempt.status === 'in_progress'" class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-xs font-black animate-pulse">
                                    In Progress
                                </span>
                                <span x-show="attempt.status === 'pending'" class="bg-amber-50 text-amber-600 px-3 py-1 rounded-full text-xs font-black">
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span x-show="attempt.time_remaining > 0" :class="attempt.time_remaining < 300 ? 'text-red-500' : 'text-gray-900'" class="font-black font-mono text-lg" x-text="formatTime(attempt.time_remaining)"></span>
                                <span x-show="attempt.time_remaining <= 0" class="text-red-500 font-black uppercase text-xs tracking-widest bg-red-50 px-2 py-1 rounded-lg">Suspended</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-xl font-black text-sm" x-text="attempt.current_step"></span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 px-3 py-1.5 rounded-xl border border-red-100" x-show="attempt.violation_count > 0">
                                    <i class="fas fa-shield-virus text-xs"></i>
                                    <span class="font-black text-sm leading-none" x-text="attempt.violation_count"></span>
                                </div>
                                <span x-show="attempt.violation_count === 0" class="text-emerald-500 font-black text-sm">Safe</span>
                            </td>
                            <td class="px-6 py-6 text-center text-[10px] text-gray-400 font-black uppercase tracking-wider" x-text="attempt.last_activity ?? 'Unknown'"></td>
                            <td class="px-6 py-6">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- View Button links to separate observation page -->
                                    <a :href="'{{ route('admin.quizzes.attempts.observe', 999999) }}'.replace('999999', attempt.id)" class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-black shadow-lg shadow-indigo-200 transition transform active:scale-95">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <form :action="'{{ route('admin.quizzes.attempts.cancel', 999999) }}'.replace('999999', attempt.id)" method="POST" onsubmit="return confirm('Force end this attempt?')">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-2xl text-xs font-black shadow-lg shadow-red-200 transition transform active:scale-95">
                                            <i class="fas fa-ban"></i> Cancel Exam
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="attempts.length === 0">
                        <td colspan="7" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center gap-4 text-gray-300">
                                <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center text-3xl">
                                    <i class="fas fa-user-slash"></i>
                                </div>
                                <p class="font-black uppercase tracking-widest text-sm">No Active Candidates</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function liveMonitor() {
    return {
        attempts: [],
        isFetching: false,
        intervalId: null,
        
        async fetchData() {
            this.isFetching = true;
            try {
                const response = await fetch('{{ route('admin.quizzes.live.data', $quiz->id) }}');
                const data = await response.json();
                this.attempts = data.attempts;
            } catch (err) {
                console.error("Live monitor fetch error", err);
            }
            this.isFetching = false;
        },

        formatTime(seconds) {
            if (seconds <= 0) return '00:00';
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m}:${s < 10 ? '0'+s : s}`;
        },

        startPolling() {
            this.fetchData();
            // Poll every 10 seconds
            this.intervalId = setInterval(() => {
                this.fetchData();
            }, 10000);
        }
    }
}
</script>
@endsection
