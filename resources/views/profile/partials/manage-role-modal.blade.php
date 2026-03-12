{{-- Manage Role Modal (Adapted for Profile Page) --}}
@props(['team'])

<div id="manageMemberModal" class="hidden relative z-[100]" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    {{-- Backdrop & Centering Wrapper --}}
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden p-4 sm:p-6 md:p-20">

        {{-- Overlay --}}
        <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true"
            onclick="closeModal('manageMemberModal')"></div>

        {{-- Modal Content --}}
        <div
            class="relative w-full max-w-lg transform rounded-2xl bg-white text-left shadow-2xl transition-all border-t-8 border-[#D4AF37] !border-t-[#D4AF37]">
            <form action="{{ route('final_project.updateMember') }}" method="POST">
                @csrf
                <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <div class="bg-white px-8 pt-8 pb-6 rounded-t-xl">
                    <div class="flex items-center gap-3 mb-6 text-[#AA8A26]">
                        <div class="p-2 bg-[#FFF8E1] rounded-full"><i class="fas fa-user-cog text-xl"></i></div>
                        <h3 class="text-xl font-black text-gray-900">Manage Role: {{ $user->name }}</h3>
                    </div>

                    {{-- Roles --}}
                    <div class="mb-5">
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Administrative
                            Role</label>
                        @php
                            $isMainLeader = isset($team) && $user->id == $team->leader_id;
                            $currentRole = isset($team) ? $team->members->where('user_id', $user->id)->first()?->role : 'member';
                        @endphp
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="leader" class="peer hidden" {{ $isMainLeader ? 'checked' : 'disabled' }}>
                                <div
                                    class="p-3 rounded-xl border-2 border-gray-100 text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all hover:bg-gray-50 {{ !$isMainLeader ? 'bg-gray-50 opacity-50 cursor-not-allowed' : '' }}">
                                    <p class="text-sm font-bold text-gray-600 peer-checked:text-yellow-700">Leader</p>
                                </div>
                            </label>

                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="vice_leader" class="peer hidden" {{ $currentRole === 'vice_leader' ? 'checked' : '' }} {{ $isMainLeader ? 'disabled' : '' }}>
                                <div
                                    class="p-3 rounded-xl border-2 border-gray-100 text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all hover:bg-gray-50 {{ $isMainLeader ? 'bg-gray-50 opacity-50 cursor-not-allowed' : '' }}">
                                    <p class="text-sm font-bold text-gray-600 peer-checked:text-purple-700">Vice Head
                                        🎖️</p>
                                </div>
                            </label>

                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="member" class="peer hidden" {{ $currentRole === 'member' || (!$isMainLeader && !in_array($currentRole, ['leader', 'vice_leader'])) ? 'checked' : '' }} {{ $isMainLeader ? 'disabled' : '' }}>
                                <div
                                    class="p-3 rounded-xl border-2 border-gray-100 text-center peer-checked:border-[#D4AF37] peer-checked:bg-[#FFF8E1] transition-all hover:bg-gray-50 {{ $isMainLeader ? 'bg-gray-50 opacity-50 cursor-not-allowed' : '' }}">
                                    <p class="text-sm font-bold text-gray-600 peer-checked:text-[#AA8A26]">Member 👤</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Technical Team --}}
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Technical
                            Assignment</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="technical_role" value="general" class="peer hidden" checked>
                                <div
                                    class="p-2.5 rounded-xl border border-gray-200 text-center peer-checked:bg-gray-800 peer-checked:text-white transition text-xs font-bold shadow-sm peer-checked:border-gray-800">
                                    General</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="technical_role" value="software" class="peer hidden">
                                <div
                                    class="p-2.5 rounded-xl border border-gray-200 text-center peer-checked:bg-blue-600 peer-checked:text-white transition text-xs font-bold shadow-sm peer-checked:border-blue-600">
                                    Software 💻</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="technical_role" value="hardware" class="peer hidden">
                                <div
                                    class="p-2.5 rounded-xl border border-gray-200 text-center peer-checked:bg-orange-500 peer-checked:text-white transition text-xs font-bold shadow-sm peer-checked:border-orange-500">
                                    Hardware 🔌</div>
                            </label>
                        </div>
                    </div>

                    {{-- Permissions --}}
                    @php
                        // Safely decode permissions if they are a string, otherwise use empty array
                        $userPermissions = collect(is_string($user->permissions) ? json_decode($user->permissions, true) : ($user->permissions ?? []));
                        $hasViewTeamFunds = $userPermissions->contains('view_team_funds');
                    @endphp
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Advanced
                            Permissions</label>
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-4">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative flex items-center">
                                    <input type="checkbox" name="permissions[]" value="view_team_funds"
                                        class="peer sr-only" {{ $userPermissions->contains('view_team_funds') ? 'checked' : '' }}>
                                    <div
                                        class="h-5 w-5 rounded border-2 border-gray-300 bg-white peer-checked:bg-[#175C53] peer-checked:border-[#175C53] transition-all flex items-center justify-center group-hover:border-[#175C53]">
                                        <i
                                            class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-gray-800 group-hover:text-[#175C53] transition-colors">View
                                        Team Funds</span>
                                    <span class="text-[10px] text-gray-500">Allow this member to view the full team's
                                        financial contributions</span>
                                </div>
                            </label>

                            <div class="h-px bg-gray-200"></div>

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative flex items-center">
                                    <input type="checkbox" name="permissions[]" value="wallet_management"
                                        class="peer sr-only" {{ $userPermissions->contains('wallet_management') ? 'checked' : '' }}>
                                    <div
                                        class="h-5 w-5 rounded border-2 border-gray-300 bg-white peer-checked:bg-amber-600 peer-checked:border-amber-600 transition-all flex items-center justify-center group-hover:border-amber-600">
                                        <i
                                            class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-gray-800 group-hover:text-amber-600 transition-colors">Wallet
                                        Management</span>
                                    <span class="text-[10px] text-gray-500">Allow this member to view, deposit, and
                                        withdraw from the wallet</span>
                                </div>
                            </label>

                            <div class="h-px bg-gray-200"></div>

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative flex items-center">
                                    <input type="checkbox" name="permissions[]" value="deposit_requests"
                                        class="peer sr-only" {{ $userPermissions->contains('deposit_requests') ? 'checked' : '' }}>
                                    <div
                                        class="h-5 w-5 rounded border-2 border-gray-300 bg-white peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center group-hover:border-indigo-600">
                                        <i
                                            class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">Deposit
                                        Requests</span>
                                    <span class="text-[10px] text-gray-500">Allow this member to see and manage deposit
                                        requests from members</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Extra --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Extra
                            Responsibility</label>
                        <select name="extra_role"
                            class="w-full border-2 border-gray-200 rounded-xl p-3 text-sm focus:ring-[#D4AF37] focus:border-[#D4AF37] outline-none transition bg-white font-semibold text-gray-700">
                            <option value="none">None</option>
                            <option value="presentation">🎤 Presentation Master</option>
                            <option value="reports">📝 Weekly Reports</option>
                            <option value="marketing">📢 Marketing & Media</option>
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100 rounded-b-xl">
                    <button type="submit"
                        class="bg-[#D4AF37] hover:bg-[#AA8A26] text-white py-2 px-6 rounded-xl font-bold shadow-md transition transform hover:-translate-y-0.5">Save
                        Changes</button>
                    <button type="button" onclick="closeModal('manageMemberModal')"
                        class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 py-2 px-6 rounded-xl font-bold shadow-sm transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    body.modal-open-lock {
        overflow: hidden !important;
    }
</style>

<script>
    function openManageModal() {
        document.getElementById('manageMemberModal').classList.remove('hidden');
        document.body.classList.add('modal-open-lock');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.classList.remove('modal-open-lock');
    }
</script>