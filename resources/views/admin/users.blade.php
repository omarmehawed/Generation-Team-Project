@extends('layouts.staff')

@section('content')
    {{-- حساب الإحصائيات داخل الـ View مباشرة لعدم تعديل الـ Controller --}}
    @php
        $totalUsers = count($users);
        $totalStudents = $users->where('role', 'student')->count();
        $totalStaff = $users->whereIn('role', ['doctor', 'ta', 'admin'])->count();
    @endphp

    <div class="min-h-screen bg-[#F3F4F6] p-6 font-sans relative overflow-hidden">
        {{-- Background Decoration --}}
        <div
            class="absolute top-0 left-0 w-full h-64 bg-gradient-to-r from-blue-600 to-indigo-700 shadow-lg -z-10 rounded-b-[3rem]">
        </div>

        <div class="max-w-7xl mx-auto space-y-8 mt-4">

            {{-- 🌟 Header Section --}}
            <div class="flex flex-col md:flex-row justify-between items-end gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight flex items-center gap-3">
                        <span class="bg-white/20 p-2 rounded-xl backdrop-blur-md border border-white/10">👥</span>
                        User Management
                    </h1>
                    <p class="mt-2 text-gray-500 text-sm font-medium">Manage access, roles, and permissions effectively.
                    </p>
                </div>

                @if (session('success'))
                    <div
                        class="animate-fade-in-up bg-white text-green-700 px-6 py-3 rounded-2xl shadow-xl border-l-4 border-green-500 flex items-center gap-3">
                        <div class="bg-green-100 p-1 rounded-full"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg></div>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                @endif
            </div>

            {{-- 📊 Stats Cards (Auto Calculated) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between hover:shadow-xl transition-shadow cursor-default">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Total Accounts</p>
                        <h2 class="text-3xl font-black text-gray-800 mt-1">{{ $totalUsers }}</h2>
                    </div>
                    <div class="p-4 bg-blue-50 text-blue-600 rounded-2xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>

                <div
                    class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between hover:shadow-xl transition-shadow cursor-default">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Active Students</p>
                        <h2 class="text-3xl font-black text-gray-800 mt-1">{{ $totalStudents }}</h2>
                    </div>
                    <div class="p-4 bg-green-50 text-green-600 rounded-2xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                </div>

                <div
                    class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between hover:shadow-xl transition-shadow cursor-default">
                    <div>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Staff & Admins</p>
                        <h2 class="text-3xl font-black text-gray-800 mt-1">{{ $totalStaff }}</h2>
                    </div>
                    <div class="p-4 bg-purple-50 text-purple-600 rounded-2xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 🎛️ Control Panel --}}

            <div
                class="bg-white/80 backdrop-blur-md p-4 rounded-2xl shadow-lg border border-gray-200 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 sticky top-4 z-30">

                {{-- Search & Filter --}}

                <form action="{{ route('admin.users') }}" method="GET"
                    class="flex flex-col md:flex-row gap-2 w-full xl:flex-1">

                    {{-- Search --}}
                    <div class="relative w-full md:flex-grow-[2] group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition duration-200 outline-none font-medium">
                    </div>

                    {{-- Role --}}
                    <select name="role" onchange="this.form.submit()"
                        class="py-2.5 px-3 bg-gray-50 border-transparent rounded-xl cursor-pointer font-medium text-gray-600 focus:ring-2 focus:ring-blue-500 hover:bg-gray-100 transition md:w-28">
                        <option value="">🎭 Role</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Students</option>
                        <option value="doctor" {{ request('role') == 'doctor' ? 'selected' : '' }}>Doctors</option>
                        <option value="ta" {{ request('role') == 'ta' ? 'selected' : '' }}>TAs</option>
                        @if (Auth::user()->role === 'admin')
                            <option value="admin">🛡️ Admin</option>
                        @endif
                    </select>

                    {{-- Department --}}
                    <select name="department" onchange="this.form.submit()"
                        class="py-2.5 px-3 bg-gray-50 border-transparent rounded-xl cursor-pointer font-medium text-gray-600 focus:ring-2 focus:ring-blue-500 hover:bg-gray-100 transition md:w-28">
                        <option value="">🏢 Dept</option>
                        <option value="general" {{ request('department') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="software" {{ request('department') == 'software' ? 'selected' : '' }}>Software
                        </option>
                        <option value="network" {{ request('department') == 'network' ? 'selected' : '' }}>Network</option>
                    </select>

                    {{-- 🔥 Date Range (From - To) --}}
                    <div
                        class="flex items-center gap-1 bg-gray-50 rounded-xl px-2 border border-transparent focus-within:ring-2 focus-within:ring-blue-500 focus-within:bg-white transition">
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="bg-transparent border-none text-xs font-bold text-gray-600 focus:ring-0 p-1 w-24"
                            title="From Date">
                        <span class="text-gray-400">-</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="bg-transparent border-none text-xs font-bold text-gray-600 focus:ring-0 p-1 w-24"
                            title="To Date">
                    </div>

                    {{-- زرار الفلترة (عشان التواريخ) --}}
                    <button type="submit"
                        class="px-3 py-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 font-bold flex items-center justify-center transition"
                        title="Apply Filter">
                        <i class="fas fa-filter"></i>
                    </button>

                    {{-- Reset --}}
                    @if (request()->anyFilled(['search', 'role', 'department', 'date_from', 'date_to']))
                        <a href="{{ route('admin.users') }}"
                            class="px-3 py-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 font-bold flex items-center justify-center transition"
                            title="Reset Filters">
                            ✕
                        </a>
                    @endif
                </form>

                {{-- Action Buttons (Wrapped & Responsive) --}}
                <div class="flex flex-wrap gap-2 w-full xl:w-auto justify-end">

                    {{-- Import --}}
                    <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                        class="flex-1 sm:flex-none bg-white text-gray-700 border border-gray-200 py-2.5 px-4 rounded-xl font-bold shadow-sm hover:bg-gray-50 transition flex items-center justify-center gap-2 whitespace-nowrap text-sm">
                        <i class="fas fa-file-upload text-gray-500"></i> Import
                    </button>

                    {{-- Export --}}
                    <button onclick="exportSelected()"
                        class="flex-1 sm:flex-none bg-emerald-50 text-emerald-700 border border-emerald-200 py-2.5 px-4 rounded-xl font-bold shadow-sm hover:bg-emerald-100 transition flex items-center justify-center gap-2 whitespace-nowrap text-sm">
                        <i class="fas fa-file-export"></i> Export
                    </button>

                    {{-- New User --}}
                    <button onclick="openAddModal()"
                        class="flex-1 sm:flex-none bg-blue-600 text-white py-2.5 px-5 rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition flex items-center justify-center gap-2 whitespace-nowrap text-sm">
                        <i class="fas fa-plus"></i> New User
                    </button>

                    {{-- Backup --}}
                    @if (Auth::user()->hasPermission('backup_db'))
                        <a href="{{ route('admin.database.export') }}"
                            class="flex-1 sm:flex-none bg-indigo-50 text-indigo-700 border border-indigo-200 py-2.5 px-4 rounded-xl font-bold shadow-sm hover:bg-indigo-100 transition flex items-center justify-center gap-2 whitespace-nowrap text-sm">
                            <i class="fas fa-database"></i> Backup
                        </a>
                    @endif
                </div>

                {{-- Hidden Export Form --}}
                <form id="exportForm" action="{{ route('admin.users.export') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="selected_ids" id="exportInput">
                </form>
            </div>
            {{-- 📋 Deleted Checkbox --}}
            <div class="flex items-center gap-2 ml-4">
                <a href="{{ request('trash') ? route('admin.users') : route('admin.users', ['trash' => 1]) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border transition-all duration-300 font-bold
               {{ request('trash')
        ? 'bg-red-100 text-red-700 border-red-300 shadow-inner'
        : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">

                    @if (request('trash'))
                        <i class="fas fa-trash-restore"></i> View Active Users
                    @else
                        <i class="fas fa-trash-alt"></i> View Trash
                    @endif
                </a>
            </div>

            {{-- 📋 The Table --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-5 text-left">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" id="selectAll" onclick="toggleSelectAll()"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-3 h-3 cursor-pointer">
                                    </label>
                                </th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    User Identity</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Role & Status</th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Context / Permissions</th>

                                {{-- غير العنوان --}}
                                {{-- لو في التراش، اظهر "Deleted By"، لو عادي اظهر "Created By" --}}
                                <th class="px-6 py-5 text-center text-xs font-bold text-gray-400 uppercase">
                                    {{ request('trash') ? 'Deleted By' : 'Created By' }}
                                </th>
                                <th class="px-6 py-5 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($users as $user)
                                                    <tr class="hover:bg-blue-50/30 transition duration-150 group">
                                                        <td class="px-6 py-4 text-center">
                                                            <div class="flex items-center justify-center">
                                                                <input type="checkbox" name="selected_users[]" value="{{ $user->id }}"
                                                                    onclick="updateBulkAction()" data-role="{{ $user->role }}"
                                                                    data-name="{{ $user->name }}" {{-- 👈 لازم السطر ده يكون موجود --}}
                                                                    data-email="{{ $user->email }}" {{-- 👈 والسطر ده كمان --}}
                                                                    class="user-checkbox w-4 h-4 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-all duration-200 checked:bg-blue-600 checked:border-blue-600">
                                                            </div>
                                                        </td>
                                                        {{-- Name & Avatar --}}
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center">
                                                                <div
                                                                    class="flex-shrink-0 h-11 w-11 rounded-full bg-gradient-to-br from-indigo-50 to-blue-100 border border-blue-100 flex items-center justify-center text-blue-600 font-extrabold text-lg shadow-sm">
                                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                                </div>
                                                                <div class="ml-4">
                                                                    <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        {{-- Role Pill --}}
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center">
                                                                @php
                                                                    $roleConfig = match ($user->role) {
                                                                        'doctor' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'dot' => 'bg-red-500'],
                                                                        'ta' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-100', 'dot' => 'bg-orange-500'],
                                                                        'admin' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-100', 'dot' => 'bg-indigo-500'],
                                                                        default => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-100', 'dot' => 'bg-green-500'],
                                                                    };
                                                                @endphp
                                 <span
                                                                    class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-full border shadow-sm {{ $roleConfig['bg'] }} {{ $roleConfig['text'] }} {{ $roleConfig['border'] }}">
                                                                    <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $roleConfig['dot'] }}"></span>
                                                                    {{ strtoupper($user->role) }}
                                                                </span>
                                                            </div>
                                                        </td>

                                                        {{-- Context Info --}}
                                                        <td class="px-6 py-4 text-center">
                                                            @if ($user->role == 'student')
                                                                <div class="inline-flex flex-col items-center">
                                                                    <span
                                                                        class="text-xs font-semibold text-gray-700 bg-gray-100 px-2 py-0.5 rounded border border-gray-200">Year
                                                                        {{ $user->academic_year }}</span>
                                                                    <span
                                                                        class="text-[10px] text-gray-400 mt-1 uppercase tracking-wide">{{ $user->department }}
                                                                        Dept</span>
                                                                </div>
                                                            @else
                                                                @if ($user->role == 'admin')
                                                                    <span
                                                                        class="text-xs text-indigo-600 font-bold bg-indigo-50 px-2 py-1 rounded border border-indigo-100">🔥
                                                                        Full Access</span>
                                                                @else
                                                                    <span class="text-xs text-gray-500 flex items-center justify-center gap-1">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                                            </path>
                                                                        </svg>
                                                                        {{ count($user->permissions ?? []) }} Privileges
                                                                    </span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        {{-- created by // deleted by --}}
                                                        <td class="px-6 py-4 text-center">
                                                            @if (request('trash'))
                                                                {{-- عرض مين اللي حذف --}}
                                                                @if ($user->deleter)
                                                                    <div class="flex flex-col items-center">
                                                                        <span class="text-xs font-bold text-red-600">{{ $user->deleter->name }}</span>
                                                                        <span
                                                                            class="text-[10px] text-gray-400">{{ $user->deleted_at->diffForHumans() }}</span>
                                                                    </div>
                                                                @else
                                                                    <span class="text-xs text-gray-400">Unknown</span>
                                                                @endif
                                                            @else
                                                                {{-- عرض مين اللي أنشأ (الكود القديم) --}}
                                                                @if ($user->creator)
                                                                    <div class="flex flex-col items-center">
                                                                        <span class="text-xs font-bold text-gray-700">{{ $user->creator->name }}</span>
                                                                        <span
                                                                            class="text-[10px] text-gray-400">{{ $user->created_at->format('d M, Y') }}</span>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        {{-- Actions --}}
                                                        <td class="px-6 py-4 text-center">
                                                            <div
                                                                class="flex justify-center items-center gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                                                <div class="flex justify-center items-center gap-2">
                                                                    @if (request('trash'))
                                                                        {{-- ♻️ زرار الاسترجاع --}}
                                                                        <a href="{{ route('admin.users.restore', $user->id) }}"
                                                                            class="p-2 bg-green-50 text-green-600 rounded-lg border border-green-200 hover:bg-green-100 transition"
                                                                            title="Restore">
                                                                            <i class="fas fa-undo"></i>
                                                                        </a>

                                                                        {{-- 🚫 زرار الحذف النهائي --}}

                                                                        <button onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')"
                                                                            class="p-2 bg-red-50 text-red-600 rounded-lg border border-red-200 hover:bg-red-100 transition"
                                                                            title="Permanent Delete">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    @else
                                                                        <button onclick="openEditModal({{ $user }})"
                                                                            class="p-2 bg-white text-yellow-600 rounded-lg border border-gray-200 hover:border-yellow-400 hover:text-yellow-700 hover:shadow-md transition"
                                                                            title="Edit User">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                                                </path>
                                                                            </svg>
                                                                        </button>
                                                                        {{-- زرار النقل للسلة (بقى مباشر Direct) --}}
                                                                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                                                            class="inline-block">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="p-2 bg-white text-red-600 rounded-lg border border-gray-200 hover:border-red-400 hover:text-red-700 hover:shadow-md transition"
                                                                                title="Move to Trash">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                                    </path>
                                                                                </svg>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                                {{-- 🔥 Smart Floating Bulk Action Bar --}}
                                                                <div id="bulkActionBar"
                                                                    class="fixed bottom-10 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-6 py-4 rounded-full shadow-2xl z-50 flex items-center gap-6 transition-all duration-300 translate-y-32 opacity-0">
                                                                    <div class="flex items-center gap-2">
                                                                        <span
                                                                            class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full"
                                                                            id="selectedCount">0</span>
                                                                        <span class="font-medium text-sm">Users Selected</span>
                                                                    </div>

                                                                    <div class="h-6 w-px bg-gray-700"></div>

                                                                    {{-- 🅰️ أزرار صفحة الـ Active Users --}}
                                                                    @if (!request('trash'))
                                                                        <button onclick="openBulkEditModal()"
                                                                            class="flex items-center gap-2 text-sm font-bold hover:text-blue-400 transition">
                                                                            <i class="fas fa-edit"></i> Edit Permissions & Courses
                                                                        </button>
                                                                        {{-- 🔥 زرار الحذف الجديد --}}
                                                                        <button onclick="openBulkDeleteModal()"
                                                                            class="flex items-center gap-2 text-sm font-bold text-red-400 hover:text-red-300 transition">
                                                                            <i class="fas fa-trash-alt"></i> Move to Trash
                                                                        </button>
                                                                    @else
                                                                        {{-- 🅱️ أزرار صفحة الـ Trash Users (جديد) --}}
                                                                        {{-- زرار الاسترجاع الجماعي --}}
                                                                        <form action="{{ route('admin.users.bulk_trash_action') }}" method="POST"
                                                                            class="flex items-center">
                                                                            @csrf
                                                                            <input type="hidden" name="selected_ids" id="restoreIds">
                                                                            <input type="hidden" name="action" value="restore">
                                                                            <button type="submit"
                                                                                class="flex items-center gap-2 text-sm font-bold text-green-400 hover:text-green-300 transition">
                                                                                <i class="fas fa-undo"></i> Restore All
                                                                            </button>
                                                                        </form>

                                                                        <div class="h-6 w-px bg-gray-700 mx-2"></div>

                                                                        {{-- زرار الحذف النهائي الجماعي --}}
                                                                        <button onclick="confirmBulkForceDelete()"
                                                                            class="flex items-center gap-2 text-sm font-bold text-red-400 hover:text-red-300 transition">
                                                                            <i class="fas fa-times"></i> Delete Forever
                                                                        </button>

                                                                        {{-- فورم مخفية للحذف النهائي عشان نبعتها بالـ JS --}}
                                                                        <form id="bulkForceDeleteForm"
                                                                            action="{{ route('admin.users.bulk_trash_action') }}" method="POST"
                                                                            class="hidden">
                                                                            @csrf
                                                                            <input type="hidden" name="selected_ids" id="forceDeleteIds">
                                                                            <input type="hidden" name="action" value="force_delete">
                                                                        </form>
                                                                    @endif

                                                                    {{-- زرار الإغلاق --}}
                                                                    <button onclick="clearSelection()" class="ml-4 text-gray-500 hover:text-white">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                        </td>
                                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <div class="bg-gray-50 p-4 rounded-full mb-3">
                                                <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <p class="text-lg font-medium">No users found matching your search.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination & Count Section --}}
                <div
                    class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">

                    {{-- جملة: صفحة رقم كذا من كذا --}}
                    <div class="text-sm text-gray-500 font-medium">
                        Showing Page <span class="font-bold text-gray-800">{{ $users->currentPage() }}</span>
                        of <span class="font-bold text-gray-800">{{ $users->lastPage() }}</span>
                        <span class="text-xs text-gray-400 mx-1">({{ $users->total() }} Total Users)</span>
                    </div>

                    {{-- أزرار التنقل (Laravel Links) --}}
                    <div class="scale-90 origin-right">
                        {{-- withQueryString مهم جداً عشان الفلتر ميروحش لما تقلب الصفحة --}}
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ✅ استدعاء ملف المودالز الخارجي --}}
    @include('admin.users_modals')

    <script>
        function toggleFields() {
            const role = document.getElementById('role').value;
            const studentFields = document.getElementById('studentFields');
            const staffFields = document.getElementById('staffFields');
            const nationalIdField = document.getElementById('nationalIdField'); // The div containing national_id input

            if (role === 'student') {
                studentFields.classList.remove('hidden');
                studentFields.classList.add('contents');
                staffFields.classList.add('hidden');

                // Show National ID field explicitly
                if (nationalIdField) {
                    nationalIdField.classList.remove('hidden');
                }
            } else {
                studentFields.classList.add('hidden');
                studentFields.classList.remove('contents');
                staffFields.classList.remove('hidden');

                // Hide and clear National ID field explicitly
                if (nationalIdField) {
                    nationalIdField.classList.add('hidden');
                    document.getElementById('national_id').value = '';
                }
            }
        }

        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Add New User';
            document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('userForm').reset();

            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[name="courses[]"]').forEach(cb => cb.checked = false);

            toggleFields();
            document.getElementById('userModal').classList.remove('hidden');
        }

        function openEditModal(user) {
            document.getElementById('modalTitle').innerText = 'Edit User Details';

            // بناء الرابط بشكل آمن بعيداً عن الاستبدال النصي المباشر الذي قد يسبب مشاكل
            // لكن بما أن Blade يطبع الرابط كسترينج، سنستخدم الطريقة الآمنة
            let baseUrl = "{{ route('admin.users.update', 'ID_PLACEHOLDER') }}";
            document.getElementById('userForm').action = baseUrl.replace('ID_PLACEHOLDER', user.id);

            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;
            document.getElementById('role').value = user.role;
            document.getElementById('academic_year').value = user.academic_year;
            document.getElementById('department').value = user.department;

            // Populate national_id if it exists
            const nationalIdInput = document.getElementById('national_id');
            if (nationalIdInput) {
                nationalIdInput.value = user.national_id || '';
            }

            let perms = user.permissions || [];
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                cb.checked = perms.includes(cb.value);
            });

            document.querySelectorAll('input[name="courses[]"]').forEach(cb => cb.checked = false);
            if (user.courses && user.courses.length > 0) {
                user.courses.forEach(course => {
                    let checkbox = document.getElementById('course_' + course.id);
                    if (checkbox) checkbox.checked = true;
                });
            }

            toggleFields();
            document.getElementById('userModal').classList.remove('hidden');
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('delete_user_name').innerText = name;

            // استخدام placeholder واضح عشان التبديل يكون دقيق
            let baseUrl = "{{ route('admin.users.force_delete', 'ID_PLACEHOLDER') }}";
            document.getElementById('deleteForm').action = baseUrl.replace('ID_PLACEHOLDER', id);
        }


        // === Bulk Action Logic ===

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.user-checkbox');

            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkAction();
        }

        // دالة تحديث الشريط (تعديل بسيط)
        function updateBulkAction() {
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            const count = checkboxes.length;
            const actionBar = document.getElementById('bulkActionBar');

            document.getElementById('selectedCount').innerText = count;

            // تجميع الـ IDs
            let ids = [];
            checkboxes.forEach(cb => ids.push(cb.value));
            const idsString = ids.join(',');

            // 1. لو إحنا في Active -> املأ الـ input بتاع المودال
            const bulkInput = document.getElementById('bulkSelectedIds');
            if (bulkInput) bulkInput.value = idsString;

            // 2. لو إحنا في Trash -> املأ الـ inputs بتاعة الاسترجاع والحذف
            const restoreInput = document.getElementById('restoreIds');
            const forceDeleteInput = document.getElementById('forceDeleteIds');
            if (restoreInput) restoreInput.value = idsString;
            if (forceDeleteInput) forceDeleteInput.value = idsString;

            // إظهار/إخفاء الشريط
            if (count > 0) {
                actionBar.classList.remove('translate-y-32', 'opacity-0');
            } else {
                actionBar.classList.add('translate-y-32', 'opacity-0');
                document.getElementById('selectAll').checked = false;
            }
        }

        function confirmBulkForceDelete() {
            // 1. تجميع الـ IDs المختارة
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            let ids = [];
            checkboxes.forEach(cb => ids.push(cb.value));

            // لو مفيش حاجة مختارة ميعملش حاجة
            if (ids.length === 0) return;

            // 2. وضع الـ IDs داخل الـ Input المخفي اللي جوه المودال بتاع الحذف النهائي
            // (استخدمنا querySelector عشان نضمن إننا بنكلم الانبوت اللي جوه المودال مش اللي في الشريط)
            const modalInput = document.querySelector('#bulkForceDeleteModal #forceDeleteIds');
            if (modalInput) modalInput.value = ids.join(',');

            // 3. تحديث رقم عدد المستخدمين في رسالة التحذير
            const countSpan = document.getElementById('forceDeleteCount');
            if (countSpan) countSpan.innerText = ids.length;

            // 4. إظهار المودال
            document.getElementById('bulkForceDeleteModal').classList.remove('hidden');
        }

        // دالة إغلاق المودال (ضيفها برضه عشان الزرار "Cancel" يشتغل)
        function closeBulkForceDeleteModal() {
            document.getElementById('bulkForceDeleteModal').classList.add('hidden');
        }

        // دالة التصدير للإكسيل
        function exportSelected() {
            // 1. تجميع الـ IDs المختارة
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            let ids = [];
            checkboxes.forEach(cb => ids.push(cb.value));

            // 2. التحقق: لازم يكون مختار حد
            if (ids.length === 0) {
                alert('⚠️ Please select at least one user to export.');
                return;
            }

            // 3. وضع الـ IDs في الفورم المخفية وإرسالها
            document.getElementById('exportInput').value = ids.join(',');
            document.getElementById('exportForm').submit();

            // (اختياري) رسالة تأكيد بسيطة
            // alert('🚀 Export started for ' + ids.length + ' users!');
        }

        function openBulkEditModal() {
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            let usersData = [];
            let hasStudent = false;
            let hasStaff = false;

            // 1. تجميع البيانات
            checkboxes.forEach(cb => {
                let role = cb.getAttribute('data-role');
                usersData.push({
                    id: cb.value,
                    name: cb.getAttribute('data-name'),
                    email: cb.getAttribute('data-email')
                });

                if (role === 'student') hasStudent = true;
                else hasStaff = true;
            });

            // 2. التحقق من الخلط
            if (hasStudent && hasStaff) {
                document.getElementById('errorModal').classList.remove('hidden');
                return;
            }

            // 3. عرض الأسماء

            const listContainer = document.getElementById('bulkListContainer');
            listContainer.innerHTML = '';
            usersData.forEach(user => {
                // ... (نفس كود رسم الكارت) ...
                let displayName = user.name ? user.name : 'Unknown';
                let displayEmail = user.email ? user.email : 'No Email';
                listContainer.innerHTML += `
                    <div class="flex items-start gap-3 p-2 bg-gray-50 border border-gray-100 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold uppercase shrink-0">${displayName.charAt(0)}</div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold text-gray-800 truncate">${displayName}</p>
                            <p class="text-[10px] text-gray-500 truncate font-mono">${displayEmail}</p>
                        </div>
                    </div>`;
            });

            // 4. 🔥🔥 التبديل الذكي بين الحقول (ده التعديل المهم) 🔥🔥

            // تعريف العناصر
            const studentFields = document.getElementById('bulkStudentFields');
            const staffPermissions = document.getElementById('bulkStaffPermissions');
            const staffCourses = document.getElementById('bulkCoursesWrapper');

            if (hasStudent) {
                // حالة الطلبة: اظهر حقول السنة/القسم واخفي الباقي
                document.getElementById('modalTitleBulk').innerText = "⚡ Bulk Edit Students";

                studentFields.classList.remove('hidden');
                staffPermissions.classList.add('hidden');
                staffCourses.classList.add('hidden');
            } else {
                // حالة الستاف: اظهر الصلاحيات والكورسات واخفي حقول الطلبة
                document.getElementById('modalTitleBulk').innerText = "⚡ Bulk Edit Staff";

                studentFields.classList.add('hidden');
                staffPermissions.classList.remove('hidden');
                staffCourses.classList.remove('hidden');
            }

            // 5. فتح المودال
            let ids = usersData.map(u => u.id);
            document.getElementById('bulkSelectedIds').value = ids.join(',');
            document.getElementById('modalSelectedCount').innerText = ids.length;
            document.getElementById('bulkEditModal').classList.remove('hidden');
        }

        function closeBulkEditModal() {
            document.getElementById('bulkEditModal').classList.add('hidden');
        }

        function openBulkDeleteModal() {
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            let ids = [];
            let htmlList = '';

            // تجميع البيانات
            checkboxes.forEach(cb => {
                ids.push(cb.value);
                let name = cb.getAttribute('data-name');
                let email = cb.getAttribute('data-email');
                let role = cb.getAttribute('data-role');

                // تصميم الكارت الصغير جوه قائمة الحذف
                htmlList += `
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-[10px] font-bold">
                                    ${name.charAt(0)}
                                </div>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-gray-800 truncate w-32">${name}</p>
                                    <p class="text-[9px] text-gray-500 truncate">${email}</p>
                                </div>
                            </div>
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-gray-200 text-gray-600">${role}</span>
                        </div>
                    `;
            });

            // ملء المودال بالبيانات
            document.getElementById('bulkDeleteInput').value = ids.join(',');
            document.getElementById('deleteUsersList').innerHTML = htmlList;

            // إظهار المودال
            document.getElementById('bulkDeleteModal').classList.remove('hidden');
        }

        function clearSelection() {
            document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateBulkAction();
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c7c7c7;
            border-radius: 2px;
        }
    </style>
@endsection