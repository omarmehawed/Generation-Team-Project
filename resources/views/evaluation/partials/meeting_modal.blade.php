{{-- Meeting Modal (Enhanced) --}}
<style>
    [x-cloak] { display: none !important; }
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 10px; }
    .dark .custom-scroll::-webkit-scrollbar-thumb { background-color: #374151; }
</style>

<div id="meetingModal" x-data="meetingModalData()" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" @click="closeModal()"></div>

    <div id="meetingModalContent" class="relative bg-white dark:bg-[#0f172a] rounded-[3rem] w-full max-w-2xl overflow-hidden shadow-2xl scale-95 opacity-0 transition-all duration-300 transform border border-gray-100 dark:border-gray-800">
        
        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-xl font-black text-gray-800 dark:text-gray-100">Schedule Internal Meeting</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Create a new meeting for your team members.</p>
            </div>
            <button @click="closeModal()" type="button" class="w-10 h-10 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="p-8 overflow-y-auto max-h-[70vh] custom-scroll">
            <form id="scheduleMeetingForm" action="{{ route('final_project.storeInternalMeeting') }}" method="POST">
                @csrf
                <input type="hidden" name="team_id" value="{{ $team->id }}">
                
                <div class="space-y-5">
                    {{-- Topic --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Meeting Topic <span class="text-red-500">*</span></label>
                        <input type="text" name="topic" required class="w-full bg-gray-50 dark:bg-[#0b1220] border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition-colors" placeholder="e.g. Weekly Sync, Code Review...">
                    </div>

                    {{-- Date & Time --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Date & Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="meeting_date" required class="w-full bg-gray-50 dark:bg-[#0b1220] border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition-colors">
                    </div>

                    {{-- Mode Selection --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Meeting Mode <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="mode" value="online" x-model="mode" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 text-center transition-all">
                                    <i class="fas fa-video text-2xl mb-2 text-indigo-500"></i>
                                    <div class="font-bold text-gray-900 dark:text-white">Online</div>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="mode" value="offline" x-model="mode" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 text-center transition-all">
                                    <i class="fas fa-handshake text-2xl mb-2 text-green-500"></i>
                                    <div class="font-bold text-gray-900 dark:text-white">In-Person</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Online Specific --}}
                    <div x-show="mode === 'online'" x-cloak x-transition>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Meeting Link <span class="text-red-500">*</span></label>
                        <input type="url" name="meeting_link" :required="mode === 'online'" class="w-full bg-gray-50 dark:bg-[#0b1220] border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition-colors" placeholder="https://meet.google.com/xyz...">
                    </div>

                    {{-- Offline Specific --}}
                    <div x-show="mode === 'offline'" x-cloak x-transition>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Location <span class="text-red-500">*</span></label>
                        <select name="location_type" x-model="locationType" :required="mode === 'offline'" class="w-full bg-gray-50 dark:bg-[#0b1220] border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition-colors mb-4">
                            <option value="">Select Location</option>
                            <option value="college">College Campus</option>
                            <option value="other">Other Specifc Location</option>
                        </select>

                        <div x-show="locationType === 'other'" x-cloak x-transition>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Specify Location <span class="text-red-500">*</span></label>
                            <input type="text" name="custom_location" :required="mode === 'offline' && locationType === 'other'" class="w-full bg-gray-50 dark:bg-[#0b1220] border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition-colors" placeholder="e.g. Starbucks, Main Library...">
                        </div>
                    </div>

                </div>

                <div class="flex justify-end pt-6 mt-6 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" @click="closeModal()" class="px-5 py-2.5 rounded-xl font-bold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors mr-3">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/30">
                        Create Meeting <i class="fas fa-paper-plane ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function meetingModalData() {
        return {
            mode: 'online',
            locationType: '',
            
            closeModal() {
                const modal = document.getElementById('meetingModal');
                const content = document.getElementById('meetingModalContent');

                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    this.mode = 'online';
                    this.locationType = '';
                    document.getElementById('scheduleMeetingForm').reset();
                }, 300);
            }
        }
    }

    function openMeetingModal() {
        const modal = document.getElementById('meetingModal');
        const content = document.getElementById('meetingModalContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
</script>
