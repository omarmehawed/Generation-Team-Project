@extends('layouts.batu')

@section('content')
    <div class="container-fluid py-6" x-data="{ 
                viewModalOpen: false, 
                approveModalOpen: false, 
                rejectModalOpen: false,
                selectedRequest: null,

                openViewModal(req) {
                    this.selectedRequest = req;
                    this.viewModalOpen = true;
                },
                openApproveModal(req) {
                    this.selectedRequest = req;
                    this.approveModalOpen = true;
                },
                openRejectModal(req) {
                    this.selectedRequest = req;
                    this.rejectModalOpen = true;
                }
            }">

        <!-- Page Header -->
        <div class="row align-items-center mb-6">
            <div class="col-lg-6 col-12">
                <h2 class="text-3xl font-bold text-white font-tech tracking-wide mb-1">
                    Join Requests <span class="text-cyan-400">Database</span>
                </h2>
                <p class="text-gray-400 text-sm font-medium">Manage and review incoming team applications.</p>
            </div>
            <div class="col-lg-6 col-12 text-end mt-4 mt-lg-0">
                <form method="GET" action="{{ route('join.admin') }}"
                    class="d-flex flex-wrap justify-content-lg-end gap-3 items-center">

                    <!-- Search Box -->
                    <div class="relative group flex-grow lg:flex-grow-0 lg:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-500 group-focus-within:text-cyan-400 transition-colors"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-700 rounded-xl leading-5 bg-gray-900 text-gray-300 placeholder-gray-500 focus:outline-none focus:bg-gray-800 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/50 sm:text-sm transition-all shadow-lg"
                            placeholder="ID, Name, Phone...">
                    </div>

                    <!-- Status Filter -->
                    <select name="status" onchange="this.form.submit()"
                        class="bg-gray-900 text-gray-300 border border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-cyan-500 transition-colors cursor-pointer hover:bg-gray-800">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                        </option>
                    </select>

                    <!-- Date Filter -->
                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                        class="bg-gray-900 text-gray-300 border border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-cyan-500 transition-colors cursor-pointer hover:bg-gray-800">

                    <!-- Export Button -->
                    <button type="button"
                        class="btn bg-gray-800 hover:bg-gray-700 text-cyan-400 border border-gray-700 shadow-lg rounded-xl px-4 py-2.5 flex items-center gap-2 transition-all hover:scale-105">
                        <i class="fas fa-file-export"></i> <span class="hidden sm:inline">Export</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-gray-900/50 backdrop-blur-xl rounded-3xl border border-gray-800 shadow-2xl overflow-hidden relative">
            <!-- Decoration -->
            <div
                class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="p-6">

                <!-- Table Container -->
                <div class="table-responsive">
                    <table class="w-full text-left border-separate border-spacing-y-3">
                        <thead>
                            <tr class="text-gray-400 text-xs uppercase tracking-wider font-bold">
                                <th class="pb-2 pl-4">User Identity</th>
                                <th class="pb-2 text-center">Context</th>
                                <th class="pb-2 text-center">Status</th>
                                <th class="pb-2 pr-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-300">
                            @forelse($requests as $request)
                                <tr
                                    class="bg-gray-800/40 hover:bg-gray-800/80 transition-all duration-300 group rounded-2xl shadow-sm hover:shadow-cyan-500/10 border border-gray-800 hover:border-cyan-500/30">

                                    <!-- 1. User Identity -->
                                    <td
                                        class="p-4 rounded-l-2xl border-l border-y border-transparent group-hover:border-cyan-500/30 group-hover:border-l-cyan-500">
                                        <div class="flex items-center gap-4">
                                            <!-- Avatar -->
                                            <div class="relative">
                                                <div
                                                    class="w-12 h-12 rounded-full overflow-hidden border-2 border-gray-700 group-hover:border-cyan-400 shadow-lg transition-colors">
                                                    @if($request->photo_path)
                                                        <img src="{{ asset('storage/' . $request->photo_path) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-gray-800 flex items-center justify-center text-lg font-bold text-gray-400">
                                                            {{ substr($request->full_name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- Online Status Dot (Visual Only) -->
                                                <div
                                                    class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-gray-900 rounded-full">
                                                </div>
                                            </div>

                                            <!-- Info -->
                                            <div>
                                                <h4
                                                    class="font-bold text-white text-base mb-0.5 group-hover:text-cyan-400 transition-colors">
                                                    {{ $request->full_name }}
                                                </h4>
                                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                                    <span
                                                        class="font-mono bg-gray-900 px-1.5 py-0.5 rounded border border-gray-700">{{ $request->academic_id }}</span>
                                                    <span class="w-1 h-1 bg-gray-600 rounded-full"></span>
                                                    <span>{{ $request->phone_number }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Context -->
                                    <td class="p-4 text-center border-y border-transparent group-hover:border-cyan-500/30">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                            <i class="fas fa-users text-[10px]"></i> {{ $request->group }}
                                        </span>
                                        <div class="mt-1 text-xs text-gray-500 font-medium">
                                            Created {{ $request->created_at->diffForHumans() }}
                                        </div>
                                    </td>

                                    <!-- 3. Status -->
                                    <td class="p-4 text-center border-y border-transparent group-hover:border-cyan-500/30">
                                        @if($request->status === 'pending')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 shadow-[0_0_10px_rgba(234,179,8,0.1)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> Pending
                                            </span>
                                        @elseif($request->status === 'approved')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-green-500/10 text-green-500 border border-green-500/20 shadow-[0_0_10px_rgba(34,197,94,0.1)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Approved
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-500/10 text-red-500 border border-red-500/20 shadow-[0_0_10px_rgba(239,68,68,0.1)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejected
                                            </span>
                                        @endif
                                    </td>

                                    <!-- 4. Actions -->
                                    <td
                                        class="p-4 text-end rounded-r-2xl border-r border-y border-transparent group-hover:border-cyan-500/30 group-hover:border-r-cyan-500">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- View -->
                                            <button @click="openViewModal({{ $request }})"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center bg-gray-700/50 hover:bg-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all hover:scale-110"
                                                title="View Details">
                                                <i class="fas fa-eye text-sm"></i>
                                            </button>

                                            @if($request->status === 'pending')
                                                <!-- Approve -->
                                                <button @click="openApproveModal({{ $request }})"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center bg-gray-700/50 hover:bg-green-500/20 text-gray-400 hover:text-green-400 transition-all hover:scale-110"
                                                    title="Approve">
                                                    <i class="fas fa-check text-sm"></i>
                                                </button>

                                                <!-- Reject -->
                                                <button @click="openRejectModal({{ $request }})"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center bg-gray-700/50 hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition-all hover:scale-110"
                                                    title="Reject">
                                                    <i class="fas fa-times text-sm"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center opacity-50">
                                            <div
                                                class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-inbox text-2xl text-gray-600"></i>
                                            </div>
                                            <h3 class="text-gray-400 font-bold text-lg">No requests found</h3>
                                            <p class="text-gray-600 text-sm">Try adjusting your search criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 border-t border-gray-800 pt-4 px-2">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>

        <!-- Modals (Placed Outside Table for Z-Index Safety) -->

        <!-- 1. VIEW MODAL (Same Logic, Polished UI) -->
        <div x-show="viewModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm transition-opacity"
                    @click="viewModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-700">
                    <!-- Modal Content -->
                    <div class="bg-gray-800 px-6 py-6 sm:p-8">
                        <div class="flex justify-between items-start mb-6 border-b border-gray-700/50 pb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-white font-tech">Application Details</h3>
                                <p class="text-gray-400 text-xs">Review full applicant information.</p>
                            </div>
                            <button @click="viewModalOpen = false"
                                class="bg-gray-700 hover:bg-gray-600 text-white w-8 h-8 rounded-full flex items-center justify-center transition-colors"><i
                                    class="fas fa-times"></i></button>
                        </div>

                        <template x-if="selectedRequest">
                            <div class="row g-4">
                                <!-- Sidebar Info -->
                                <div class="col-md-4 text-center border-r border-gray-700/50 pr-6">
                                    <div class="relative w-32 h-32 mx-auto mb-4">
                                        <div
                                            class="absolute inset-0 bg-cyan-500 rounded-full blur opacity-20 animate-pulse">
                                        </div>
                                        <div
                                            class="avatar w-full h-full rounded-full border-4 border-gray-700 bg-gray-800 relative z-10 overflow-hidden shadow-xl">
                                            <template x-if="selectedRequest.photo_path">
                                                <img :src="'/storage/' + selectedRequest.photo_path"
                                                    class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!selectedRequest.photo_path">
                                                <div class="w-full h-full flex items-center justify-center text-4xl font-bold text-gray-500"
                                                    x-text="selectedRequest.full_name.charAt(0)"></div>
                                            </template>
                                        </div>
                                    </div>

                                    <h5 class="text-xl font-bold text-white mb-1" x-text="selectedRequest.full_name"></h5>
                                    <p class="text-cyan-400 font-mono text-sm mb-4" x-text="selectedRequest.academic_id">
                                    </p>

                                    <span
                                        class="inline-block px-4 py-1.5 rounded-full bg-blue-500/20 text-blue-400 text-xs font-bold border border-blue-500/30 mb-6"
                                        x-text="selectedRequest.group"></span>

                                    <div
                                        class="text-left bg-gray-900/50 rounded-xl p-4 space-y-3 border border-gray-700/50">
                                        <div class="flex items-center gap-3 text-sm text-gray-300">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-gray-800 flex items-center justify-center text-blue-400">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <span x-text="selectedRequest.phone_number"></span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm text-gray-300">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-gray-800 flex items-center justify-center text-green-400">
                                                <i class="fab fa-whatsapp"></i>
                                            </div>
                                            <span x-text="selectedRequest.whatsapp_number"></span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm text-gray-300">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-gray-800 flex items-center justify-center text-gray-400">
                                                <i class="fas fa-id-card"></i>
                                            </div>
                                            <span x-text="selectedRequest.national_id"></span>
                                        </div>

                                        <!-- Address -->
                                        <div class="flex items-start gap-3 text-sm text-gray-300">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-gray-800 shrink-0 flex items-center justify-center text-purple-400 mt-0.5">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <span x-text="selectedRequest.home_address" class="break-words"></span>
                                        </div>

                                        <!-- Dorm Status -->
                                        <div class="flex items-center gap-3 text-sm text-gray-300">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-gray-800 shrink-0 flex items-center justify-center text-orange-400">
                                                <i class="fas fa-bed"></i>
                                            </div>
                                            <span
                                                x-text="selectedRequest.is_dorm == 1 ? 'Dorm Resident (مغترب)' : 'Commuter (غير مغترب)'"></span>
                                        </div>

                                        <!-- University Email (Synced from User) -->
                                        <template x-if="selectedRequest.user">
                                            <div
                                                class="flex items-center gap-3 text-sm text-gray-300 pt-3 border-t border-gray-800 mt-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-gray-800 shrink-0 flex items-center justify-center text-cyan-400">
                                                    <i class="fas fa-envelope"></i>
                                                </div>
                                                <div>
                                                    <div class="text-[10px] text-gray-500 uppercase font-bold">University
                                                        Email</div>
                                                    <span x-text="selectedRequest.user.email"
                                                        class="text-xs break-all font-mono text-cyan-400"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Answers -->
                                <div class="col-md-8 pl-6">
                                    <h5 class="text-white font-bold mb-4 flex items-center gap-2"><i
                                            class="fas fa-comments text-cyan-500"></i> Questionnaire Responses</h5>
                                    <div class="bg-gray-900/50 border border-gray-700/50 rounded-xl p-4 custom-scrollbar"
                                        style="max-height: 500px; overflow-y: auto;">
                                        <template x-for="(value, key) in selectedRequest.answers" :key="key">
                                            <div class="mb-5 last:mb-0">
                                                <label
                                                    class="text-[10px] text-blue-300/70 uppercase font-bold tracking-wider mb-1 block"
                                                    x-text="key.replace(/_/g, ' ')"></label>

                                                <!-- Simple Value -->
                                                <template x-if="typeof value !== 'object' || value === null">
                                                    <div class="text-gray-200 text-sm bg-gray-800/80 px-3 py-2 rounded-lg border-l-2 border-blue-500"
                                                        x-text="value"></div>
                                                </template>

                                                <!-- Nested Object/Array (Matrices) -->
                                                <template x-if="typeof value === 'object' && value !== null">
                                                    <div
                                                        class="mt-1 bg-gray-800/50 rounded-lg p-3 border-l-2 border-purple-500">
                                                        <template x-for="(subValue, subKey) in value" :key="subKey">
                                                            <div
                                                                class="flex justify-between items-center text-xs py-1.5 border-b border-gray-700/50 last:border-0">
                                                                <span class="text-gray-400 font-medium"
                                                                    x-text="subKey"></span>
                                                                <span
                                                                    class="text-white font-bold bg-gray-700 px-2.5 py-0.5 rounded-md text-[10px]"
                                                                    x-text="subValue"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-900/50 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse border-t border-gray-700/50">
                        <button type="button" @click="viewModalOpen = false"
                            class="btn bg-gray-700 hover:bg-gray-600 text-white rounded-xl px-6">Close</button>
                        <template x-if="selectedRequest && selectedRequest.status === 'pending'">
                            <div class="sm:mr-3 flex gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                                <button @click="viewModalOpen = false; openApproveModal(selectedRequest)"
                                    class="btn bg-green-500 hover:bg-green-600 text-white rounded-xl px-6 flex-1 sm:flex-none">Approve</button>
                                <button @click="viewModalOpen = false; openRejectModal(selectedRequest)"
                                    class="btn bg-red-500 hover:bg-red-600 text-white rounded-xl px-6 flex-1 sm:flex-none">Reject</button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. APPROVE MODAL -->
        <div x-show="approveModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm transition-opacity"
                    @click="approveModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-700">
                    <form method="POST" :action="'/join-requests/' + selectedRequest?.id + '/store-user'">
                        @csrf
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <div
                                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-500/20 mb-4 border border-green-500/30">
                                    <i class="fas fa-user-plus text-green-500 text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-white">Approve Application</h3>
                                <p class="text-sm text-gray-400 mt-2">Create user account for <span
                                        x-text="selectedRequest?.full_name" class="text-white font-bold"></span>.</p>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email
                                        Address</label>
                                    <input type="email" name="email" required
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-colors">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Password</label>
                                    <input type="password" name="password" required minlength="8"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-colors">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Confirm
                                        Password</label>
                                    <input type="password" name="password_confirmation" required minlength="8"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-colors">
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-700/50">
                            <button type="submit"
                                class="btn bg-green-500 hover:bg-green-600 text-white rounded-xl shadow-lg shadow-green-500/20 px-6 font-bold">Create
                                Account</button>
                            <button type="button" @click="approveModalOpen = false"
                                class="btn bg-transparent hover:bg-gray-800 text-gray-400 rounded-xl px-6 font-bold">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 3. REJECT MODAL -->
        <div x-show="rejectModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm transition-opacity"
                    @click="rejectModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-700 border-t-4 border-t-red-500">
                    <form method="POST" :action="'/join-requests/' + selectedRequest?.id + '/reject'">
                        @csrf
                        <div class="p-6">
                            <div class="text-center mb-4">
                                <div
                                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-500/20 mb-4 border border-red-500/30">
                                    <i class="fas fa-user-times text-red-500 text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-white">Reject Application?</h3>
                                <p class="text-sm text-gray-400 mt-2">Are you sure you want to reject <span
                                        x-text="selectedRequest?.full_name" class="text-white font-bold"></span>?</p>
                                <div class="mt-4 bg-red-500/10 border border-red-500/20 rounded-lg p-3">
                                    <p class="text-xs text-red-400 font-bold"><i
                                            class="fas fa-exclamation-triangle mr-1"></i> This action cannot be undone.</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-700/50">
                            <button type="submit"
                                class="btn bg-red-500 hover:bg-red-600 text-white rounded-xl shadow-lg shadow-red-500/20 px-6 font-bold">Reject
                                Request</button>
                            <button type="button" @click="rejectModalOpen = false"
                                class="btn bg-transparent hover:bg-gray-800 text-gray-400 rounded-xl px-6 font-bold">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection