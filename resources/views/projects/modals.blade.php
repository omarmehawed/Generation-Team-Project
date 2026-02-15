{{-- ================================================ --}}
{{-- 🚪 منطقة المودالات (Popups) - كلها هنا 🚪 --}}
{{-- أنا حافظت على الكود بتاعك بالظبط زي ما طلبت --}}
{{-- ================================================ --}}

{{-- 1. Create Team Modal --}}
<div id="createTeamModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"
            onclick="closeModal('createTeamModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            <form action="{{ route('teams.store') }}" method="POST">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <div class="bg-white px-8 pt-8 pb-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-gradient-to-br from-[#266963] to-[#1A4D48] rounded-xl text-white shadow-lg">
                            <i class="fas fa-crown text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900">Create Team</h3>
                    </div>
                    <div class="mb-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Team
                            Name</label>
                        <input type="text" name="name" required placeholder="e.g. The Avengers"
                            class="w-full border-2 border-gray-100 bg-gray-50 p-4 rounded-xl focus:ring-0 focus:border-[#266963] focus:bg-white outline-none transition shadow-inner text-lg font-bold text-gray-800 placeholder-gray-300">
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3">
                    <button type="submit"
                        class="bg-[#266963] hover:bg-[#1e524d] text-white py-3 px-6 rounded-xl font-bold shadow-lg transition transform hover:-translate-y-0.5">Create
                        Team</button>
                    <button type="button" onclick="closeModal('createTeamModal')"
                        class="bg-white border border-gray-200 text-gray-600 py-3 px-6 rounded-xl font-bold hover:bg-gray-100 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. Join Team Modal --}}
<div id="joinTeamModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"
            onclick="closeModal('joinTeamModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-blue-100">
            <form action="{{ route('teams.join') }}" method="POST">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <div class="bg-white px-8 pt-8 pb-6 text-center">
                    <div
                        class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="fas fa-key text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Join Team</h3>
                    <p class="text-gray-400 text-sm mb-8 font-medium">Enter the 6-character code provided by your
                        leader.</p>
                    <input type="text" name="code" required placeholder="X 7 K 9 M 2"
                        class="w-full border-2 border-blue-100 p-5 rounded-2xl uppercase text-center font-mono text-4xl tracking-[0.2em] text-gray-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition placeholder-gray-200">
                </div>
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 px-6 rounded-xl font-bold shadow-lg transition transform hover:-translate-y-0.5">Join
                        Now</button>
                    <button type="button" onclick="closeModal('joinTeamModal')"
                        class="w-full bg-white border border-gray-200 text-gray-600 py-3.5 px-6 rounded-xl font-bold hover:bg-gray-100 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($myTeam)
    {{-- 3. Invite Modal --}}
    <div id="inviteMemberModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"
                onclick="closeModal('inviteMemberModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('teams.invite') }}" method="POST">
                    @csrf
                    <div class="bg-white px-8 pt-8 pb-6">
                        <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <div class="p-2 bg-purple-100 rounded-lg text-purple-600"><i
                                    class="fas fa-envelope-open-text"></i></div>
                            Invite Member
                        </h3>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Student
                            Email</label>
                        <input type="email" name="email" required placeholder="student@example.com"
                            class="w-full border border-gray-200 bg-gray-50 p-4 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:bg-white outline-none transition shadow-inner">
                    </div>
                    <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3">
                        <button type="submit"
                            class="bg-purple-600 hover:bg-purple-700 text-white py-3 px-6 rounded-xl font-bold shadow-md transition transform hover:-translate-y-0.5">Send
                            Invite</button>
                        <button type="button" onclick="closeModal('inviteMemberModal')"
                            class="bg-white border border-gray-200 text-gray-600 py-3 px-6 rounded-xl font-bold hover:bg-gray-100 transition">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 4. Leave Modal --}}
    <div id="leaveTeamModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"
                onclick="closeModal('leaveTeamModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-t-8 border-red-500">
                <form action="{{ route('teams.leave') }}" method="POST">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $myTeam->id }}">
                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="flex items-center gap-4 text-red-600 mb-6">
                            <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center"><i
                                    class="fas fa-exclamation-triangle text-xl"></i></div>
                            <h3 class="text-2xl font-black text-gray-800">Leave Team?</h3>
                        </div>

                        @if($myRole == 'leader' && $myTeam->members->count() > 1)
                            <div class="bg-red-50 p-4 rounded-xl border border-red-100 mb-4">
                                <p class="text-red-800 font-bold text-sm mb-2">⚠️ You are the leader!</p>
                                <p class="text-red-600 text-xs">You must appoint a new leader before leaving.</p>
                            </div>
                            <select name="new_leader_id"
                                class="w-full border border-gray-300 p-3 rounded-xl text-sm bg-white focus:ring-red-500 focus:border-red-500 font-bold">
                                <option value="" disabled selected>Select new leader...</option>
                                @foreach($myTeam->members as $member)
                                    @if($member->user_id != auth()->id())
                                        <option value="{{ $member->user_id }}">{{ $member->user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <p class="text-gray-600 font-medium">Are you sure you want to leave? You will lose access to all
                                tasks.</p>
                        @endif
                    </div>
                    <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3">
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-xl font-bold shadow-md transition transform hover:-translate-y-0.5">Yes,
                            Leave</button>
                        <button type="button" onclick="closeModal('leaveTeamModal')"
                            class="bg-white border border-gray-200 text-gray-600 py-3 px-6 rounded-xl font-bold hover:bg-gray-100 transition">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 5. Report Modal --}}
    <div id="reportMemberModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"
                onclick="closeModal('reportMemberModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('teams.reportMember') }}" method="POST">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $myTeam->id }}">
                    <input type="hidden" name="reported_user_id" id="reportedUserId">
                    <div class="bg-white px-8 pt-8 pb-6">
                        <h3 class="text-xl font-black text-gray-800 mb-2">Report Member</h3>
                        <div class="bg-gray-100 p-3 rounded-xl mb-4 flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-500 uppercase">Reporting:</span>
                            <span id="reportedUserName" class="text-gray-900 font-bold"></span>
                        </div>
                        <textarea name="reason" required
                            class="w-full border border-gray-200 bg-gray-50 p-4 rounded-xl focus:ring-gray-500 focus:border-gray-500 transition shadow-inner text-sm"
                            placeholder="Please describe the issue..." rows="4"></textarea>
                    </div>
                    <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3">
                        <button type="submit"
                            class="bg-gray-800 hover:bg-black text-white py-3 px-6 rounded-xl font-bold shadow-md transition transform hover:-translate-y-0.5">Submit
                            Report</button>
                        <button type="button" onclick="closeModal('reportMemberModal')"
                            class="bg-white border border-gray-200 text-gray-600 py-3 px-6 rounded-xl font-bold hover:bg-gray-100 transition">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 6. Submit Task Modal --}}
   <div id="submitTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"
            onclick="closeModal('submitTaskModal')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            
            <form id="submitTaskForm" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ type: 'link' }">
                @csrf
                
                <input type="hidden" name="submission_type" x-model="type">

                <div class="bg-[#266963] px-8 py-6 border-b border-[#1e524d]">
                    <h3 class="text-xl font-bold text-white flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                            <i class="fas fa-paper-plane text-yellow-400"></i>
                        </div>
                        Submit Task
                    </h3>
                    <p class="text-green-100 text-sm mt-2 ml-12" id="taskModalTitle">Upload your work</p>
                </div>

                <div class="p-8 space-y-6 bg-white">
                    
                    <div class="flex p-1 bg-gray-100 rounded-xl">
                        <button type="button" @click="type = 'link'"
                            :class="{ 'bg-white text-blue-600 shadow-sm': type === 'link', 'text-gray-500 hover:text-gray-700': type !== 'link' }"
                            class="flex-1 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-link"></i> External Link
                        </button>

                        <button type="button" @click="type = 'file'"
                            :class="{ 'bg-white text-[#266963] shadow-sm': type === 'file', 'text-gray-500 hover:text-gray-700': type !== 'file' }"
                            class="flex-1 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-file-upload"></i> Upload File
                        </button>
                    </div>

                    <div x-show="type === 'link'" x-transition>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Project Link (GitHub, Drive)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-globe text-gray-400"></i>
                            </div>
                            <input type="url" name="link" placeholder="https://github.com/..."
                                class="w-full pl-11 border border-gray-200 bg-gray-50 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#266963] focus:border-[#266963] transition outline-none">
                        </div>
                    </div>

                    <div x-show="type === 'file'" x-transition style="display: none;">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Upload File (PDF, ZIP, IMG)</label>
                        {{-- 👇👇👇 هنا كان التعديل المهم: غيرنا name="file" لـ name="submission_file" --}}
                        <input type="file" name="submission_file" 
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#266963]/10 file:text-[#266963] hover:file:bg-[#266963]/20 transition cursor-pointer border border-gray-200 bg-gray-50 rounded-xl p-1">
                        <p class="text-xs text-gray-400 mt-1">Max size: 100MB</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Comment (Optional)</label>
                        <textarea name="submission_comment" rows="3" 
                            class="w-full border border-gray-200 bg-gray-50 rounded-xl p-4 text-sm focus:ring-2 focus:ring-[#266963] focus:border-[#266963] transition outline-none resize-none" 
                            placeholder="Any notes for the reviewer?"></textarea>
                    </div>

                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button type="submit"
                        class="bg-[#266963] hover:bg-[#1e524d] text-white py-2.5 px-6 rounded-xl font-bold shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <span>Submit</span> <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="button" onclick="closeModal('submitTaskModal')"
                        class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 py-2.5 px-6 rounded-xl font-bold transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif