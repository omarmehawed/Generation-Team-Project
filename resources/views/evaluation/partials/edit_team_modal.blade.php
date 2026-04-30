{{-- Edit Team Modal --}}
<div id="editTeamModal" x-data="editTeamModalData()"
    @open-edit-team.window="open($event.detail.id, $event.detail.team, $event.detail.name)"
    class="fixed inset-0 z-[9999] hidden items-center justify-center p-4" x-cloak>

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" @click="closeModal()"></div>

    {{-- Modal Content --}}
    <div id="editTeamModalContent"
        class="relative bg-white dark:bg-gray-800 rounded-[3rem] w-full max-w-lg overflow-visible shadow-2xl scale-95 opacity-0 transition-all duration-300 transform border border-gray-100 dark:border-gray-700">

        {{-- Header --}}
        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-xl font-black text-gray-800 dark:text-gray-200">Edit Team Number</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-bold italic" x-text="'Member: ' + memberName"></p>
            </div>
            <button @click="closeModal()" type="button"
                class="w-10 h-10 rounded-2xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-8">
            <form id="editTeamForm" :action="'/evaluation/team/{{ $team->id }}/assign-member'" method="POST">
                @csrf
                <input type="hidden" name="member_id" :value="memberId">

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">
                            New Team Number
                        </label>
                        <select name="team_number" x-model="teamNumber" required
                            class="w-full bg-gray-50 dark:bg-gray-900 border-2 border-gray-100 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-base font-bold rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 block p-5 transition-all shadow-inner">
                            <option value="">Select Team</option>
                            @for($i=1; $i<=20; $i++)
                                <option value="{{ $i }}">Team #{{ $i }}</option>
                            @endfor
                        </select>
                        <p class="text-[10px] text-gray-400 mt-3 italic text-center font-medium">Changing the team number will reassign this member to the corresponding sub-leader.</p>
                    </div>

                    <div class="pt-4 border-t border-gray-50 flex justify-end gap-3">
                        <button type="button" @click="closeModal()"
                            class="px-6 py-3 rounded-xl font-bold text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-8 py-3 rounded-xl font-black text-sm text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/25">
                            Update Team
                            <i class="fas fa-check ml-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editTeamModalData() {
    return {
        memberId: '',
        memberName: '',
        teamNumber: '',

        open(id, currentTeam, name) {
            this.memberId = id;
            this.teamNumber = currentTeam || '';
            this.memberName = name;

            const modal = document.getElementById('editTeamModal');
            const content = document.getElementById('editTeamModalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        },

        closeModal() {
            const modal = document.getElementById('editTeamModal');
            const content = document.getElementById('editTeamModalContent');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }
    }
}

window.openEditTeamModal = function(id, currentTeam, name) {
    window.dispatchEvent(new CustomEvent('open-edit-team', { 
        detail: { id, team: currentTeam, name } 
    }));
};
</script>
