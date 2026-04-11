@extends('layouts.batu')

@section('content')
    <div class="container-fluid py-6 lg:py-8" x-data="{ 
                viewModalOpen: false, 
                approveModalOpen: false, 
                rejectModalOpen: false,
                exportModal: false,
                selectedRequest: null,
                questionsMap: {{ json_encode($questions->pluck('question_text', 'id')) }},

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

        <!-- Elegant Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 mb-8">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">
                        Join <span class="text-blue-600 dark:text-cyan-400">Requests</span>
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium max-w-lg">
                        Review, manage, and process all incoming applications for the Generation Team. 
                    </p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3 bg-white dark:bg-slate-900/80 p-2 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                    <!-- Export Button -->
                    <button type="button" @click="exportModal = true"
                        class="px-5 py-2.5 bg-slate-100/50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all shadow-sm font-bold text-sm flex items-center gap-2">
                        <i class="fas fa-cloud-download-alt text-amber-500"></i> Export
                    </button>

                    <!-- Question Settings Button -->
                    <a href="{{ route('join-questions.index') }}" 
                        class="px-5 py-2.5 bg-slate-100/50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all shadow-sm font-bold text-sm flex items-center gap-2">
                        <i class="fas fa-sliders-h text-blue-500"></i> Configure
                    </a>

                    <!-- ON/OFF Toggle -->
                    <div class="flex items-center gap-3 bg-slate-100 dark:bg-slate-800 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 h-full" 
                         x-data="{ 
                            enabled: '{{ $joinRequestEnabled }}' === 'on',
                            toggle() {
                                this.enabled = !this.enabled;
                                fetch('{{ route('join.toggle') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ status: this.enabled ? 'on' : 'off' })
                                });
                            }
                         }">
                        <button type="button" @click="toggle()" 
                                :class="enabled ? 'bg-emerald-500 shadow-emerald-500/40' : 'bg-slate-300 dark:bg-slate-600'"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none shadow-sm">
                            <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                        <span class="text-[10px] font-black tracking-widest uppercase" :class="enabled ? 'text-emerald-500' : 'text-slate-400'" x-text="enabled ? 'Accepting' : 'Paused'"></span>
                    </div>
                </div>
            </div>

            <!-- Highly Polished Analytics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6 mb-8">
                <!-- Total -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[1.5rem] p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Apps</p>
                    <div class="flex items-end justify-between relative z-10">
                        <h4 class="text-4xl font-black text-slate-800 dark:text-white leading-none">{{ $totalCount }}</h4>
                        <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-slate-800 flex items-center justify-center text-blue-500">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[1.5rem] p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-colors"></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pending</p>
                    <div class="flex items-end justify-between relative z-10">
                        <h4 class="text-4xl font-black text-slate-800 dark:text-white leading-none">{{ $pendingCount }}</h4>
                        <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-slate-800 flex items-center justify-center text-amber-500">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[1.5rem] p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-colors"></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Approved</p>
                    <div class="flex items-end justify-between relative z-10">
                        <h4 class="text-4xl font-black text-slate-800 dark:text-white leading-none">{{ $approvedCount }}</h4>
                        <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-slate-800 flex items-center justify-center text-emerald-500">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                </div>

                <!-- Rejected -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[1.5rem] p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-rose-500/5 rounded-full blur-2xl group-hover:bg-rose-500/10 transition-colors"></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Rejected</p>
                    <div class="flex items-end justify-between relative z-10">
                        <h4 class="text-4xl font-black text-slate-800 dark:text-white leading-none">{{ $rejectedCount }}</h4>
                        <div class="w-10 h-10 rounded-full bg-rose-50 dark:bg-slate-800 flex items-center justify-center text-rose-500">
                            <i class="fas fa-ban"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toolbar / Search Engine -->
            <form method="GET" action="{{ route('join.admin') }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-2 sm:p-3 rounded-2xl shadow-sm flex flex-col md:flex-row items-center gap-3 relative z-20 w-full mb-6">
                <!-- Search Input -->
                <div class="flex-1 w-full relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                    <input type="search" name="search" value="{{ request('search') }}"
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-transparent rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm font-medium transition-all"
                        placeholder="Search by ID, Name or phone...">
                </div>

                <!-- Filters -->
                <div class="flex w-full md:w-auto gap-3 shrink-0">
                    <select name="status" onchange="this.form.submit()"
                        class="flex-1 md:w-40 bg-slate-50 dark:bg-slate-800/50 border border-transparent rounded-xl px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-300 focus:bg-white dark:focus:bg-slate-800 focus:border-blue-500 transition-colors cursor-pointer appearance-none">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>

                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                        class="flex-1 md:w-44 bg-slate-50 dark:bg-slate-800/50 border border-transparent rounded-xl px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-300 focus:bg-white dark:focus:bg-slate-800 focus:border-blue-500 transition-colors cursor-pointer">
                </div>
            </form>
        </div>

        <!-- NEW POLISHED TABLE LAYOUT FOR DESKTOP -->
        <div class="hidden lg:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap text-left border-collapse">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest w-[30%]">Applicant Identity</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest w-[25%]">Contact Info</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest w-[20%]">Status & Context</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right w-[25%]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($requests as $request)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                                <!-- Column 1: Identity -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0">
                                            @if($request->photo_path)
                                                <img src="{{ Str::startsWith($request->photo_path, ['http://', 'https://']) ? $request->photo_path : asset('storage/' . $request->photo_path) }}" 
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($request->full_name) }}&background=random&color=fff';"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-sm font-black text-slate-400">
                                                    {{ substr($request->full_name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white text-sm" title="{{ $request->full_name }}">
                                                {{ Str::limit($request->full_name, 30) }}
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-mono text-slate-500 bg-slate-100 dark:bg-slate-800 p-0.5 px-1.5 rounded">{{ $request->academic_id }}</span>
                                                <span class="text-[10px] text-slate-400"><i class="far fa-clock"></i> {{ $request->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Column 2: Contact Info -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="text-[11px] font-bold text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                                            <i class="fas fa-phone-alt text-slate-400 w-3"></i> {{ $request->phone_number }}
                                        </div>
                                        @if($request->user)
                                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5 truncate max-w-[200px]" title="{{ $request->user->email }}">
                                                <i class="fas fa-envelope text-slate-400 w-3"></i> {{ $request->user->email }}
                                            </div>
                                        @else
                                            <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                                <i class="fas fa-envelope text-slate-300 w-3"></i> No Email
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Column 3: Status -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2 items-start">
                                        @if($request->status === 'pending')
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 dark:bg-amber-500/10 border border-amber-200/50 rounded-md">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 tracking-wide uppercase">Pending</span>
                                            </div>
                                        @elseif($request->status === 'approved')
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 rounded-md">
                                                <i class="fas fa-check text-emerald-500 text-[10px]"></i>
                                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 tracking-wide uppercase">Approved</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 dark:bg-rose-500/10 border border-rose-200/50 rounded-md">
                                                <i class="fas fa-times text-rose-500 text-[10px]"></i>
                                                <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 tracking-wide uppercase">Rejected</span>
                                            </div>
                                        @endif

                                        <div class="text-[10px] font-bold text-slate-400 flex items-center gap-1 uppercase tracking-wider">
                                            <i class="fas text-[10px] {{ $request->is_dorm == 1 ? 'fa-building text-blue-400' : 'fa-home' }}"></i>
                                            {{ $request->is_dorm == 1 ? 'Dorm' : 'Offline' }}
                                        </div>
                                    </div>
                                </td>

                                <!-- Column 4: Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openViewModal({{ json_encode($request->toArray()) }})"
                                                class="w-8 h-8 bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-blue-600 dark:text-slate-400 rounded-lg transition-colors flex items-center justify-center shrink-0" title="View Application">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        
                                        @if($request->status === 'pending')
                                            <button @click="openApproveModal({{ $request }})"
                                                    class="px-3 h-8 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-400 font-bold text-xs transition-all tracking-wide">
                                                Accept
                                            </button>
                                            <button @click="openRejectModal({{ $request }})"
                                                    class="px-3 h-8 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-lg hover:border-rose-300 hover:text-rose-600 font-bold text-xs transition-all">
                                                Reject
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                                        <i class="fas fa-inbox text-2xl text-slate-300 dark:text-slate-500"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300">No requests found</h3>
                                    <p class="text-xs text-slate-400 mt-1">Adjust filters or wait for new applications.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE CARDS (Only visible on small/medium screens) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
            @forelse($requests as $request)
                <div class="bg-white dark:bg-slate-900 rounded-[1.5rem] border border-slate-200 dark:border-slate-800 shadow-sm p-5 flex flex-col relative">
                    <!-- Mobile Avatar & Header -->
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0">
                            @if($request->photo_path)
                                <img src="{{ Str::startsWith($request->photo_path, ['http://', 'https://']) ? $request->photo_path : asset('storage/' . $request->photo_path) }}" 
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($request->full_name) }}&background=random&color=fff';"
                                        class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-sm font-black text-slate-400">
                                    {{ substr($request->full_name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 pt-0.5">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                {{ $request->full_name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-mono text-[10px] font-bold px-1.5 py-0.5 rounded">
                                    {{ $request->academic_id }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Details Row -->
                    <div class="grid grid-cols-2 gap-2 mb-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 border border-slate-100 dark:border-slate-700/50">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 flex items-center gap-1"><i class="fas fa-tag"></i> Status</div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 flex items-center gap-1"><i class="fas fa-phone"></i> Phone</div>
                        
                        <div>
                            @if($request->status === 'pending')
                                <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Pending</span>
                            @elseif($request->status === 'approved')
                                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Approved</span>
                            @else
                                <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">Rejected</span>
                            @endif
                        </div>
                        <div class="text-[11px] font-bold text-slate-800 dark:text-slate-200">{{ $request->phone_number }}</div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 mt-auto">
                        @if($request->status === 'pending')
                            <button @click="openViewModal({{ json_encode($request->toArray()) }})"
                                    class="w-10 h-10 bg-slate-100 dark:bg-slate-800 text-slate-600 rounded-xl hover:bg-slate-200 transition-colors flex items-center justify-center shrink-0">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                            <button @click="openRejectModal({{ $request }})"
                                    class="flex-1 h-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold text-xs">
                                Reject
                            </button>
                            <button @click="openApproveModal({{ $request }})"
                                    class="flex-1 h-10 bg-blue-600 text-white rounded-xl font-bold text-xs shadow-sm">
                                Accept
                            </button>
                        @else
                            <button @click="openViewModal({{ json_encode($request->toArray()) }})"
                                    class="w-full h-10 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold text-xs gap-2 flex items-center justify-center">
                                <i class="fas fa-eye text-slate-400"></i> View Details
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-10 text-center text-slate-500 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900">
                    No requests found.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center w-full">
            <div class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm overflow-hidden">
                {{ $requests->links() }}
            </div>
        </div>

        <!-- Export Modal -->
        <div x-show="exportModal" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 z-[60] flex items-center justify-center px-4" style="display: none;">
            
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" @click="exportModal = false"></div>
            
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl w-full max-w-lg relative z-10 overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh]">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50 text-slate-900 dark:bg-slate-800/80 dark:text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/10 flex items-center justify-center">
                            <i class="fas fa-file-export"></i>
                        </div>
                        Export Data
                    </h3>
                    <button @click="exportModal = false" class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 hover:text-red-500 flex items-center justify-center transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form action="{{ route('join.export') }}" method="GET" class="p-6 space-y-8 overflow-y-auto">
                    <div>
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-2">
                            <i class="fas fa-filter text-blue-500"></i> File Target Status
                        </label>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="status" value="all" checked class="peer sr-only">
                                <div class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-center text-sm font-bold text-slate-600 dark:text-slate-400 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-500/20 peer-checked:text-blue-600">
                                    All
                                </div>
                            </label>
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="status" value="pending" class="peer sr-only">
                                <div class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-center text-sm font-bold text-slate-600 dark:text-slate-400 transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-500/20 peer-checked:text-amber-600">
                                    Pending
                                </div>
                            </label>
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="status" value="approved" class="peer sr-only">
                                <div class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-center text-sm font-bold text-slate-600 dark:text-slate-400 transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-500/20 peer-checked:text-emerald-600">
                                    Approved
                                </div>
                            </label>
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="status" value="rejected" class="peer sr-only">
                                <div class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-center text-sm font-bold text-slate-600 dark:text-slate-400 transition-all peer-checked:border-rose-500 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-500/20 peer-checked:text-rose-600">
                                    Rejected
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-2">
                            <i class="fas fa-columns text-blue-500"></i> Select Columns
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['full_name' => 'Full Name', 'national_id' => 'National ID', 'academic_id' => 'Academic ID', 'phone_number' => 'Phone', 'whatsapp_number' => 'WhatsApp', 'address' => 'Address', 'date_of_birth' => 'Date of Birth', 'status' => 'Status'] as $col => $label)
                                <label class="cursor-pointer group relative">
                                    <input type="checkbox" name="columns[]" value="{{ $col }}" checked class="peer sr-only">
                                    <div class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 flex flex-row items-center gap-3 transition-all peer-checked:border-blue-500 peer-checked:shadow-sm">
                                        <div class="w-5 h-5 rounded flex items-center justify-center border border-slate-300 bg-slate-50 dark:bg-slate-700 peer-checked:bg-blue-500 peer-checked:border-transparent text-white transition-colors shrink-0">
                                            <i class="fas fa-check text-[10px]"></i>
                                        </div>
                                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-400 capitalize truncate">{{ $label }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit" @click="exportModal = false" class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold flex items-center justify-center gap-2 shadow hover:bg-blue-700 transition">
                            <i class="fas fa-file-download"></i> Generate CSV Report
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 1. VIEW MODAL (Compact & Mobile Optimized) -->
        <div x-show="viewModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="min-h-screen flex items-center justify-center p-3 sm:p-4">
                <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity" 
                     x-transition.opacity duration.300ms @click="viewModalOpen = false"></div>
                
                <div class="relative bg-white dark:bg-slate-900 w-full max-w-sm sm:max-w-xl md:max-w-3xl rounded-[1.5rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh]">
                    
                    <div class="flex justify-between items-center px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/80">
                        <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="fas fa-id-badge text-blue-500"></i> Application Details
                        </h3>
                        <button @click="viewModalOpen = false" class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <template x-if="selectedRequest">
                        <div class="flex-1 overflow-y-auto p-4 sm:p-6 pb-6">
                            <!-- Mobile Compact Profile -->
                            <div class="flex items-center gap-4 border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 flex-shrink-0 rounded-[1.2rem] overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                    <template x-if="selectedRequest.photo_path">
                                        <img :src="selectedRequest.photo_path.startsWith('http') ? selectedRequest.photo_path : (selectedRequest.photo_path.startsWith('storage/') ? '/' + selectedRequest.photo_path : '/storage/' + selectedRequest.photo_path)"
                                             class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!selectedRequest.photo_path">
                                        <div class="w-full h-full flex items-center justify-center text-xl font-black text-slate-400 capitalize" x-text="selectedRequest.full_name.charAt(0)"></div>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-lg sm:text-lg font-bold text-slate-900 dark:text-white truncate pb-0.5" x-text="selectedRequest.full_name"></h2>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-700 dark:text-slate-300" x-text="selectedRequest.academic_id"></span>
                                        <span class="text-[10px] text-slate-500" x-text="new Date(selectedRequest.created_at).toLocaleDateString()"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Grids -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Col 1: Contact & Details -->
                                <div class="space-y-4">
                                    <div class="bg-slate-50 dark:bg-slate-800/40 rounded-xl p-3 border border-slate-100 dark:border-slate-700/50">
                                        <h4 class="text-[10px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3">Contact</h4>
                                        <div class="space-y-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-7 h-7 bg-blue-100 dark:bg-blue-500/10 text-blue-500 rounded-lg flex items-center justify-center"><i class="fas fa-phone-alt text-[10px]"></i></div>
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="selectedRequest.phone_number"></span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-7 h-7 bg-green-100 dark:bg-green-500/10 text-green-500 rounded-lg flex items-center justify-center"><i class="fab fa-whatsapp text-[10px]"></i></div>
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="selectedRequest.whatsapp_number"></span>
                                            </div>
                                            <template x-if="selectedRequest.user">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-7 h-7 bg-slate-200 dark:bg-slate-700 text-slate-500 rounded-lg flex items-center justify-center"><i class="fas fa-envelope text-[10px]"></i></div>
                                                    <span class="text-[11px] font-bold text-blue-600 dark:text-cyan-400 truncate max-w-full" x-text="selectedRequest.user.email"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-slate-50 dark:bg-slate-800/40 rounded-xl p-3 border border-slate-100 dark:border-slate-700/50">
                                        <h4 class="text-[10px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Details</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center text-[11px]">
                                                <span class="text-slate-500">ID</span>
                                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedRequest.national_id"></span>
                                            </div>
                                            <div class="flex justify-between items-center text-[11px]">
                                                <span class="text-slate-500">Resident</span>
                                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedRequest.is_dorm == 1 ? 'Yes' : 'No'"></span>
                                            </div>
                                            <div class="flex flex-col gap-1 text-[11px] mt-2 border-t border-slate-200 dark:border-slate-700 pt-2">
                                                <span class="text-slate-500">Address</span>
                                                <span class="font-bold text-slate-800 dark:text-slate-200 leading-tight" x-text="selectedRequest.address"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Col 2: Answers -->
                                <div>
                                    <h4 class="text-[10px] sm:text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Q&A</h4>
                                    <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1 custom-scrollbar">
                                        <template x-for="(value, id) in selectedRequest.answers" :key="id">
                                            <div class="bg-slate-50 dark:bg-slate-800/40 rounded-xl p-3 border border-slate-100 dark:border-slate-700/50">
                                                <h5 class="text-[10px] text-blue-600 dark:text-blue-400 font-bold mb-1 uppercase tracking-wide leading-tight" 
                                                   x-text="questionsMap[id] || (id.replace(/_/g, ' '))"></h5>
                                                
                                                <template x-if="typeof value !== 'object' || value === null">
                                                    <p class="text-slate-800 dark:text-slate-200 text-xs font-medium" x-text="value || 'No response'"></p>
                                                </template>

                                                <template x-if="typeof value === 'object' && value !== null">
                                                    <div class="space-y-1.5 mt-2">
                                                        <template x-for="(subVal, subKey) in value" :key="subKey">
                                                            <div class="flex justify-between items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-2.5 py-1.5 rounded-lg shadow-sm">
                                                                <span class="text-[10px] text-slate-500 dark:text-slate-400" x-text="subKey"></span>
                                                                <span class="text-[10px] font-bold text-slate-900 dark:text-slate-200" x-text="subVal"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!selectedRequest.answers || Object.keys(selectedRequest.answers).length === 0">
                                            <div class="border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-center py-4 rounded-xl">
                                                <p class="text-[11px] text-slate-400 font-bold">No answers provided</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 p-3 sm:p-4 rounded-b-[1.5rem]">
                            <template x-if="selectedRequest.status === 'pending'">
                                <div class="flex gap-2">
                                    <button @click="viewModalOpen = false; openRejectModal(selectedRequest)"
                                            class="w-1/3 py-3 bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-[1rem] text-sm font-bold hover:border-rose-400 hover:text-rose-600 transition-colors">
                                        Reject
                                    </button>
                                    <button @click="viewModalOpen = false; openApproveModal(selectedRequest)"
                                            class="w-2/3 py-3 bg-blue-600 dark:bg-blue-500 text-white rounded-[1rem] text-sm font-bold shadow-md hover:bg-blue-700 transition-colors tracking-wide">
                                        Accept
                                    </button>
                                </div>
                            </template>
                            <template x-if="selectedRequest.status !== 'pending'">
                                <button @click="viewModalOpen = false" class="w-full py-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white font-bold rounded-[1rem] text-sm">Close Layout</button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- 2. APPROVE MODAL -->
        <div x-show="approveModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity" @click="approveModalOpen = false"></div>
                
                <div class="inline-block bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all align-middle max-w-sm w-full border border-slate-200 dark:border-slate-800">
                    <form method="POST" :action="'/join-requests/' + selectedRequest?.id + '/store-user'">
                        @csrf
                        <div class="p-6">
                            <div class="text-center mb-6 mt-2">
                                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-600 mb-4 border border-blue-100 dark:border-blue-500/20">
                                    <i class="fas fa-user-check text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Accept Application</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">Set up account credentials for <span x-text="selectedRequest?.full_name" class="font-bold text-slate-800 dark:text-slate-200"></span>.</p>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-0.5">Email Address</label>
                                    <input type="email" name="email" required
                                        class="w-full bg-slate-50 dark:bg-slate-800 border bg-transparent border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-3.5 text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-0.5">Password</label>
                                    <input type="password" name="password" required minlength="8"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border bg-transparent border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-3.5 text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-0.5">Confirm Password</label>
                                    <input type="password" name="password_confirmation" required minlength="8"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border bg-transparent border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-3.5 text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/80 p-5 flex gap-3 border-t border-slate-100 dark:border-slate-800 rounded-b-3xl">
                            <button type="button" @click="approveModalOpen = false" class="flex-1 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-2xl font-bold text-sm transition-colors hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="flex-1 py-3.5 bg-blue-600 text-white rounded-2xl font-bold text-sm shadow-md transition-colors hover:bg-blue-700">Accept</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 3. REJECT MODAL -->
        <div x-show="rejectModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity" @click="rejectModalOpen = false"></div>
                
                <div class="inline-block bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all align-middle max-w-sm w-full border border-slate-200 dark:border-slate-800">
                    <form method="POST" :action="'/join-requests/' + selectedRequest?.id + '/reject'">
                        @csrf
                        <div class="p-6">
                            <div class="text-center mb-2 mt-4">
                                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-500 mb-4 border border-rose-100 dark:border-rose-500/20">
                                    <i class="fas fa-times text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Reject Application</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Are you sure you want to reject <span x-text="selectedRequest?.full_name" class="font-bold text-slate-800 dark:text-slate-200"></span>?</p>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/80 p-5 flex gap-3 border-t border-slate-100 dark:border-slate-800 rounded-b-3xl">
                            <button type="button" @click="rejectModalOpen = false" class="flex-1 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-2xl font-bold text-sm transition-colors hover:border-slate-300 hover:text-slate-900">Cancel</button>
                            <button type="submit" class="flex-1 py-3.5 bg-rose-600 text-white rounded-2xl font-bold text-sm shadow-md transition-colors hover:bg-rose-700">Reject Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection