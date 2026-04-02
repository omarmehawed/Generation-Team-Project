{{-- Define New Week Modal --}}
<div id="definePeriodModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" onclick="closeModal('definePeriodModal')"></div>
    <div class="relative bg-white dark:bg-[#0f172a] rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-2xl scale-95 opacity-0 transition-all duration-300 transform border border-gray-100 dark:border-gray-800" id="definePeriodModalContent">
        
        {{-- Header --}}
        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-xl font-black text-gray-800 dark:text-gray-100">Define New Week</h3>
                <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest mt-1">Manual Evaluation Period</p>
            </div>
            <button onclick="closeModal('definePeriodModal')" class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('evaluation.store-period', $team->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            {{-- Week Number --}}
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Week Number</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-hashtag text-xs"></i>
                    </div>
                    <input type="number" name="week_number" required min="1" value="{{ ($allPeriods->first()?->week_number ?? 0) + 1 }}"
                        class="w-full pl-10 pr-4 py-3 rounded-2xl border border-gray-100 dark:border-gray-800 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 dark:bg-[#111827] text-sm font-bold text-gray-800 dark:text-gray-200 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Start Date --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Start Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <i class="far fa-calendar-alt text-xs"></i>
                        </div>
                        <input type="date" name="start_date" required value="{{ now()->format('Y-m-d') }}"
                            class="w-full pl-10 pr-4 py-3 rounded-2xl border border-gray-100 dark:border-gray-800 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 dark:bg-[#111827] text-sm font-bold text-gray-800 dark:text-gray-200 transition-all">
                    </div>
                </div>

                {{-- End Date --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">End Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <i class="far fa-calendar-check text-xs"></i>
                        </div>
                        <input type="date" name="end_date" required value="{{ now()->addDays(6)->format('Y-m-d') }}"
                            class="w-full pl-10 pr-4 py-3 rounded-2xl border border-gray-100 dark:border-gray-800 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 dark:bg-[#111827] text-sm font-bold text-gray-800 dark:text-gray-200 transition-all">
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30 flex gap-3">
                <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                <p class="text-[10px] text-amber-700 dark:text-amber-400 font-bold leading-relaxed">
                    Setting a new week will automatically close any current open week. All tasks, workshops, and meetings within this range will be automatically tracked.
                </p>
            </div>

            {{-- Footer --}}
            <div class="pt-4 flex justify-end items-center gap-4">
                <button type="button" onclick="closeModal('definePeriodModal')" 
                    class="px-6 py-2.5 rounded-xl font-bold text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors text-xs uppercase tracking-widest">Cancel</button>
                <button type="submit" 
                    class="px-8 py-2.5 rounded-xl font-black text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl shadow-indigo-500/30 transition-all flex items-center gap-2 text-xs uppercase tracking-widest">
                    Create Period <i class="fas fa-arrow-right text-[10px] opacity-50"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDefinePeriodModal() {
        document.getElementById('definePeriodModal').classList.remove('hidden');
        document.getElementById('definePeriodModal').classList.add('flex');
        setTimeout(() => {
            document.getElementById('definePeriodModalContent').classList.remove('scale-95', 'opacity-0');
        }, 10);
        document.body.classList.add('modal-open');
    }
</script>
