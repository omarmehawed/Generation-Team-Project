{{-- Workshop Modal Component (New) --}}
<div id="workshopModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" onclick="closeModal('workshopModal')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] w-full max-w-2xl overflow-hidden shadow-2xl scale-95 opacity-0 transition-all duration-300 transform border border-gray-100 dark:border-gray-700" id="workshopModalContent">
        
        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-xl font-black text-gray-800 dark:text-gray-200">Plan New Workshop</h3>
            <button onclick="closeModal('workshopModal')" class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('workshops.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4 md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Workshop Title</label>
                    <input type="text" name="title" required
                        class="w-full rounded-2xl border border-gray-100 dark:border-gray-700 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 px-6 py-4 text-sm font-bold text-gray-800 dark:text-gray-200 transition-all"
                        placeholder="e.g. PCB Design Advanced">
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Workshop Date</label>
                    <input type="date" name="workshop_date" required
                        class="w-full rounded-2xl border border-gray-100 dark:border-gray-700 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 px-6 py-4 text-sm font-bold text-gray-800 dark:text-gray-200 transition-all">
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Workshop Time</label>
                    <input type="time" name="workshop_time" required
                        class="w-full rounded-2xl border border-gray-100 dark:border-gray-700 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 px-6 py-4 text-sm font-bold text-gray-800 dark:text-gray-200 transition-all">
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Location / Link</label>
                    <input type="text" name="location_or_link"
                        class="w-full rounded-2xl border border-gray-100 dark:border-gray-700 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 px-6 py-4 text-sm font-bold text-gray-800 dark:text-gray-200 transition-all"
                        placeholder="Lab Room / Meet Link">
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Domain Scope</label>
                    <select name="domain"
                        class="w-full rounded-2xl border border-gray-100 dark:border-gray-700 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 px-6 py-4 text-sm font-bold text-gray-800 dark:text-gray-200 transition-all appearance-none">
                        <option value="software">Software Team</option>
                        <option value="hardware">Hardware Team</option>
                        <option value="general">General (Both)</option>
                    </select>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Workshop Type</label>
                    <select name="type"
                        class="w-full rounded-2xl border border-gray-100 dark:border-gray-700 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 px-6 py-4 text-sm font-bold text-gray-800 dark:text-gray-200 transition-all appearance-none">
                        <option value="offline">In-Person</option>
                        <option value="online">Online Session</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeModal('workshopModal')" 
                    class="px-6 py-3 rounded-2xl font-bold text-gray-400 hover:bg-gray-100 transition-colors text-xs uppercase tracking-widest">Discard</button>
                <button type="submit" 
                    class="px-8 py-3 rounded-2xl font-black text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl shadow-indigo-500/30 hover:-translate-y-0.5 transition-all text-xs uppercase tracking-widest">
                    Create Workshop
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openWorkshopModal() {
        document.getElementById('workshopModal').classList.remove('hidden');
        document.getElementById('workshopModal').classList.add('flex');
        setTimeout(() => {
            document.getElementById('workshopModalContent').classList.remove('scale-95', 'opacity-0');
        }, 10);
        document.body.classList.add('modal-open');
    }
</script>
