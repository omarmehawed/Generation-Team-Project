{{-- ================= UNIFIED USER MODAL (Premium Design) ================= --}}
<div id="userModal" class="fixed inset-0 z-50 hidden">
    {{-- Backdrop with Blur --}}
    <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-md transition-opacity" onclick="closeUserModal()"></div>

    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">

        <div
            class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl border border-gray-100 flex flex-col max-h-[90vh] overflow-hidden transform transition-all">

            {{-- 1. Premium Header --}}
            <div class="bg-white px-8 py-6 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                <div>
                    <h3 class="text-2xl font-black text-gray-800 tracking-tight" id="modalTitle">User Profile</h3>
                    <p class="text-sm text-gray-400 font-medium mt-1">Manage account details, roles, and permissions.
                    </p>
                </div>
                <button onclick="closeUserModal()"
                    class="w-10 h-10 rounded-full bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            {{-- Form Wrapper --}}
            <form id="userForm" method="POST" class="flex flex-col flex-1 overflow-hidden bg-[#FAFBFC]">
                @csrf
                <div id="methodField"></div>

                {{-- 2. Scrollable Body --}}
                <div class="p-8 space-y-8 overflow-y-auto custom-scrollbar flex-1">

                    {{-- Section: Account Info --}}
                    <div>
                        <h4
                            class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Account Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Name --}}
                            <div class="group">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Full
                                    Name</label>
                                <input type="text" name="name" id="name" required
                                    class="w-full px-5 py-3.5 rounded-xl bg-white border-2 border-gray-100 text-gray-700 font-bold focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none placeholder-gray-300"
                                    placeholder="e.g. Omar Mehawed">
                            </div>
                            {{-- Email --}}
                            <div class="group">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Email
                                    Address</label>
                                <input type="email" name="email" id="email" required
                                    class="w-full px-5 py-3.5 rounded-xl bg-white border-2 border-gray-100 text-gray-700 font-bold focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none placeholder-gray-300"
                                    placeholder="name@batechu.com">
                            </div>

                            {{-- Role Selector (Custom UI) --}}
                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">System
                                    Role</label>
                                <div class="relative">
                                    <select name="role" id="role" onchange="toggleFields()"
                                        class="w-full px-5 py-3.5 rounded-xl bg-white border-2 border-gray-100 text-gray-700 font-bold focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none appearance-none cursor-pointer">
                                        <option value="student">🎓 Student</option>
                                        <option value="doctor">👨‍🏫 Doctor</option>
                                        <option value="ta">🧑‍💻 Teaching Assistant (TA)</option>
                                        <option value="admin">🛡️ Administrator</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="group">
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Password</label>
                                <input type="password" name="password"
                                    class="w-full px-5 py-3.5 rounded-xl bg-white border-2 border-gray-100 text-gray-700 font-bold focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none placeholder-gray-300"
                                    placeholder="•••••••• (Leave empty to keep)">
                            </div>
                        </div>
                    </div>

                    {{-- 🎓 Section: Student Details --}}
                    <div id="studentFields" class="contents">
                        <div class="border-t border-gray-200 pt-6">
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span> Student Academics
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Academic
                                        Year</label>
                                    <div class="relative">
                                        <select name="academic_year" id="academic_year"
                                            class="w-full px-5 py-3.5 rounded-xl bg-white border-2 border-gray-100 text-gray-700 font-bold focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none appearance-none cursor-pointer">
                                            <option value="1">Year 1</option>
                                            <option value="2">Year 2</option>
                                            <option value="3">Year 3</option>
                                            <option value="4">Year 4</option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Department</label>
                                    <div class="relative">
                                        <select name="department" id="department"
                                            class="w-full px-5 py-3.5 rounded-xl bg-white border-2 border-gray-100 text-gray-700 font-bold focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none appearance-none cursor-pointer">
                                            <option value="general">General</option>
                                            <option value="software">Software Engineering</option>
                                            <option value="network">Network Security</option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- National ID --}}
                                <div class="col-span-1 md:col-span-2 group">
                                    <label
                                        class="text-xs font-bold text-gray-500 uppercase mb-2 ml-1 flex items-center gap-1">
                                        National ID <span class="text-gray-400 text-[10px] lowercase font-medium">(الرقم
                                            القومي)</span>
                                    </label>
                                    <input type="text" name="national_id" id="national_id" maxlength="14"
                                        pattern="\d{14}"
                                        class="w-full px-5 py-3.5 rounded-xl bg-white border-2 border-gray-100 text-gray-700 font-bold focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all outline-none placeholder-gray-300"
                                        placeholder="Enter exactly 14 digits">
                                    <p class="text-[10px] text-gray-400 font-medium mt-1 ml-1">Must be exactly 14 digits
                                        and unique to each student.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 🔐 Section: Staff Permissions & Courses --}}
                    <div id="staffFields" class="hidden space-y-8">

                        {{-- Interactive Permissions Cards --}}
                        <div class="border-t border-gray-200 pt-6">
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span> Access Control
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- Card Item --}}
                                <label class="cursor-pointer group relative">
                                    <input type="checkbox" name="permissions[]" value="view_proposals"
                                        class="peer sr-only">
                                    <div
                                        class="p-4 rounded-xl bg-white border-2 border-gray-100 peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:shadow-md transition-all duration-200 flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center peer-checked:bg-purple-500 peer-checked:text-white transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        <div>
                                            <span class="block font-bold text-gray-700 peer-checked:text-purple-700">
                                                Proposals Review</span>
                                            <span class="text-[10px] text-gray-400 font-medium">Read-only access to
                                                projects</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="cursor-pointer group relative">
                                    <input type="checkbox" name="permissions[]" value="manage_teams"
                                        class="peer sr-only">
                                    <div
                                        class="p-4 rounded-xl bg-white border-2 border-gray-100 peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:shadow-md transition-all duration-200 flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center peer-checked:bg-purple-500 peer-checked:text-white transition-colors">
                                            <i class="fas fa-users-cog"></i>
                                        </div>
                                        <div>
                                            <span class="block font-bold text-gray-700 peer-checked:text-purple-700">My
                                                Teams</span>
                                            <span class="text-[10px] text-gray-400 font-medium">Create & edit student
                                                teams</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="cursor-pointer group relative">
                                    <input type="checkbox" name="permissions[]" value="manage_subjects"
                                        class="peer sr-only">
                                    <div
                                        class="p-4 rounded-xl bg-white border-2 border-gray-100 peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:shadow-md transition-all duration-200 flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center peer-checked:bg-purple-500 peer-checked:text-white transition-colors">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <div>
                                            <span
                                                class="block font-bold text-gray-700 peer-checked:text-purple-700">Subject
                                                Projects</span>
                                            <span class="text-[10px] text-gray-400 font-medium">Control course
                                                projects</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="cursor-pointer group relative">
                                    <input type="checkbox" name="permissions[]" value="view_defense"
                                        class="peer sr-only">
                                    <div
                                        class="p-4 rounded-xl bg-white border-2 border-gray-100 peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:shadow-md transition-all duration-200 flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center peer-checked:bg-purple-500 peer-checked:text-white transition-colors">
                                            <i class="fas fa-gavel"></i>
                                        </div>
                                        <div>
                                            <span
                                                class="block font-bold text-gray-700 peer-checked:text-purple-700">View
                                                Defense</span>
                                            <span class="text-[10px] text-gray-400 font-medium">Access defense
                                                schedules</span>
                                        </div>
                                    </div>
                                </label>

                                {{-- New Permission: Teams Database --}}
                                <label class="cursor-pointer group relative">
                                    <input type="checkbox" name="permissions[]" value="manage_teams_db"
                                        class="peer sr-only">
                                    <div
                                        class="p-4 rounded-xl bg-gray-50 border-2 border-gray-200 peer-checked:border-[#175c53] peer-checked:bg-[#175c53] hover:shadow-md transition-all duration-200 flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gray-200 text-gray-600 flex items-center justify-center peer-checked:bg-white/20 peer-checked:text-white transition-colors">
                                            <i class="fas fa-network-wired"></i>
                                        </div>
                                        <div>
                                            <span class="block font-bold text-gray-600 peer-checked:text-white">Teams
                                                Database</span>
                                            <span
                                                class="text-[10px] text-gray-400 font-medium peer-checked:text-gray-200">Manage
                                                All Teams</span>
                                        </div>
                                    </div>
                                </label>
                                {{-- ================= 🔥 USER MANAGEMENT 🔥 ================= --}}
                                <div class="col-span-1 sm:col-span-2 border-2 border-blue-100 rounded-xl bg-white overflow-hidden transition-all duration-300"
                                    id="userMgmtContainer">

                                    {{-- Header (Parent Control) --}}
                                    <div class="flex items-center justify-between p-4 bg-blue-50 cursor-pointer hover:bg-blue-100 transition-colors"
                                        onclick="toggleUserMgmtDropdown()">

                                        <div class="flex items-center gap-3">
                                            <div id="userMgmtArrow"
                                                class="text-blue-500 transition-transform duration-300 transform -rotate-90">
                                                <i class="fas fa-chevron-down"></i>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shadow-sm">
                                                    <i class="fas fa-user-shield"></i>
                                                </div>
                                                <div>
                                                    <span class="block font-bold text-gray-800 text-sm">User
                                                        Management</span>
                                                    <span class="text-[10px] text-gray-500 font-medium">Controls Users
                                                        & Logs Access</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Master Checkbox --}}
                                        <div onclick="event.stopPropagation()">
                                            <input type="checkbox" id="perm_parent_user_mgmt"
                                                onchange="toggleAllUserMgmt(this)"
                                                class="w-5 h-5 border-2 border-blue-300 rounded text-blue-600 focus:ring-blue-500 cursor-pointer">
                                        </div>
                                    </div>

                                    {{-- Dropdown Body (Children) --}}
                                    <div id="userMgmtBody"
                                        class="hidden border-t border-blue-100 bg-white p-3 space-y-2">

                                        {{-- Child A: Manage Users (List) --}}
                                        <label
                                            class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-100 transition">
                                            <div
                                                class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs">
                                                <i class="fas fa-list"></i>
                                            </div>
                                            <input type="checkbox" name="permissions[]" value="manage_users"
                                                class="perm-child-user-mgmt rounded text-blue-600 focus:ring-blue-500 w-4 h-4"
                                                onchange="updateUserMgmtParentState()">
                                            <span class="text-xs font-bold text-gray-700">All Users List</span>
                                        </label>

                                        {{-- Child B: Activity Logs --}}
                                        <label
                                            class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-100 transition">
                                            <div
                                                class="w-6 h-6 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center text-xs">
                                                <i class="fas fa-history"></i>
                                            </div>
                                            <input type="checkbox" name="permissions[]" value="view_activity_log"
                                                class="perm-child-user-mgmt rounded text-blue-600 focus:ring-blue-500 w-4 h-4"
                                                onchange="updateUserMgmtParentState()">
                                            <span class="text-xs font-bold text-gray-700">Activity Logs</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- New Permission: Academic Control (System Control) --}}
                                @if (Auth::user()->hasPermission('manage_academic_control'))
                                    <label class="cursor-pointer group relative">
                                        <input type="checkbox" name="permissions[]" value="manage_academic_control"
                                            class="peer sr-only">
                                        <div
                                            class="p-4 rounded-xl bg-gray-50 border-2 border-gray-200 peer-checked:border-gray-800 peer-checked:bg-gray-800 hover:shadow-md transition-all duration-200 flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-gray-200 text-gray-600 flex items-center justify-center peer-checked:bg-gray-600 peer-checked:text-white transition-colors">
                                                <i class="fas fa-cogs"></i>
                                            </div>
                                            <div>
                                                <span class="block font-bold text-gray-600 peer-checked:text-white">Academic
                                                    Control</span>
                                                <span
                                                    class="text-[10px] text-gray-400 font-medium peer-checked:text-gray-300">Change
                                                    Term & Promote</span>
                                            </div>
                                        </div>
                                    </label>
                                @endif
                                {{-- DataBase --}}
                                @if (Auth::user()->hasPermission('backup_db'))
                                    <label class="cursor-pointer group relative">
                                        <input type="checkbox" name="permissions[]" value="backup_db" class="peer sr-only">
                                        <div
                                            class="p-4 rounded-xl bg-red-50 border-2 border-red-100 peer-checked:border-red-500 peer-checked:bg-red-50 hover:shadow-md transition-all duration-200 flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-red-200 text-red-600 flex items-center justify-center peer-checked:bg-red-600 peer-checked:text-white transition-colors">
                                                <i class="fas fa-database"></i>
                                            </div>
                                            <div>
                                                <span
                                                    class="block font-bold text-red-800 peer-checked:text-red-900">Database
                                                    Backup ⚠️</span>
                                                <span class="text-[10px] text-red-400 font-medium">Sensitive
                                                    Permission</span>
                                            </div>
                                        </div>
                                    </label>
                                @endif
                            </div>
                        </div>

                        {{-- Interactive Courses Cards --}}
                        <div class="border-t border-gray-200 pt-6">
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Assigned Courses
                            </h4>
                            <div
                                class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                @foreach ($courses as $course)
                                    <label class="cursor-pointer group relative">
                                        <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                            id="course_{{ $course->id }}" class="peer sr-only">
                                        <div
                                            class="p-3 rounded-xl bg-white border-2 border-gray-100 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-200 transition-all duration-200">
                                            <div class="flex justify-between items-start">
                                                <span
                                                    class="text-[10px] font-black text-gray-400 uppercase tracking-wider peer-checked:text-indigo-400">{{ $course->code }}</span>
                                                <div
                                                    class="w-4 h-4 rounded-full border border-gray-300 peer-checked:bg-indigo-500 peer-checked:border-indigo-500 flex items-center justify-center">
                                                    <svg class="w-2 h-2 text-white hidden peer-checked:block" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="4" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <h5
                                                class="text-xs font-bold text-gray-700 mt-1 peer-checked:text-indigo-800 truncate">
                                                {{ $course->name }}
                                            </h5>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Footer (Fixed) --}}
                <div
                    class="px-8 py-5 border-t border-gray-100 bg-white flex justify-end gap-4 flex-shrink-0 rounded-b-3xl">
                    <button type="button" onclick="closeUserModal()"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-8 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-lg shadow-blue-500/30 transform active:scale-95 transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



