@extends('layouts.batu')
@section('title', 'Form Analytics - ' . $form->title)

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <a href="{{ route('forms.manage.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Forms
        </a>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <span class="px-4 py-1.5 rounded-full text-sm font-bold {{ $form->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $form->is_active ? 'Active' : 'Inactive' }}
            </span>
            <button @click="$dispatch('open-pending-modal')" class="btn-primary bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-xl font-bold transition flex items-center gap-2 shadow-lg shadow-amber-500/30">
                <i class="fas fa-user-clock"></i> View Pending Members
            </button>
            <a href="{{ route('forms.manage.edit', $form->id) }}" class="btn-primary bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2 rounded-xl font-bold transition flex items-center gap-2 shadow-lg shadow-indigo-500/30">
                <i class="fas fa-edit"></i> Edit Form
            </a>
        </div>
    </div>

    <!-- Stats Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 mb-8 border-t-8 border-t-[#2596be]">
        <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100 mb-2">{{ $form->title }}</h1>
        <p class="text-gray-500">{{ $form->description }}</p>

        @if($form->is_required || $form->target_gender !== 'all')
            <div class="flex flex-wrap gap-2 mt-3">
                @if($form->is_required)
                    <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-lock mr-1"></i> Required Form</span>
                @endif
                @if($form->target_gender === 'male')
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-mars mr-1"></i> Male Only</span>
                @elseif($form->target_gender === 'female')
                    <span class="bg-pink-100 text-pink-700 text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-venus mr-1"></i> Female Only</span>
                @else
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-users mr-1"></i> Everyone</span>
                @endif
            </div>
        @endif
        
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mt-8">
            <div class="bg-gray-50 dark:bg-gray-900/40 p-5 rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="text-gray-500 mb-2"><i class="fas fa-users text-xl"></i></div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalEligible }}</div>
                <div class="text-xs text-gray-500 font-medium">Eligible Members</div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 p-5 rounded-2xl border border-blue-100 dark:border-blue-800">
                <div class="text-blue-500 mb-2"><i class="fas fa-check-circle text-xl"></i></div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $eligibleSubmitted }}</div>
                <div class="text-xs text-gray-500 font-medium">Submitted</div>
            </div>
            
            <div class="bg-amber-50 dark:bg-amber-900/20 p-5 rounded-2xl border border-amber-100 dark:border-amber-800">
                <div class="text-amber-500 mb-2"><i class="fas fa-clock text-xl"></i></div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalPending }}</div>
                <div class="text-xs text-gray-500 font-medium">Pending</div>
            </div>

            <div class="bg-emerald-50 dark:bg-emerald-900/20 p-5 rounded-2xl border border-emerald-100 dark:border-emerald-800">
                <div class="text-emerald-500 mb-2"><i class="fas fa-chart-pie text-xl"></i></div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $completionRate }}%</div>
                <div class="text-xs text-gray-500 font-medium">Completion Rate</div>
            </div>

            <div class="bg-orange-50 dark:bg-orange-900/20 p-5 rounded-2xl border border-orange-100 dark:border-orange-800">
                <div class="text-orange-500 mb-2"><i class="fas fa-calendar-alt text-xl"></i></div>
                <div class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $form->deadline ? $form->deadline->format('M d, g:i A') : 'No Deadline' }}</div>
                <div class="text-xs text-gray-500 font-medium mt-1">Deadline</div>
            </div>
        </div>
    </div>

    <!-- Charts / Questions Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        @foreach($form->questions as $question)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4">{{ $question->title }}</h3>
                
                @if(isset($chartData[$question->id]) && count($chartData[$question->id]['labels']) > 0)
                    <div class="h-64 relative w-full flex justify-center">
                        <canvas id="chart-{{ $question->id }}"></canvas>
                    </div>
                @elseif(in_array($question->type, ['short_answer', 'paragraph', 'file_upload']))
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 max-h-64 overflow-y-auto space-y-3">
                        @forelse($question->answers->take(10) as $answer)
                            @if($question->type === 'file_upload' && $answer->answer_file)
                                @php
                                    $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $answer->answer_file);
                                @endphp
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        @if($isImage)
                                            <img src="{{ Storage::url($answer->answer_file) }}" alt="Thumbnail" class="w-10 h-10 object-cover rounded shadow-sm">
                                        @else
                                            <i class="fas fa-file-alt text-[#2596be] text-2xl"></i>
                                        @endif
                                        <span class="text-sm text-gray-700 dark:text-gray-300">File uploaded by <strong>{{ $answer->response->user->name }}</strong></span>
                                    </div>
                                    <a href="{{ Storage::url($answer->answer_file) }}" target="_blank" class="text-blue-500 hover:underline text-sm font-bold shrink-0">View File</a>
                                </div>
                            @elseif($answer->answer_text)
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300">
                                    "{{ $answer->answer_text }}"
                                </div>
                            @endif
                        @empty
                            <div class="text-center text-gray-400 py-4 italic">No answers yet.</div>
                        @endforelse
                        @if($question->answers->count() > 10)
                            <div class="text-center text-xs text-gray-500 font-bold pt-2">Showing 10 most recent</div>
                        @endif
                    </div>
                @else
                    <div class="h-64 flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-chart-pie text-4xl mb-3 opacity-50"></i>
                        <p>Not enough data for chart.</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Individual Responses Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <i class="fas fa-list-ul text-[#2596be]"></i> Individual Responses
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">User</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Submitted At</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($form->responses as $response)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-gray-100">{{ $response->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $response->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $response->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('forms.manage.response.show', $response->id) }}" class="text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg transition-colors inline-block">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">No responses received.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Activity Logs -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <i class="fas fa-history text-gray-500"></i> Audit Logs
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($logs as $log)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                            @if($log->action === 'Created') <i class="fas fa-plus text-green-500"></i>
                            @elseif($log->action === 'Updated') <i class="fas fa-pen text-blue-500"></i>
                            @else <i class="fas fa-info text-gray-500"></i> @endif
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $log->causer->name ?? 'System' }} {{ strtolower($log->action) }} this form
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $log->created_at->diffForHumans() }} - {{ $log->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500 text-sm italic">No activity recorded.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    
    // Modern vibrant colors
    const colors = [
        '#2596be', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#f43f5e', '#14b8a6'
    ];

    Object.keys(chartData).forEach(questionId => {
        const data = chartData[questionId];
        const ctx = document.getElementById('chart-' + questionId);
        
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: colors.slice(0, data.labels.length),
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: window.innerWidth < 768 ? 'bottom' : 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 12
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    });
});
</script>

<!-- Alpine.js Pending Members Modal -->
<div x-data="pendingMembersModal()" @open-pending-modal.window="open = true" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="display: none;">
    <!-- Backdrop -->
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="open = false"></div>
    
    <!-- Modal -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
         class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden border border-gray-100 dark:border-gray-700">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-user-clock text-amber-500"></i> Pending Members
                </h3>
                <p class="text-sm text-gray-500 mt-1">Users who have not submitted this form yet.</p>
            </div>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row gap-4 justify-between items-center bg-white dark:bg-gray-800">
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="searchQuery" placeholder="Search by name, ID, or email..." class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-[#2596be] focus:border-[#2596be] bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                </div>
                <select x-model="roleFilter" class="w-full sm:w-48 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-[#2596be] focus:border-[#2596be] bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                    <option value="">All Roles</option>
                    <template x-for="role in availableRoles" :key="role">
                        <option :value="role" x-text="role"></option>
                    </template>
                </select>
            </div>
            <div class="flex gap-2 w-full sm:w-auto justify-end">
                <button @click="copyList" class="px-4 py-2 text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-copy" :class="copied ? 'text-green-500' : ''"></i> <span x-text="copied ? 'Copied!' : 'Copy List'"></span>
                </button>
                <button @click="exportCSV" class="px-4 py-2 text-sm font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Export CSV
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="flex-1 overflow-auto bg-gray-50 dark:bg-gray-900/20">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead class="sticky top-0 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-10">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Member Name</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Phone</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">WhatsApp</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Role / Team</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    <template x-for="user in filteredMembers" :key="user.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white" x-text="user.name"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400" x-text="user.email"></td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-mono" x-text="user.phone_number || 'N/A'"></td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-mono">
                                <template x-if="user.whatsapp_number">
                                    <a :href="'https://wa.me/' + user.whatsapp_number.replace(/[^0-9]/g, '')" target="_blank" class="text-green-600 hover:underline flex items-center gap-1">
                                        <i class="fab fa-whatsapp"></i> <span x-text="user.whatsapp_number"></span>
                                    </a>
                                </template>
                                <template x-if="!user.whatsapp_number">
                                    <span>N/A</span>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full capitalize bg-indigo-100 text-indigo-700 mr-1" x-text="user.role"></span>
                                <template x-for="membership in user.team_memberships" :key="membership.id">
                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <i class="fas fa-users text-gray-400"></i>
                                        <span x-text="membership.team ? membership.team.name : 'Unknown Team'"></span>
                                        (<span x-text="membership.role" class="capitalize"></span>)
                                    </div>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wider">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredMembers.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">No matching members found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-sm text-gray-500 flex justify-between">
            <span x-text="`Showing ${filteredMembers.length} of ${members.length} pending members`"></span>
            <span class="font-bold text-gray-700 dark:text-gray-300">Mandatory forms will block these users.</span>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pendingMembersModal', () => ({
        open: false,
        searchQuery: '',
        roleFilter: '',
        copied: false,
        members: @json($pendingUsers),
        
        get availableRoles() {
            const roles = new Set();
            this.members.forEach(m => {
                if (m.role) roles.add(m.role);
                if (m.team_memberships) {
                    m.team_memberships.forEach(tm => {
                        if (tm.role) roles.add(tm.role);
                    });
                }
            });
            return Array.from(roles).sort();
        },
        
        get filteredMembers() {
            return this.members.filter(user => {
                const searchLower = this.searchQuery.toLowerCase();
                const matchesSearch = !this.searchQuery || 
                    (user.name && user.name.toLowerCase().includes(searchLower)) ||
                    (user.email && user.email.toLowerCase().includes(searchLower)) ||
                    (user.phone_number && user.phone_number.includes(searchLower)) ||
                    (user.whatsapp_number && user.whatsapp_number.includes(searchLower));
                    
                const matchesRole = !this.roleFilter || 
                    user.role === this.roleFilter || 
                    (user.team_memberships && user.team_memberships.some(tm => tm.role === this.roleFilter));
                    
                return matchesSearch && matchesRole;
            });
        },
        
        copyList() {
            let text = "Name\tEmail\tPhone\tWhatsApp\tRole\n";
            this.filteredMembers.forEach(m => {
                text += `${m.name}\t${m.email}\t${m.phone_number || 'N/A'}\t${m.whatsapp_number || 'N/A'}\t${m.role}\n`;
            });
            navigator.clipboard.writeText(text).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        },
        
        exportCSV() {
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Name,Email,Phone,WhatsApp,Global Role,Team Roles\n";
            
            this.filteredMembers.forEach(m => {
                const name = `"${m.name.replace(/"/g, '""')}"`;
                const email = `"${m.email}"`;
                const phone = `"${m.phone_number || ''}"`;
                const whatsapp = `"${m.whatsapp_number || ''}"`;
                const globalRole = `"${m.role || ''}"`;
                
                let teamRoles = [];
                if (m.team_memberships) {
                    teamRoles = m.team_memberships.map(tm => `${tm.team ? tm.team.name : 'Unknown Team'} (${tm.role})`);
                }
                const teamRolesStr = `"${teamRoles.join('; ')}"`;
                
                csvContent += `${name},${email},${phone},${whatsapp},${globalRole},${teamRolesStr}\n`;
            });

            
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "pending_members.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }));
});
</script>
@endsection
