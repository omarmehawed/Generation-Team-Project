{{-- Assign Member Modal --}}
<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<div id="assignMemberModal" x-data="assignMemberModalData()"
    class="fixed inset-0 z-[9999] hidden items-center justify-center p-4">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" @click="closeModal()"></div>

    {{-- Modal --}}
    <div id="assignMemberModalContent"
        class="relative bg-white dark:bg-[#0f172a] rounded-[3rem] w-full max-w-2xl overflow-visible shadow-2xl scale-95 opacity-0 transition-all duration-300 transform border border-gray-100 dark:border-gray-800">

        {{-- Header --}}
        <div
            class="px-8 py-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h3 class="text-xl font-black text-gray-800 dark:text-gray-100">Add Member to Team</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Select an unassigned member to add to your team module.</p>
            </div>
            <button @click="closeModal()" type="button"
                class="w-10 h-10 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Content --}}
        <div class="p-8">
            <form id="assignMemberForm" action="{{ route('evaluation.assign-member', $team->id ?? 0) }}" method="POST">
                @csrf
                <input type="hidden" name="member_id" x-model="selectedMemberId">

                {{-- Step 1: Member Selection --}}
                <div x-show="step === 1" x-transition.opacity.duration.300ms>
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">
                                1
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200">Select Member</h4>
                        </div>

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
                                                    <p class="text-xs font-black uppercase tracking-widest opacity-60" x-text="m.member_tech"></p>
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

                        {{-- Selected Member Card --}}
                        <div x-show="selectedMember" x-cloak x-transition
                            class="mt-4 p-5 rounded-2xl border-2 border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/10 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <img :src="selectedMember?.avatar"
                                    class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                                <div>
                                    <div class="text-base font-bold text-gray-900 dark:text-white"
                                        x-text="selectedMember?.name"></div>
                                    <div class="text-xs text-gray-500" x-text="selectedMember?.email"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-lg"
                                    x-text="selectedMember?.technical_role"></span>
                                <i class="fas fa-check-circle text-indigo-500 text-xl"></i>
                            </div>
                        </div>

                    </div>
                    
                    {{-- Continue Button --}}
                    <div class="flex justify-end pt-6 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" @click="nextStep()" :disabled="!selectedMember"
                            class="px-6 py-3 rounded-xl font-bold text-sm text-white transition-all shadow-lg"
                            :class="selectedMember ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-300 dark:bg-gray-700 cursor-not-allowed'">
                            Continue
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                {{-- Step 2: Confirmation --}}
                <div x-show="step === 2" x-cloak x-transition.opacity.duration.300ms>
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center font-bold text-sm">
                                2
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200">Confirm Assignment</h4>
                        </div>
                        
                        <div class="p-6 rounded-2xl bg-gray-50 dark:bg-[#0b1220] border border-gray-100 dark:border-gray-800 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 font-medium">
                                @if($isSubLeader)
                                    You are about to add <span class="font-bold text-gray-900 dark:text-white" x-text="selectedMember?.name"></span> to your team module (Team <span class="font-bold text-indigo-500">#{{ $myMember->team_number ?? '-' }}</span>).
                                @else
                                    Select a team number to assign <span class="font-bold text-gray-900 dark:text-white" x-text="selectedMember?.name"></span> to:
                                @endif
                            </p>
                            
                            @if(!$isSubLeader)
                            <div class="mt-4 max-w-xs mx-auto">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 text-left">Target Team Number</label>
                                <select name="team_number" required class="w-full bg-white dark:bg-[#111827] border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition-colors">
                                    <option value="">Select Team</option>
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}">Team #{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            @endif

                            <p class="text-[10px] text-gray-500 italic mt-4">This will allow the corresponding sub-leader to evaluate their weekly performance.</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-between items-center pt-6 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" @click="step = 1"
                            class="px-5 py-2.5 rounded-xl font-bold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </button>
                        <button type="submit"
                            class="px-6 py-3 rounded-xl font-bold text-sm text-white bg-green-600 hover:bg-green-700 transition-all shadow-lg shadow-green-500/30">
                            Confirm Assignment
                            <i class="fas fa-check ml-2"></i>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    function assignMemberModalData() {
        return {
            step: 1,
            search: '',
            members: @json($unassignedMembersFormatted),
            selectedMember: null,
            selectedMemberId: '',

            get filteredMembers() {
                if (this.search.trim() === '') return [];
                const query = this.search.toLowerCase();
                return this.members.filter(m =>
                    m.name.toLowerCase().includes(query) ||
                    m.email.toLowerCase().includes(query) ||
                    m.academic_number.toString().includes(query)
                );
            },

            selectMember(member) {
                this.selectedMember = member;
                this.selectedMemberId = member.id;
                this.search = '';
            },

            clearSelection() {
                this.selectedMember = null;
                this.selectedMemberId = '';
                this.step = 1;
            },

            selectFirstResult() {
                if (!this.selectedMember && this.search.trim().length > 0 && this.filteredMembers.length > 0) {
                    this.selectMember(this.filteredMembers[0]);
                } else if (this.selectedMember && this.step === 1) {
                    this.nextStep();
                } else if (this.selectedMember && this.step === 2) {
                    document.getElementById('assignMemberForm').submit();
                }
            },

            nextStep() {
                if (this.selectedMember) {
                    this.step = 2;
                }
            },

            closeModal() {
                const modal = document.getElementById('assignMemberModal');
                const content = document.getElementById('assignMemberModalContent');

                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    this.clearSelection(); // Reset state
                }, 300);
            }
        }
    }

    function openAssignMemberModal() {
        const modal = document.getElementById('assignMemberModal');
        const content = document.getElementById('assignMemberModalContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Small delay to allow the removal of 'hidden' to render before applying transitions
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
</script>