{{-- Import Modal --}}
{{-- ================= IMPORT MODAL ================= --}}
<div id="importModal"
    class="hidden fixed inset-0 z-50 {{'flex'}} items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
    <div class="bg-white p-8 rounded-2xl w-full max-w-md shadow-2xl border border-gray-100 text-center">

        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
        </div>

        <h3 class="font-extrabold text-2xl text-gray-900 mb-2">Import CSV File</h3>

        <p class="text-sm text-gray-500 mb-4">Bulk create users by uploading a CSV file.</p>

        {{-- 👇 زرار تحميل القالب (الجديد) --}}
        <div class="mb-6">
            <a href="{{ route('admin.users.import.sample') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-200 hover:bg-green-100 transition">
                <i class="fas fa-file-csv text-lg"></i>
                Download Sample Template
            </a>
        </div>

        <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
                <input type="file" name="file"
                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-200 rounded-lg p-1"
                    required>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                    class="flex-1 bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-bold hover:bg-gray-200 transition">Cancel</button>
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white px-4 py-3 rounded-xl font-bold shadow-lg hover:bg-blue-700 transition">Upload
                    Users</button>
            </div>
        </form>
    </div>
</div>




{{-- Delete Modal --}}
<div id="deleteModal"
    class="hidden fixed inset-0 z-50 {{'flex'}} items-center justify-center bg-gray-900/60 backdrop-blur-sm">
    <div class="bg-white p-6 rounded-xl w-full max-w-sm text-center shadow-2xl border border-gray-100">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Delete User?</h3>
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete <span id="delete_user_name"
                class="font-bold text-gray-900"></span>?</p>
        <div class="flex gap-3">
            <button onclick="document.getElementById('deleteModal').classList.add('hidden')"
                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-bold transition">Cancel</button>
            <form id="deleteForm" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold shadow transition">Delete</button>
            </form>
        </div>
    </div>
