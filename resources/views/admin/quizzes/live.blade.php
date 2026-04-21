@extends('layouts.batu')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="mb-4 flex justify-between items-end">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="text-gray-500 hover:text-black font-bold text-sm mb-2 inline-block"><i class="fas fa-arrow-left"></i> Back to Quizzes</a>
            <h1 class="text-3xl font-black text-gray-800"><i class="fas fa-satellite-dish animate-pulse text-red-500 mr-2"></i> Live Monitor: {{ $quiz->title }}</h1>
        </div>
        <div class="flex items-center gap-2 text-sm font-bold text-gray-500 bg-white px-4 py-2 rounded-xl shadow-sm">
            <span class="w-3 h-3 bg-green-500 rounded-full animate-ping"></span> Live Updates Active
        </div>
    </div>

    <!-- Live Alpine Component -->
    <div x-data="liveMonitor()" x-init="startPolling()" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
            <p class="font-bold text-gray-700">Active Attempts: <span x-text="attempts.length" class="text-blue-600 text-xl font-black"></span></p>
            <button @click="fetchData()" class="btn-royal-gold px-3 py-1.5 rounded shadow text-xs"><i class="fas fa-sync-alt" :class="{'fa-spin': isFetching}"></i> Refresh Now</button>
        </div>

        <table class="min-w-full text-left text-sm whitespace-nowrap">
            <thead class="uppercase tracking-wider border-b border-gray-200 bg-white">
                <tr>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold">Member Name</th>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold text-center">Status</th>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold text-center">Time Left</th>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold text-center">Step</th>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold text-center">Violations</th>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold text-center">Last Active</th>
                    <th scope="col" class="px-6 py-4 text-gray-400 font-bold text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="attempt in attempts" :key="attempt.id">
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-800">
                            <i class="fas fa-user-circle text-gray-400 mr-2"></i> <span x-text="attempt.user_name"></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span x-show="attempt.status === 'in_progress'" class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold animate-pulse">In Progress</span>
                            <span x-show="attempt.status === 'pending'" class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">Pending</span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold font-mono">
                            <span x-show="attempt.time_remaining > 0" :class="attempt.time_remaining < 300 ? 'text-red-500' : 'text-gray-700'" x-text="Math.floor(attempt.time_remaining / 60) + 'm ' + (attempt.time_remaining % 60) + 's'"></span>
                            <span x-show="attempt.time_remaining <= 0" class="text-red-500">Over</span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-gray-600">
                            <span x-text="attempt.current_step"></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span x-show="attempt.violation_count == 0" class="text-green-500 font-bold border border-green-200 bg-green-50 rounded px-2 py-0.5">0</span>
                            <span x-show="attempt.violation_count > 0" class="text-red-600 font-black border border-red-200 bg-red-50 rounded px-2 py-0.5 flex items-center justify-center gap-1 mx-auto w-fit"><i class="fas fa-exclamation-triangle text-[10px]"></i> <span x-text="attempt.violation_count"></span></span>
                        </td>
                        <td class="px-6 py-4 text-center text-xs text-gray-500" x-text="attempt.last_activity ?? 'Unknown'"></td>
                        <td class="px-6 py-4 text-center">
                            <form :action="'{{ route('admin.quizzes.attempts.cancel', 999999) }}'.replace('999999', attempt.id)" method="POST" onsubmit="return confirm('Force end this attempt and mark score as 0?')">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow text-xs font-bold"><i class="fas fa-ban"></i> Cancel Exam</button>
                            </form>
                        </td>
                    </tr>
                </template>
                <tr x-show="attempts.length === 0">
                    <td colspan="7" class="py-12 text-center text-gray-400 font-bold"><i class="fas fa-bed text-3xl mb-2 block"></i> Nobody is taking this exam right now.</td>
                </tr>
            </tbody>
        </table>
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
