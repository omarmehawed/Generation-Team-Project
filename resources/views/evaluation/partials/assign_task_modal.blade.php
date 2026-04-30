{{-- =====================================================
     Assign Task Modal — Evaluation Dashboard
     Features:
       - Live search by name, email, or academic number
       - Assign to all Software Team button (leader + sw_vice)
       - Assign to all Hardware Team button (leader + hw_vice)
       - Sub-leader sees only their team members in search
     ===================================================== --}}

@php
    // Build full assignable members list for JS injection
    $evalTaskMembers = collect();
    if ($myRole === 'leader') {
        $evalTaskMembers = $allMembers->where('user_id', '!=', auth()->id());
    } elseif ($myRole === 'vice_leader') {
        $myTech = strtolower($myMember->technical_role ?? 'general');
        $evalTaskMembers = $allMembers->where('user_id', '!=', auth()->id())->filter(function($m) use ($myTech) {
            $mTech = strtolower($m->technical_role ?? 'general');
            if ($myTech === 'general') return true;
            return $mTech === $myTech || $mTech === 'general';
        })->where('role', '!=', 'vice_leader');
    } elseif ($isSubLeader) {
        $evalTaskMembers = $allMembers->where('user_id', '!=', auth()->id())
            ->where('team_number', $myMember->team_number)
            ->where('is_sub_leader', false)
            ->where('role', 'member');
    }

    // Software/Hardware full lists for assign-all (leader) or domain (vice leader)
    $softwareAssignables = $allMembers->filter(fn($m) => strtolower($m->technical_role ?? '') === 'software' && $m->user_id !== auth()->id());
    $hardwareAssignables = $allMembers->filter(fn($m) => strtolower($m->technical_role ?? '') === 'hardware' && $m->user_id !== auth()->id());

    // Map data for JavaScript
    $mapMember = function($m) {
        $emailParts = explode('@', $m->user->email ?? '');
        return [
            'user_id'    => $m->user_id,
            'name'       => $m->user->name ?? 'Unknown',
            'email'      => $m->user->email ?? '',
            'academic'   => $emailParts[0] ?? '',
            'national'   => $m->user->national_id ?? '',
            'tech'       => $m->technical_role ?? 'general',
            'role'       => $m->role,
            'is_sub'     => (bool)($m->is_sub_leader ?? false),
        ];
    };

    $evalTaskAllMembersData = $evalTaskMembers->values()->map($mapMember);
    $evalTaskSoftwareMembersData = $softwareAssignables->values()->map($mapMember);
    $evalTaskHardwareMembersData = $hardwareAssignables->values()->map($mapMember);
@endphp

<div id="evalAssignTaskModal"
    class="fixed inset-0 z-[200] hidden items-center justify-center p-4"
    role="dialog" aria-modal="true">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm" onclick="closeEvalTaskModal()"></div>

    {{-- Modal Card --}}
    <div id="evalTaskModalCard"
        class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-visible transform transition-all duration-300 scale-95 opacity-0">

        {{-- Header --}}
        <div class="bg-gray-900 px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                    <i class="fas fa-tasks text-white text-sm"></i>
                </div>
                <div>
                    <h3 class="text-white font-black text-base">Assign Task</h3>
                    <p class="text-gray-400 text-[10px] font-medium uppercase tracking-widest">Weekly Evaluation</p>
                </div>
            </div>
            <button onclick="closeEvalTaskModal()" class="text-gray-400 hover:text-white transition transform hover:rotate-90 duration-200">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('tasks.store') }}" method="POST" id="evalTaskForm">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto custom-scrollbar">

                {{-- ASSIGN TO ALL BUTTONS --}}
                @if ($myRole === 'leader' || ($myRole === 'vice_leader' && strtolower($myMember->technical_role ?? '') === 'software'))
                <button type="button"
                    onclick="evalTaskAssignAll('software')"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-black text-[11px] uppercase tracking-widest shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fas fa-users-cog"></i>
                    Assign to All Software Team
                </button>
                @endif

                @if ($myRole === 'leader' || ($myRole === 'vice_leader' && strtolower($myMember->technical_role ?? '') === 'hardware'))
                <button type="button"
                    onclick="evalTaskAssignAll('hardware')"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl font-black text-[11px] uppercase tracking-widest shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fas fa-microchip"></i>
                    Assign to All Hardware Team
                </button>
                @endif

                @if ($myRole === 'leader')
                <div class="relative flex items-center">
                    <div class="flex-grow border-t border-gray-100 dark:border-gray-700"></div>
                    <span class="flex-shrink mx-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">OR SELECT INDIVIDUALLY</span>
                    <div class="flex-grow border-t border-gray-100 dark:border-gray-700"></div>
                </div>
                @endif

                {{-- Task Title --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Task Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="evalTaskTitle" required
                        placeholder="e.g. Design Database Schema"
                        class="w-full border-2 border-gray-100 dark:border-gray-700 rounded-xl px-4 py-3 text-sm font-bold bg-gray-50 dark:bg-gray-900 focus:border-gray-800 focus:bg-white outline-none transition">
                </div>

                {{-- Search Member --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Search Member</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" id="evalTaskSearch"
                            placeholder="Search by name, email or academic no..."
                            autocomplete="off"
                            oninput="filterEvalTaskMembers(this.value)"
                            class="w-full border-2 border-gray-100 dark:border-gray-700 rounded-xl pl-10 pr-4 py-3 text-sm font-semibold bg-gray-50 dark:bg-gray-900 focus:border-gray-800 focus:bg-white outline-none transition">
                    </div>

                    {{-- Dropdown Results --}}
                    <div id="evalTaskSearchResults"
                        class="absolute left-0 right-0 top-full mt-1 z-[100] rounded-xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg hidden max-h-44 overflow-y-auto">
                    </div>
                </div>

                {{-- Selected Assignees --}}
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Current Assignees:</p>
                    <div id="evalTaskAssigneeList" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        <div id="evalTaskEmptyState" class="text-center py-5 border-2 border-dashed border-gray-100 dark:border-gray-700 rounded-2xl">
                            <i class="fas fa-user-plus text-gray-200 text-3xl mb-1 block"></i>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">No members selected yet</p>
                        </div>
                    </div>
                </div>

                {{-- Deadline --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Due Date & Time <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="deadline" id="evalTaskDeadline" required
                        class="w-full border-2 border-gray-100 dark:border-gray-700 rounded-xl px-4 py-3 text-sm font-bold bg-gray-50 dark:bg-gray-900 focus:border-gray-800 outline-none transition">
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="closeEvalTaskModal()"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-800 font-bold text-xs uppercase transition px-4 py-2">
                    Cancel
                </button>
                <button type="submit"
                    class="bg-gray-900 text-white hover:bg-gray-700 font-black text-xs uppercase tracking-widest px-6 py-2.5 rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-xs"></i> Assign Task
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ──────────────────────────────────────────────────
// DATA: All assignable members for this role
// ──────────────────────────────────────────────────
const evalTaskAllMembers = @json($evalTaskAllMembersData);
const evalTaskSoftwareMembers = @json($evalTaskSoftwareMembersData);
const evalTaskHardwareMembers = @json($evalTaskHardwareMembersData);

// ──────────────────────────────────────────────────
// STATE
// ──────────────────────────────────────────────────
let evalTaskSelectedMembers = []; // { user_id, name }

// ──────────────────────────────────────────────────
// OPEN / CLOSE
// ──────────────────────────────────────────────────
function openEvalAssignTaskModal() {
    const modal = document.getElementById('evalAssignTaskModal');
    const card  = document.getElementById('evalTaskModalCard');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    }, 20);
}

function closeEvalTaskModal() {
    const modal = document.getElementById('evalAssignTaskModal');
    const card  = document.getElementById('evalTaskModalCard');
    card.classList.remove('scale-100', 'opacity-100');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        // Reset form
        document.getElementById('evalTaskForm').reset();
        evalTaskSelectedMembers = [];
        document.getElementById('evalTaskSearch').value = '';
        document.getElementById('evalTaskSearchResults').classList.add('hidden');
        renderEvalAssignees();
    }, 300);
}

// ──────────────────────────────────────────────────
// SEARCH
// ──────────────────────────────────────────────────
function filterEvalTaskMembers(query) {
    const resultsDiv = document.getElementById('evalTaskSearchResults');
    query = query.toLowerCase().trim();

    if (!query) {
        resultsDiv.classList.add('hidden');
        return;
    }

    const matches = evalTaskAllMembers.filter(m => {
        const alreadyAdded = evalTaskSelectedMembers.some(s => s.user_id == m.user_id);
        if (alreadyAdded) return false;
        return (
            m.name.toLowerCase().includes(query) ||
            m.email.toLowerCase().includes(query) ||
            (m.academic && m.academic.toString().toLowerCase().includes(query)) ||
            (m.national && m.national.toString().toLowerCase().includes(query))
        );
    });

    if (matches.length === 0) {
        resultsDiv.innerHTML = `<div class="px-4 py-3 text-xs text-gray-400 font-bold text-center">No results found</div>`;
        resultsDiv.classList.remove('hidden');
        return;
    }

    resultsDiv.innerHTML = matches.map(m => {
        const badge = m.is_sub ? '🔶 Sub Leader' : (m.role === 'vice_leader' ? '⭐ Vice Leader' : '');
        const techColor = m.tech === 'software' ? 'text-blue-500' : m.tech === 'hardware' ? 'text-orange-500' : 'text-gray-400';
        return `<div 
            onclick="evalTaskAddMember(${m.user_id}, '${escapeHtml(m.name)}', '${m.tech}')"
            class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition border-b border-gray-50 last:border-0">
            <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-[10px] font-black text-white flex-shrink-0">
                ${m.name.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase()}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-black text-gray-900 dark:text-gray-100 truncate">${escapeHtml(m.name)} ${badge ? `<span class="text-[9px] font-bold text-gray-400">${badge}</span>` : ''}</p>
                <p class="text-[10px] text-gray-400 truncate">${m.email} ${m.academic ? '· ' + m.academic : ''}</p>
            </div>
            <span class="text-[9px] font-black uppercase ${techColor}">${m.tech}</span>
        </div>`;
    }).join('');

    resultsDiv.classList.remove('hidden');
}

function evalTaskAddMember(userId, name, tech) {
    if (evalTaskSelectedMembers.some(m => m.user_id == userId)) return;
    evalTaskSelectedMembers.push({ user_id: userId, name: name, tech: tech });
    renderEvalAssignees();
    document.getElementById('evalTaskSearch').value = '';
    document.getElementById('evalTaskSearchResults').classList.add('hidden');
}

function evalTaskRemoveMember(userId) {
    evalTaskSelectedMembers = evalTaskSelectedMembers.filter(m => m.user_id != userId);
    renderEvalAssignees();
}

// ──────────────────────────────────────────────────
// ASSIGN TO ALL
// ──────────────────────────────────────────────────
function evalTaskAssignAll(domain) {
    const pool = domain === 'software' ? evalTaskSoftwareMembers : evalTaskHardwareMembers;
    pool.forEach(m => {
        if (!evalTaskSelectedMembers.some(s => s.user_id == m.user_id)) {
            evalTaskSelectedMembers.push({ user_id: m.user_id, name: m.name, tech: m.tech });
        }
    });
    renderEvalAssignees();
}

// ──────────────────────────────────────────────────
// RENDER ASSIGNEES LIST
// ──────────────────────────────────────────────────
function renderEvalAssignees() {
    const list  = document.getElementById('evalTaskAssigneeList');
    const empty = document.getElementById('evalTaskEmptyState');

    // Remove old chips (keep empty state)
    list.querySelectorAll('.eval-task-chip').forEach(el => el.remove());
    // Remove old hidden inputs
    document.querySelectorAll('input[name="user_id[]"]').forEach(el => el.remove());

    if (evalTaskSelectedMembers.length === 0) {
        empty.style.display = '';
        return;
    }
    empty.style.display = 'none';

    const form = document.getElementById('evalTaskForm');
    evalTaskSelectedMembers.forEach(m => {
        // Chip
        const chip = document.createElement('div');
        chip.className = 'eval-task-chip flex items-center justify-between bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5';
        const techColor = m.tech === 'software' ? 'bg-blue-100 text-blue-700' : m.tech === 'hardware' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600';
        chip.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center text-[10px] font-black text-white">
                    ${m.name.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase()}
                </div>
                <p class="text-xs font-black text-gray-800 dark:text-gray-200">${escapeHtml(m.name)}</p>
                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full ${techColor}">${m.tech}</span>
            </div>
            <button type="button" onclick="evalTaskRemoveMember(${m.user_id})"
                class="text-gray-300 hover:text-red-500 transition ml-2">
                <i class="fas fa-times-circle text-sm"></i>
            </button>`;
        list.appendChild(chip);

        // Hidden input
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_id[]';
        input.value = m.user_id;
        form.appendChild(input);
    });
}

function escapeHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    const search = document.getElementById('evalTaskSearch');
    const results = document.getElementById('evalTaskSearchResults');
    if (search && results && !search.contains(e.target) && !results.contains(e.target)) {
        results.classList.add('hidden');
    }
});
</script>
