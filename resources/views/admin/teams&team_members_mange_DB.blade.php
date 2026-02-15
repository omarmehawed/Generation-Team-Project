@extends('layouts.staff')

@section('content')
    <div class="min-h-screen bg-[#F3F4F6] p-6 font-sans relative overflow-hidden">
        {{-- Background Decoration --}}
        <div
            class="absolute top-0 left-0 w-full h-64 bg-gradient-to-r from-[#175c53] to-[#1e7e72] shadow-lg -z-10 rounded-b-[3rem]">
        </div>

        <div class="max-w-7xl mx-auto space-y-8 mt-4">

            {{-- 🌟 Header Section --}}
            <div class="flex flex-col md:flex-row justify-between items-end gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-black flex items-center gap-3">
                        <span class="bg-white/20 p-2 rounded-xl backdrop-blur-md border border-white/10">🚀</span>
                        Teams Database Management
                    </h1>
                    <p class="mt-2 text-black-100 text-sm font-medium">Monitor all teams, filter by project type, and manage
                        members.</p>
                </div>
            </div>

            {{-- 🎛️ Control Panel (Filters) --}}
            <div class="bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-lg border border-gray-200 sticky top-4 z-30">
                <form action="{{ route('admin.teams.index') }}" method="GET"
                    class="flex flex-col xl:flex-row gap-4 items-end">

                    {{-- 🔍 Search Bar --}}
                    <div class="w-full xl:flex-1">
                        <label class="text-xs font-bold text-gray-500 uppercase ml-1">Search</label>
                        <div class="relative mt-1">
                            <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Team Name, Leader, or Member Name..."
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border-transparent rounded-xl focus:ring-2 focus:ring-[#175c53] focus:bg-white transition outline-none font-bold text-gray-700">
                        </div>
                    </div>

                    {{-- 🎓 Project Type Filter --}}
                    <div class="w-full md:w-48">
                        <label class="text-xs font-bold text-gray-500 uppercase ml-1">Project Type</label>
                        <select name="type" onchange="this.form.submit()"
                            class="w-full mt-1 py-3 px-4 bg-gray-50 border-transparent rounded-xl cursor-pointer font-bold text-gray-600 focus:ring-2 focus:ring-[#175c53] hover:bg-gray-100 transition">
                            <option value="">📂 All Types</option>
                            <option value="graduation" {{ request('type') == 'graduation' ? 'selected' : '' }}>🎓 Graduation
                                Projects</option>
                            <option value="subject" {{ request('type') == 'subject' ? 'selected' : '' }}>📚 Subject Projects
                            </option>
                        </select>
                    </div>

                    {{-- 📅 Date Range Filter --}}
                    <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
                        <div>
                            <label class="text-[9px] font-bold text-gray-400 uppercase block pl-1">From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="bg-transparent border-none text-xs font-bold text-gray-700 focus:ring-0 p-1">
                        </div>
                        <div class="h-8 w-px bg-gray-300"></div>
                        <div>
                            <label class="text-[9px] font-bold text-gray-400 uppercase block pl-1">To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="bg-transparent border-none text-xs font-bold text-gray-700 focus:ring-0 p-1">
                        </div>
                    </div>

                    {{-- Submit & Reset Buttons --}}
                    <div class="flex gap-2">
                        <button type="submit"
                            class="py-3 px-4 bg-[#175c53] text-white rounded-xl hover:bg-[#124a42] font-bold shadow-lg shadow-green-900/20 transition"
                            title="Apply Filters">
                            <i class="fas fa-filter"></i>
                        </button>

                        @if (request()->anyFilled(['search', 'type', 'date_from', 'date_to']))
                            <a href="{{ route('admin.teams.index') }}"
                                class="py-3 px-4 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 font-bold transition"
                                title="Reset">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- 📋 Teams Table --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-wider">Team Name
                                </th>
                                {{-- 🔥 العمود الجديد: Subject / Type --}}
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-wider">Subject /
                                    Context</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-wider">Leader</th>
                                <th class="px-6 py-5 text-center text-xs font-black text-gray-400 uppercase tracking-wider">
                                    Members</th>
                                <th class="px-6 py-5 text-center text-xs font-black text-gray-400 uppercase tracking-wider">
                                    Created At</th>
                                <th class="px-6 py-5 text-right text-xs font-black text-gray-400 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($teams as $team)
                                <tr class="hover:bg-gray-50 transition-colors group">

                                    {{-- 1. Team Name & Code --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-800 text-base">{{ $team->name }}</span>
                                            <span
                                                class="text-xs text-gray-400 font-mono bg-gray-100 px-2 py-0.5 rounded w-fit mt-1">#{{ $team->code }}</span>
                                        </div>
                                    </td>

                                    {{-- 2. Subject / Type Logic --}}
                                    <td class="px-6 py-4">
                                        @if ($team->ta_id)
                                            {{-- 🎓 Graduation Project --}}
                                            <div class="flex flex-col">
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100 w-fit">
                                                    <i class="fas fa-graduation-cap"></i> Graduation
                                                </span>

                                                @if ($team->proposal_title)
                                                    <span
                                                        class="font-bold text-gray-800 text-sm mt-2 flex items-center gap-2"
                                                        title="Project Title">
                                                        <i class="fas fa-lightbulb text-yellow-500 text-xs"></i>
                                                        {{ Str::limit($team->proposal_title, 30) }}
                                                    </span>
                                                @endif

                                                <span class="text-[10px] text-gray-400 mt-1 pl-1">
                                                    <i class="fas fa-user-tie text-[9px]"></i> TA:
                                                    {{ $team->ta->name ?? 'Unknown' }}
                                                </span>
                                            </div>
                                        @else
                                            {{-- 📚 Subject Project --}}
                                            <div class="flex flex-col">
                                                {{-- البادج --}}
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 w-fit">
                                                    <i class="fas fa-book-reader"></i> Subject Project
                                                </span>
                                                {{-- اسم المادة --}}
                                                @if ($team->project && $team->project->course)
                                                    <span
                                                        class="font-bold text-[#175c53] text-sm mt-2 flex items-center gap-2">
                                                        <i
                                                            class="{{ $team->project->course->icon_class ?? 'fas fa-book' }}"></i>
                                                        {{ $team->project->course->name }}
                                                    </span>
                                                    {{-- كود المادة --}}
                                                    <span class="text-[10px] text-gray-400 font-mono mt-0.5 pl-6">
                                                        {{ $team->project->course->code ?? '' }}
                                                    </span>
                                                @else
                                                    <span class="text-xs text-gray-400 italic mt-2">No Course Linked</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 3. Leader Info --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-gradient-to-tr from-gray-200 to-gray-300 flex items-center justify-center text-gray-700 font-bold shadow-sm">
                                                {{ substr($team->leader->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-bold text-gray-800">{{ $team->leader->name ?? 'Deleted User' }}</span>
                                                <span
                                                    class="text-[10px] text-gray-500">{{ $team->leader->email ?? '' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 4. 🔥 Members Button --}}
                                    <td class="px-6 py-4 text-center">
                                        <button type="button"
                                            data-members="{{ json_encode($team->members->load('user'), JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                                            onclick="openMembersModal(JSON.parse(this.getAttribute('data-members')), '{{ $team->name }}', '{{ $team->id }}')"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-100 hover:border-[#175c53] text-gray-600 hover:text-[#175c53] rounded-xl font-bold text-xs transition-all shadow-sm hover:shadow-md">
                                            <i class="fas fa-users"></i>
                                            <span>{{ $team->members->count() }} Members</span>
                                        </button>
                                    </td>

                                    {{-- 5. Created At --}}
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="text-xs font-bold text-gray-500">{{ $team->created_at->format('Y-m-d') }}</span>
                                        <span
                                            class="block text-[10px] text-gray-400">{{ $team->created_at->format('h:i A') }}</span>
                                    </td>

                                    {{-- 6. Actions --}}
                                    <td class="px-6 py-4 text-right">
                                        <form id="delete-team-form-{{ $team->id }}"
                                            action="{{ route('admin.teams.delete', $team->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDeleteTeam('{{ $team->id }}', '{{ $team->name }}')"
                                                class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                title="Delete Team">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-search mb-3 text-2xl opacity-50"></i>
                                            <p class="font-medium">No teams found matching your criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $teams->withQueryString()->links() }}
                </div>
            </div>
        </div>

        {{-- 🔥 Members Modal (Pop-up) --}}
        <div id="membersModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeMembersModal()">
            </div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    {{-- Header --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between items-center border-b border-gray-100">
                        <h3 class="text-lg font-black text-gray-900" id="modalTeamName">Team Members</h3>
                        <button type="button" onclick="closeMembersModal()" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        <div id="membersList" class="space-y-3">
                            {{-- JS will populate this --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- 🔥 Scripts --}}
    {{-- استدعاء مكتبة SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openMembersModal(members, teamName, teamId) {
            document.getElementById('modalTeamName').innerText = teamName + " Members";
            const listContainer = document.getElementById('membersList');
            listContainer.innerHTML = '';

            if (!members || members.length === 0) {
                listContainer.innerHTML = '<p class="text-center text-gray-400 italic">No members found.</p>';
            } else {
                let contentHtml = '';

                members.forEach(member => {
                    let user = member.user;
                    if (!user) return; // Skip if user deleted

                    let roleBadge = member.role === 'leader' ?
                        '<span class="text-[10px] bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full font-bold ml-2">Leader 👑</span>' :
                        '<span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-bold ml-2">Member</span>';

                    // رابط الحذف
                    let deleteUrl =
                        "{{ route('admin.teams.remove_member', ['team_id' => ':team_id', 'user_id' => ':user_id']) }}"
                        .replace(':team_id', teamId)
                        .replace(':user_id', user.id);

                    let deleteBtn = '';
                    // ممنوع حذف الليدر من هنا (عشان السيستم ميبوظش)، لازم يغير الليدر الأول
                    if (member.role !== 'leader') {
                        // استخدام data-username لتجنب مشاكل علامات التنصيص في الاسم
                        deleteBtn = `
                            <form id="remove-member-form-${user.id}" action="${deleteUrl}" method="POST">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content || ''}">
                                <input type="hidden" name="_method" value="DELETE">

                                <button type="button"
                                    onclick="confirmRemoveMember('${user.id}', this.getAttribute('data-username'))"
                                    data-username="${user.name}"
                                    class="text-red-400 hover:text-red-600 p-2 rounded-full hover:bg-red-50 transition"
                                    title="Kick Member">
                                    <i class="fas fa-user-times"></i>
                                </button>
                            </form>
                        `;
                    }

                    contentHtml += `
                        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-gray-200 hover:shadow-sm transition bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 font-bold shadow-sm">
                                    ${user.name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 flex items-center">
                                        ${user.name} ${roleBadge}
                                    </p>
                                    <p class="text-xs text-gray-500">${user.email}</p>
                                </div>
                            </div>
                            ${deleteBtn}
                        </div>
                    `;
                });

                listContainer.innerHTML = contentHtml;
            }

            document.getElementById('membersModal').classList.remove('hidden');
        }

        function closeMembersModal() {
            document.getElementById('membersModal').classList.add('hidden');
        }

        // 1. Delete Team Alert
        function confirmDeleteTeam(teamId, teamName) {
            Swal.fire({
                title: 'Delete Team?',
                text: `Are you sure you want to delete "${teamName}"? This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'Cancel',
                background: '#fff',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-4 py-2 font-bold',
                    cancelButton: 'rounded-xl px-4 py-2 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-team-form-' + teamId).submit();
                }
            });
        }

        // 2. Remove Member Alert
        function confirmRemoveMember(userId, userName) {
            // نقفل المودال بتاع الأعضاء مؤقتاً عشان الـ Alert يظهر بوضوح (اختياري)
            // document.getElementById('membersModal').classList.add('hidden'); // اختياري: إخفاء المودال
            Swal.fire({
                title: 'Remove Member?',
                text: `Are you sure you want to remove "${userName}" from this team?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Remove',
                cancelButtonText: 'No, keep them',
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('remove-member-form-' + userId).submit();
                } else {

                }
            });
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8f8f8;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #bbb;
        }
    </style>
@endsection
