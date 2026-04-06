{{-- Workshop Attendance Modal for Evaluation --}}
<div id="attendanceWorkshopModal" x-data="workshopAttendanceManager()" 
    class="fixed inset-0 z-[10000] hidden items-center justify-center p-4">
    
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" @click="closeModal()"></div>
    
    <div id="attendanceWorkshopModalContent" 
        class="relative bg-white dark:bg-[#0f172a] rounded-[2.5rem] w-full max-w-2xl overflow-hidden shadow-2xl scale-95 opacity-0 transition-all duration-300 transform border border-gray-100 dark:border-gray-800 flex flex-col max-h-[90vh]">
        
        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-xl font-black text-gray-800 dark:text-gray-100" id="attendanceWorkshopTitle">Workshop Attendance</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Record attendance and participation scores.</p>
            </div>
            <button @click="closeModal()" class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Search Bar --}}
        <div class="px-8 py-4 bg-gray-50/30 dark:bg-gray-800/10 border-b border-gray-50 dark:border-gray-800">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" x-model="searchQuery" placeholder="Search members by name, email or academic ID..."
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-gray-100 dark:border-gray-800 bg-white dark:bg-[#111827] text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder-gray-400">
            </div>
        </div>

        <form id="attendanceWorkshopForm" @submit.prevent="submitForm()" class="flex flex-col flex-1 overflow-hidden">
            <div class="flex-1 overflow-y-auto p-8 space-y-4 custom-scroll">
                {{-- Loading State --}}
                <div x-show="isLoading" class="flex flex-col items-center justify-center py-20">
                    <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="mt-4 text-xs font-black text-gray-400 uppercase tracking-widest">Fetching Attendee List...</p>
                </div>

                {{-- Empty State --}}
                <div x-show="!isLoading && filteredAttendees.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-user-slash text-2xl text-gray-300"></i>
                    </div>
                    <h4 class="text-gray-500 dark:text-gray-400 font-bold">No Members Found</h4>
                    <p class="text-xs text-gray-400 mt-1">Try a different search term or check team assignments.</p>
                </div>

                {{-- List --}}
                <div x-show="!isLoading && filteredAttendees.length > 0" class="space-y-3">
                    <template x-for="(a, index) in filteredAttendees" :key="a.user_id">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 rounded-3xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/10 hover:border-indigo-200 dark:hover:border-indigo-900/50 transition-all group">
                            {{-- Member Info --}}
                            <div class="flex items-center gap-4 sm:w-1/3">
                                <div class="w-10 h-10 rounded-2xl bg-indigo-600 flex items-center justify-center text-xs font-black text-white shadow-lg shadow-indigo-500/20" x-text="a.name.substring(0, 2).toUpperCase()"></div>
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-gray-800 dark:text-gray-200 truncate" x-text="a.name"></p>
                                    <p class="text-[10px] text-gray-400 font-bold truncate" x-text="a.email"></p>
                                </div>
                            </div>

                            {{-- Status Select --}}
                            <div class="flex-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Attendance Status</label>
                                <select x-model="a.status" 
                                    class="w-full bg-white dark:bg-[#111827] border-2 border-gray-100 dark:border-gray-800 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                                    @change="a.status === 'absent' ? a.participation_score = 0 : null">
                                    <option value="pending">⏳ Pending</option>
                                    <option value="attended">✅ Present</option>
                                    <option value="late">⏰ Late</option>
                                    <option value="absent">❌ Absent</option>
                                </select>
                            </div>

                            {{-- Score Input --}}
                            <div class="w-full sm:w-24" x-show="canScore">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Score / 10</label>
                                <input type="number" x-model.number="a.participation_score" min="0" max="10" step="0.5" 
                                    :disabled="a.status === 'absent'"
                                    :class="a.status === 'absent' ? 'opacity-30 cursor-not-allowed' : ''"
                                    class="w-full bg-white dark:bg-[#111827] border-2 border-gray-100 dark:border-gray-800 rounded-xl px-4 py-2.5 text-xs font-black text-center focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-5 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20 flex justify-between items-center">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest" x-text="attendees.length + ' members total'"></p>
                <div class="flex gap-3">
                    <button type="button" @click="closeModal()" 
                        class="px-5 py-2.5 rounded-xl font-bold text-xs text-gray-500 uppercase tracking-widest transition-colors hover:text-gray-800 dark:hover:text-white">Cancel</button>
                    <button type="submit" :disabled="isSaving || isLoading"
                        class="px-8 py-2.5 rounded-xl font-black text-xs text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl shadow-indigo-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center gap-2">
                        <i class="fas fa-spinner fa-spin" x-show="isSaving"></i>
                        <span x-text="isSaving ? 'Saving...' : 'Save Records'"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function workshopAttendanceManager() {
        return {
            workshopId: null,
            isLoading: false,
            isSaving: false,
            canScore: true, // Default to true until fetched
            searchQuery: '',
            attendees: [],

            get filteredAttendees() {
                if (this.searchQuery.trim() === '') return this.attendees;
                const q = this.searchQuery.toLowerCase();
                return this.attendees.filter(a => 
                    a.name.toLowerCase().includes(q) || 
                    a.email.toLowerCase().includes(q)
                );
            },

            open(id, title) {
                this.workshopId = id;
                this.searchQuery = '';
                document.getElementById('attendanceWorkshopTitle').innerText = title;
                
                const modal = document.getElementById('attendanceWorkshopModal');
                const content = document.getElementById('attendanceWorkshopModalContent');
                
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => content.classList.remove('scale-95', 'opacity-0'), 10);
                
                this.fetchAttendees();
            },

            closeModal() {
                const modal = document.getElementById('attendanceWorkshopModal');
                const content = document.getElementById('attendanceWorkshopModalContent');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    this.attendees = [];
                }, 300);
            },

            fetchAttendees() {
                this.isLoading = true;
                fetch(`/final-project/workshops/${this.workshopId}/attendees`)
                    .then(r => r.json())
                    .then(data => {
                        this.attendees = data.attendees || [];
                        this.canScore = data.can_score ?? true;
                        this.isLoading = false;
                    })
                    .catch(e => {
                        console.error(e);
                        this.isLoading = false;
                        alert('Failed to load attendees.');
                    });
            },

            submitForm() {
                this.isSaving = true;
                const token = document.querySelector('meta[name="csrf-token"]').content;
                
                fetch(`/final-project/workshops/${this.workshopId}/attendance`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        attendees: this.attendees.map(a => ({
                            id: a.id,
                            status: a.status,
                            participation_score: a.participation_score
                        }))
                    })
                })
                .then(r => r.json())
                .then(res => {
                    this.isSaving = false;
                    this.closeModal();
                    // Optional: Show success message via SweetAlert if available
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Attendance and scores updated.',
                            icon: 'success',
                            background: '#111827',
                            color: '#fff',
                            confirmButtonColor: '#6366f1'
                        });
                    } else {
                        alert('Attendance updated successfully.');
                    }
                })
                .catch(e => {
                    this.isSaving = false;
                    console.error(e);
                    alert('Failed to save attendance.');
                });
            }
        }
    }

    function openWorkshopAttendanceModal(id, title) {
        // Find the Alpine data and call open
        const el = document.getElementById('attendanceWorkshopModal');
        if (el) {
            Alpine.$data(el).open(id, title);
        }
    }
</script>