</div>

{{-- Bulk Edit Modal (Fixed Scroll Issue) --}}
<div id="bulkEditModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeBulkEditModal()"></div>

    {{-- هنا التعديل: ضيفنا flex لتوسيط المودال وحجم أقصى --}}
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">

        {{-- هنا التعديل: flex-col و max-h-[90vh] عشان المودال ميخرجش بره الشاشة --}}
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl border border-gray-100 flex flex-col max-h-[90vh]">

            {{-- 1. Header (Fixed) --}}
            <div
                class="bg-gradient-to-r from-gray-900 to-gray-800 px-6 py-4 border-b flex justify-between items-center text-white flex-shrink-0 rounded-t-2xl">
                <h3 class="text-lg font-bold" id="modalTitleBulk">⚡ Bulk Edit Users</h3>
                <button onclick="closeBulkEditModal()" class="text-gray-400 hover:text-white transition">✕</button>
            </div>

            {{-- Form Wrapper --}}
            <form action="{{ route('admin.users.bulk_update') }}" method="POST"
                class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="selected_ids" id="bulkSelectedIds">

                {{-- 2. Scrollable Body --}}
                <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar flex-1">

                    {{-- Note --}}
                    <div class="bg-yellow-50 text-yellow-800 p-3 rounded-lg text-sm border border-yellow-200">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Note:</strong> Changes will be applied to <span id="modalSelectedCount"
                            class="font-bold">0</span> users.
                    </div>

                    {{-- Selected Users List --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Selected
                            Users</label>
                        <div id="bulkListContainer"
                            class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                            {{-- JS will populate this --}}
                        </div>
                    </div>

                    {{-- ================= 🔥 A. STUDENT FIELDS (New) 🔥 ================= --}}
                    {{-- القسم ده خاص بالطلبة فقط (يظهر ويختفي بالـ JS) --}}
                    <div id="bulkStudentFields" class="hidden space-y-4 border-t pt-4">
                        <h4 class="text-sm font-bold text-gray-700 uppercase">Update Academic Info</h4>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Academic Year --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Academic
                                    Year</label>
                                <select name="academic_year"
                                    class="w-full px-4 py-2 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 font-bold focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all outline-none cursor-pointer">
                                    <option value="">Don't Change</option>
                                    <option value="1">Year 1</option>
                                    <option value="2">Year 2</option>
                                    <option value="3">Year 3</option>
                                    <option value="4">Year 4</option>
                                </select>
                            </div>

                            {{-- Department --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Department</label>
                                <select name="department"
                                    class="w-full px-4 py-2 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 font-bold focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all outline-none cursor-pointer">
                                    <option value="">Don't Change</option>
                                    <option value="general">General</option>
                                    <option value="software">Software Engineering</option>
                                    <option value="network">Network Security</option>
                                </select>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">* Courses will be auto-assigned based on new
                            year/department.</p>
                    </div>

                    {{-- ================= 🔥 B. STAFF FIELDS (Permissions) 🔥 ================= --}}
                    {{-- القسم ده خاص بالستاف فقط (يظهر ويختفي بالـ JS) --}}
                    <div id="bulkStaffPermissions">
                        <h4 class="text-sm font-bold text-gray-700 uppercase mb-3">Update Permissions (Staff Only)</h4>

                        {{-- Grid Container --}}
                        <div class="grid grid-cols-2 gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200">

                            {{-- 1. Regular Permissions --}}
                            <label
                                class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-1 rounded transition">
                                <input type="checkbox" name="permissions[]" value="view_proposals"
                                    class="rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">View Proposals</span>
                            </label>

                            <label
                                class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-1 rounded transition">
                                <input type="checkbox" name="permissions[]" value="manage_teams"
                                    class="rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Manage Teams</span>
                            </label>

                            <label
                                class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-1 rounded transition">
                                <input type="checkbox" name="permissions[]" value="manage_subjects"
                                    class="rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Subject Projects</span>
                            </label>

                            <label
                                class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-1 rounded transition">
                                <input type="checkbox" name="permissions[]" value="view_defense"
                                    class="rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Time table Defence</span>
                            </label>
                            {{-- New Permission: Teams Database (Bulk) --}}
                            <label
                                class="col-span-2 flex items-center gap-2 bg-gray-100 p-2 rounded border border-gray-200 hover:bg-gray-200 transition cursor-pointer mt-1">
                                <input type="checkbox" name="permissions[]" value="manage_teams_db"
                                    class="rounded text-gray-800 focus:ring-[#175c53]">
                                <span class="text-sm text-gray-800 font-bold flex items-center gap-2">
                                    <i class="fas fa-network-wired text-xs"></i> Teams Database Access
                                </span>
                            </label>

                            {{-- ================= 🔥 BULK USER MANAGEMENT DROPDOWN 🔥 ================= --}}
                            <div class="col-span-2 mt-2 border border-blue-200 rounded-lg bg-white overflow-hidden"
                                id="bulkUserMgmtContainer">

                                {{-- Header --}}
                                <div class="flex items-center justify-between p-3 bg-blue-50 cursor-pointer hover:bg-blue-100 transition-colors"
                                    onclick="toggleBulkUserMgmtDropdown()">
                                    <div class="flex items-center gap-2">
                                        <div id="bulkUserMgmtArrow"
                                            class="text-blue-500 transition-transform duration-300 transform -rotate-90">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                        <span class="text-sm font-bold text-blue-900">User Management System</span>
                                    </div>

                                    {{-- Master Checkbox --}}
                                    <div onclick="event.stopPropagation()">
                                        <input type="checkbox" id="bulk_perm_parent_user_mgmt"
                                            onchange="toggleAllBulkUserMgmt(this)"
                                            class="w-4 h-4 border-2 border-blue-400 rounded text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </div>
                                </div>

                                {{-- Body --}}
                                <div id="bulkUserMgmtBody"
                                    class="hidden border-t border-blue-100 p-2 space-y-1 bg-white">
                                    <label class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="manage_users"
                                            class="bulk-perm-child rounded text-blue-600 focus:ring-blue-500 w-4 h-4"
                                            onchange="updateBulkUserMgmtParentState()">
                                        <span class="text-xs font-bold text-gray-700">All Users List</span>
                                    </label>

                                    <label class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="view_activity_log"
                                            class="bulk-perm-child rounded text-blue-600 focus:ring-blue-500 w-4 h-4"
                                            onchange="updateBulkUserMgmtParentState()">
                                        <span class="text-xs font-bold text-gray-700">Activity Logs</span>
                                    </label>
                                </div>
                            </div>
                            {{-- Academic Control (Bulk Edit) --}}
                            @if (Auth::user()->hasPermission('manage_academic_control'))
                                <label
                                    class="col-span-2 flex items-center gap-2 bg-gray-100 p-2 rounded border border-gray-200 hover:bg-gray-200 transition cursor-pointer mt-1">
                                    <input type="checkbox" name="permissions[]" value="manage_academic_control"
                                        class="rounded text-gray-800 focus:ring-gray-600">
                                    <span class="text-sm text-gray-800 font-bold flex items-center gap-2">
                                        <i class="fas fa-cogs text-xs"></i> Academic Control (Term & Promote)
                                    </span>
                                </label>
                            @endif
                            {{-- ==================================================================== --}}

                            @if (Auth::user()->hasPermission('backup_db'))
                                <label
                                    class="col-span-2 flex items-center gap-2 bg-red-50 p-2 rounded border border-red-100 hover:bg-red-100 transition cursor-pointer mt-2">
                                    <input type="checkbox" name="permissions[]" value="backup_db"
                                        class="rounded text-red-600 focus:ring-red-500">
                                    <span class="text-sm text-red-700 font-bold">Backup DB ⚠️</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    {{-- ================= 🔥 C. MANUAL COURSES (Staff Only) 🔥 ================= --}}
                    <div id="bulkCoursesWrapper">
                        <h4 class="text-sm font-bold text-gray-700 uppercase mb-3">Assign Courses (Staff Only)</h4>
                        <div
                            class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 max-h-40 overflow-y-auto custom-scrollbar">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach ($courses as $course)
                                    <label
                                        class="flex items-center gap-2 p-2 bg-white rounded border border-indigo-100 hover:border-indigo-300 transition cursor-pointer">
                                        <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                            class="rounded text-indigo-600 focus:ring-indigo-500">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-gray-700">{{ $course->code }}</span>
                                            <span class="text-[10px] text-gray-500 truncate w-20">{{ $course->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Footer (Fixed) --}}
                <div class="flex justify-end gap-3 p-4 border-t bg-gray-50 flex-shrink-0 rounded-b-2xl">
                    <button type="button" onclick="closeBulkEditModal()"
                        class="px-5 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-100 transition">Cancel</button>
                    <button type="submit"
                        class="px-6 py-2 bg-gray-900 text-white rounded-lg font-bold shadow-lg hover:bg-black transition transform active:scale-95">Apply
                        Bulk Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- 🚫 Custom Error Modal (Mixing Roles) --}}
<div id="errorModal" class="fixed inset-0 z-[60] hidden">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-red-100 transform transition-all scale-100 p-6 text-center">

            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>

            <h3 class="text-xl font-bold text-gray-900 mb-2">Action Denied</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                You cannot select <span class="font-bold text-gray-800">Students</span> and <span
                    class="font-bold text-gray-800">Staff</span> at the same time because their settings are
                different.
                <br><br>
                Please select only <span class="text-blue-600 font-bold">Students</span> OR only <span
                    class="text-blue-600 font-bold">Staff</span>.
            </p>

            <button onclick="document.getElementById('errorModal').classList.add('hidden')"
                class="w-full py-3 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition shadow-lg">
                Understood, I'll fix it
            </button>
        </div>
    </div>
</div>
{{-- 🗑️ Bulk Delete Confirmation Modal --}}
<div id="bulkDeleteModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
        onclick="document.getElementById('bulkDeleteModal').classList.add('hidden')"></div>
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-red-100 overflow-hidden transform transition-all scale-100">

            {{-- Header --}}
            <div class="bg-red-50 p-4 border-b border-red-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                    <i class="fas fa-trash-alt text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Move to Trash?</h3>
                    <p class="text-xs text-red-600 font-medium">These users will be deactivated.</p>
                </div>
            </div>

            <form action="{{ route('admin.users.bulk_delete') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="selected_ids" id="bulkDeleteInput">

                {{-- List of Users to Delete --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Users to be
                        deleted:</label>
                    <div id="deleteUsersList" class="flex flex-col gap-2 max-h-40 overflow-y-auto custom-scrollbar p-1">
                        {{-- الأسماء هتتحط هنا بالجافاسكريبت --}}
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('bulkDeleteModal').classList.add('hidden')"
                        class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-200">
                        Yes, Move to Trash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 💀 Bulk Force Delete Modal (Danger Zone) --}}
<div id="bulkForceDeleteModal" class="fixed inset-0 z-50 hidden">
    {{-- الخلفية الغامقة --}}
    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="closeBulkForceDeleteModal()">
    </div>

    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-md border-2 border-red-100 overflow-hidden transform transition-all scale-100">

            {{-- Header --}}
            <div class="bg-red-50 p-6 flex flex-col items-center text-center border-b border-red-100">
                <div
                    class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-4 animate-bounce">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900">PERMANENT DELETE</h3>
                <p class="text-sm text-gray-500 mt-2">
                    You are about to delete <span id="forceDeleteCount" class="font-bold text-red-600">0</span> users
                    <b>FOREVER</b>.
                </p>
                <p class="text-xs text-red-500 font-bold mt-1 uppercase tracking-widest bg-red-100 px-2 py-1 rounded">
                    This action cannot be undone!
                </p>
            </div>

            {{-- Footer & Actions --}}
            <div class="p-6">
                <form id="bulkForceDeleteForm" action="{{ route('admin.users.bulk_force_delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" id="forceDeleteIds">

                    <div class="flex gap-3">
                        <button type="button" onclick="closeBulkForceDeleteModal()"
                            class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold shadow-lg shadow-red-500/30 hover:bg-red-700 hover:shadow-red-600/40 transition transform active:scale-95">
                            Yes, Delete Forever
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    // 1. فتح وقفل القائمة
    function toggleUserMgmtDropdown() {
        const body = document.getElementById('userMgmtBody');
        const arrow = document.getElementById('userMgmtArrow');

        if (body.classList.contains('hidden')) {
            body.classList.remove('hidden');
            arrow.classList.remove('-rotate-90');
        } else {
            body.classList.add('hidden');
            arrow.classList.add('-rotate-90');
        }
    }

    // 2. التحكم الأبوي (تحديد الكل)
    function toggleAllUserMgmt(parentCheckbox) {
        const children = document.querySelectorAll('.perm-child-user-mgmt');
        children.forEach(child => {
            child.checked = parentCheckbox.checked;
        });

        // افتح القائمة لو اختارهم عشان يشوف إيه اللي حصل
        if (parentCheckbox.checked) {
            document.getElementById('userMgmtBody').classList.remove('hidden');
            document.getElementById('userMgmtArrow').classList.remove('-rotate-90');
        }
    }

    // 3. التحكم الفرعي (تحديث حالة الأب)
    function updateUserMgmtParentState() {
        const parent = document.getElementById('perm_parent_user_mgmt');
        const children = document.querySelectorAll('.perm-child-user-mgmt');
        const checkedChildren = document.querySelectorAll('.perm-child-user-mgmt:checked');

        // لو كلهم مختارين -> علم على الأب
        if (checkedChildren.length === children.length && children.length > 0) {
            parent.checked = true;
            parent.indeterminate = false;
        } else {
            parent.checked = false;
            // اختياري: لو عايز تظهر شرطة لو مختار جزء منهم
            // parent.indeterminate = (checkedChildren.length > 0);
        }
    }

    // === BULK Modal Logic ===

    // 1. فتح وقفل القائمة في الـ Bulk Modal
    function toggleBulkUserMgmtDropdown() {
        const body = document.getElementById('bulkUserMgmtBody');
        const arrow = document.getElementById('bulkUserMgmtArrow');

        if (body.classList.contains('hidden')) {
            body.classList.remove('hidden');
            arrow.classList.remove('-rotate-90');
        } else {
            body.classList.add('hidden');
            arrow.classList.add('-rotate-90');
        }
    }

    // 2. تحديد الكل (للأب في الـ Bulk)
    function toggleAllBulkUserMgmt(parentCheckbox) {
        const children = document.querySelectorAll('.bulk-perm-child');
        children.forEach(child => {
            child.checked = parentCheckbox.checked;
        });

        if (parentCheckbox.checked) {
            document.getElementById('bulkUserMgmtBody').classList.remove('hidden');
            document.getElementById('bulkUserMgmtArrow').classList.remove('-rotate-90');
        }
    }

    // 3. تحديث حالة الأب (للأبناء في الـ Bulk)
    function updateBulkUserMgmtParentState() {
        const parent = document.getElementById('bulk_perm_parent_user_mgmt');
        const children = document.querySelectorAll('.bulk-perm-child');
        const checkedChildren = document.querySelectorAll('.bulk-perm-child:checked');

        if (checkedChildren.length === children.length && children.length > 0) {
            parent.checked = true;
            parent.indeterminate = false;
        } else {
            parent.checked = false;
        }
    }
</script>