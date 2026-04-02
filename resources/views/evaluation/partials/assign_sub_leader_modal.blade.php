{{-- Assign Sub Leader Modal (Safe Fixed Version) --}}
<style>
    [x-cloak] {
        display: none !important;
    }
</style>

@php
    $assignableMembers = collect($searchableMembers ?? [])->map(function ($m) {
        $user = $m->user ?? null;

        return [
            'id' => $m->id ?? null,
            'name' => $user->name ?? 'Unknown',
            'email' => $user->email ?? 'N/A',
            'academic_number' => $m->academic_number ?? 'N/A',
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=6366f1&color=fff&bold=true',
            'current_technical_role' => strtolower($m->technical_role ?? 'software'),
            'team' => $m->team_number ?? '-',
        ];
    })->values()->toArray();
@endphp

<div id="assignSubLeaderModal"
    x-data="assignSubLeaderModalData(@js($assignableMembers), {{ ($isLeader ?? false) ? 'true' : 'false' }})"
    class="fixed inset-0 z-[9999] hidden items-center justify-center p-4">

    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" @click="closeModal()"></div>

    <div id="assignSubLeaderModalContent"
        class="relative bg-white dark:bg-[#0f172a] rounded-[3rem] w-full max-w-3xl overflow-visible shadow-2xl scale-95 opacity-0 transition-all duration-300 transform border border-gray-100 dark:border-gray-800">

        <div
            class="px-8 py-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-xl font-black text-gray-800 dark:text-gray-100">Assign Sub Leader</h3>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest mt-1">
                    <span x-text="members.length + ' members available'"></span>
                </p>
            </div>

            <button type="button" @click="closeModal()"
                class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('evaluation.assign-sub-leader', $team->id) }}" method="POST">
            @csrf

            <div class="p-8 space-y-6">

                {{-- Step 1 --}}
                <div x-show="step === 1" x-transition x-cloak>
                    <div class="relative">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4 ml-1">
                            Step 1: Search by Name, Email or Academic ID
                        </label>

                        <div class="relative">
                            <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-xl text-indigo-500"></i>

                            <input type="text"
                                   x-model="search"
                                   @keydown.enter.prevent="if(filteredMembers.length > 0) selectMember(filteredMembers[0])"
                                   placeholder="Enter name, email or ID..."
                                   class="w-full rounded-[2rem] border-2 border-gray-100 dark:border-gray-800 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 dark:bg-[#111827] pl-16 pr-8 py-6 text-base font-bold text-gray-800 dark:text-gray-200 transition-all shadow-inner">

                            <div x-show="search.trim().length > 0 && !selectedMember"
                                 x-cloak
                                 class="absolute left-0 right-0 top-full mt-3 z-[10010] bg-white dark:bg-[#111827] border-2 border-indigo-500 rounded-[2rem] shadow-2xl overflow-hidden">
                                <div class="max-h-[450px] overflow-y-auto custom-scroll">
                                    <template x-for="m in filteredMembers" :key="m.id">
                                        <div @click="selectMember(m)"
                                             class="px-8 py-6 hover:bg-indigo-600 hover:text-white cursor-pointer flex items-center gap-8 transition-all border-b border-gray-50 dark:border-gray-800 last:border-none group">
                                            <img :src="m.avatar" class="w-14 h-14 rounded-2xl border-2 border-white dark:border-gray-800 shadow-md group-hover:scale-105 transition-transform">
                                            <div class="overflow-hidden flex-1 grid grid-cols-2 gap-6 items-center">
                                                <div>
                                                    <p class="text-base font-black group-hover:text-white" x-text="m.name"></p>
                                                    <p class="text-xs opacity-70 font-bold" x-text="m.email"></p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs font-black uppercase tracking-widest opacity-60" x-text="'Current Team #' + m.team"></p>
                                                    <p class="text-xs font-bold" x-text="'ID: ' + m.academic_number"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <div x-show="search.length > 0 && filteredMembers.length === 0" class="p-16 text-center">
                                        <p class="text-lg text-gray-500 dark:text-gray-400 font-bold">No members found</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div x-show="step === 2" x-transition x-cloak class="space-y-6">
                    <div
                        class="p-6 bg-indigo-50/50 dark:bg-indigo-500/5 border border-indigo-100/50 dark:border-indigo-500/10 rounded-[2rem] flex items-center gap-4">
                        <img :src="selectedMember ? selectedMember.avatar : ''"
                            class="w-16 h-16 rounded-2xl border-2 border-white dark:border-gray-800 shadow-md"
                            alt="Selected member avatar">

                        <div class="flex-1">
                            <h4 class="font-black text-gray-900 dark:text-white leading-tight"
                                x-text="selectedMember ? selectedMember.name : ''"></h4>

                            <p class="text-[10px] text-indigo-500 font-black uppercase tracking-widest mt-1"
                                x-text="selectedMember ? 'Academic ID: ' + selectedMember.academic_number : ''"></p>
                        </div>

                        <div class="text-right">
                            <span
                                class="rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 px-3 py-1 text-[8px] font-black uppercase"
                                x-text="technical_role || 'unassigned'"></span>
                        </div>
                    </div>

                    <input type="hidden" name="member_id" :value="selectedMember ? selectedMember.id : ''">
                    <input type="hidden" name="technical_role" :value="technical_role">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 ml-1">
                                Step 2: Assign Team Number
                            </label>

                            <select name="team_number" x-model="team_number" required
                                class="w-full rounded-2xl border border-gray-100 dark:border-gray-800 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 dark:bg-[#111827] px-5 py-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                                <option value="">Select Team Number</option>
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}">Team #{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div x-show="isLeader" x-cloak>
                            <label
                                class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 ml-1">
                                Step 3: Assign Domain
                            </label>

                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="technical_role = 'software'" :class="technical_role === 'software'
                                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
                                            : 'bg-gray-100 dark:bg-gray-800 text-gray-500'"
                                    class="py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                                    Software
                                </button>

                                <button type="button" @click="technical_role = 'hardware'" :class="technical_role === 'hardware'
                                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
                                            : 'bg-gray-100 dark:bg-gray-800 text-gray-500'"
                                    class="py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                                    Hardware
                                </button>
                            </div>
                        </div>

                        <div x-show="!isLeader" x-cloak>
                            <label
                                class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 ml-1">
                                Step 3: Domain
                            </label>

                            <div
                                class="w-full rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-[#111827] px-5 py-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                                Auto selected:
                                <span class="text-indigo-600 dark:text-indigo-400 uppercase"
                                    x-text="technical_role"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="px-8 py-6 bg-gray-50/50 dark:bg-gray-800/20 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center gap-4">
                <button type="button" @click="step === 1 ? closeModal() : prevStep()"
                    class="px-6 py-3 rounded-2xl font-bold text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors text-xs uppercase tracking-widest">
                    <span x-text="step === 1 ? 'Cancel' : 'Back'"></span>
                </button>

                <div x-show="step === 2" x-cloak>
                    <button type="submit" :disabled="!selectedMember || !team_number || !technical_role"
                        class="px-10 py-3 rounded-2xl font-black text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl shadow-indigo-500/30 hover:-translate-y-0.5 disabled:opacity-50 disabled:translate-y-0 disabled:cursor-not-allowed transition-all text-sm uppercase tracking-widest">
                        Confirm Assignment
                        <i class="fas fa-check-circle ml-2 opacity-50"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function assignSubLeaderModalData(membersData, isLeaderFlag) {
        return {
            step: 1,
            search: '',
            selectedMember: null,
            team_number: '',
            technical_role: '',
            isLeader: isLeaderFlag,
            members: membersData || [],

            get filteredMembers() {
                if (this.search.trim() === '') return [];

                const q = this.search.toLowerCase().trim();

                return this.members.filter(m =>
                    String(m.name || '').toLowerCase().includes(q) ||
                    String(m.email || '').toLowerCase().includes(q) ||
                    String(m.academic_number || '').includes(this.search.trim())
                );
            },

            selectMember(m) {
                this.selectedMember = m;
                this.search = '';
                this.technical_role = m.current_technical_role || '';
                this.step = 2;
            },

            prevStep() {
                if (this.step > 1) {
                    this.step--;
                }

                if (this.step === 1) {
                    this.selectedMember = null;
                    this.team_number = '';
                }
            },

            resetModal() {
                this.step = 1;
                this.search = '';
                this.selectedMember = null;
                this.team_number = '';
                this.technical_role = '';
            },

            closeModal() {
                closeModal('assignSubLeaderModal');
                setTimeout(() => this.resetModal(), 200);
            }
        }
    }

    function openAssignSubLeaderModal() {
        const modal = document.getElementById('assignSubLeaderModal');
        const content = document.getElementById('assignSubLeaderModalContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
        }, 10);

        document.body.classList.add('modal-open');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + 'Content');

        if (content) {
            content.classList.add('scale-95', 'opacity-0');
        }

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('modal-open');
        }, 200);
    }
</script>